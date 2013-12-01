<?php


ob_start();
session_start();

// Include required files.
require "includes/config.php";
require "includes/database.php";
require "includes/src/facebook.php";
require "includes/language.php";

$ck_username = 'username';
$ck_session = 'session';
$ck_userid = 'userid';

$cookie_path = '/';
//Cookie Domain/ Leave out the http://www and start with a dot.
$cookie_domain = '.shufflebuy.com';

// init database object
$db = new database();
$db->connect();

// Create Facebook connect object
$facebook = new Facebook(array(
    'appId'  => $fb_app_api,
    'secret' => $fb_app_secret,
    'cookie' => true
));
// Get User ID
$user = $facebook->getUser();
// If a session exists, proceed to check details against database
if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }

    
	// check to see if user is in the database
	
	$where = "oauth_uid = '{$user}'";

	$db->select('*','vivaru_acc_users',$where);
	$rows = $db->getRows();
	$result = $db->getResult();

	if($rows == 0) {
	
		if ( $username_login_only == 1 )
		{
			$u_not_empty = (!empty($username)) ? '1' : '0';
			$u_success = ($username == $row['username']) ? '1' : '0';
			$u_sql = "username = '$username'";
		}
		else
		{
			$u_not_empty = (!empty($update_email)) ? '1' : '0';
			$u_success = ($update_email == $row['email']) ? '1' : '0';
			$u_sql = "email = '$update_email'";
		}
		// user not in db, insert details	
		if(!empty($user)){
	        
				$user_ip = $_SERVER['REMOTE_ADDR'];
			// insert details
			$iString = "oauth_provider, oauth_uid, username, email, joined, last_login, user_ip, active";
			$iArray = array();
			array_push($iArray,'facebook');
			array_push($iArray,$user);
			array_push($iArray,$user_profile['name']);
			array_push($iArray,$user_profile['email']);
			array_push($iArray,time());
			array_push($iArray,time());
			array_push($iArray,$user_ip);
			array_push($iArray,"1");
			$db->insert('vivaru_acc_users',$iArray,$iString);
			$where = "oauth_uid = '{$user}'";
			$db->select('*','vivaru_acc_users',$where);
			$result = $db->getResult();					
                  // create profile page
				 // echo "user : ",$result['username'];
                  //createProfile($result['username']);

			// set session vars
			validate_user($result['username'],$result['userType']);
			
			$target_id = $user;
			$title = FACEBOOK_POST_TITLE;
			$name = FACEBOOK_POST_NAME;
			$caption = FACEBOOK_POST_CAPTION;
			$description = FACEBOOK_POST_DESCRIPTION;
			$image = FACEBOOK_POST_IMAGE;
			$url = FACEBOOK_POST_URL;

			try {
			$facebook->api('/'.$target_id.'/feed', 'post', array(
			'message' => $title,
			'name' => $name,
			'description' => $description,
			'caption' => $caption,
			'picture' => $image,
			'link' => $url
			));
			}
			catch (Exception $e){ echo $output = '<li>'.$e.'</li>'; }

			// divert to profile
			
		}

        } else {
			validate_user($user['name'],$result['userType']);
                    // user exists, redirect to profile page
                      //header('location: ../account.html');

        }
		//	$where = "oauth_uid = '{$uid}'";
		//	$db->select('*','vivaru_acc_users',$where);
		//	$result = $db->getResult();	
			print_r($user);
			$sql = "SELECT user_id, username, email, password, active, user_ip FROM vivaru_acc_users 
			WHERE oauth_uid = '".$user."' LIMIT 1";
			
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result);
			
			$sql = "SELECT user_id, username, email, password, active, user_ip FROM vivaru_acc_users 
			WHERE oauth_uid = '".$user."' LIMIT 1";
			
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result);
			$md5_password = md5($password . SALT);
			$cookie_expire_time = 86400;
			
			setcookie($ck_username, $row['username'], time() + $cookie_expire_time, $cookie_path, $cookie_domain);
			setcookie($ck_session, $md5_password, time() + $cookie_expire_time, $cookie_path, $cookie_domain);
			setcookie($ck_userid, $row['user_id'], time() + $cookie_expire_time, $cookie_path, $cookie_domain);


	
			
			$update_empty_ip = ($row['user_ip'] <= 0) ? ", user_ip = '$user_ip'" : "";
			
			$sql = "UPDATE vivaru_acc_users SET last_login = '".time()."' $update_empty_ip WHERE user_id ='".$row['user_id']."'";
	        mysql_query($sql);
			header('location: ../account.html');

}
ob_flush();
header('location: ../login.html');
?>