<?php

ob_start();

define('IN_SCRIPT', true); 

// Extra security - do not remove
if ( empty($_GET['view']) && ( !$_POST || !$_GET ) )
{
	header("Location: ./"); 
	exit;
}

if ( $acc_construction )
{
	general_message($lang['ACC_CONSTRUCTION'], $lang['ACC_CONSTRUCTION_MSG'] . $go_back);
	exit;
}


if ( !empty($_COOKIE[$ck_username]) && !empty($_COOKIE[$ck_session]) && !empty($_COOKIE[$ck_userid]) )
{
	header("Location: $acc_panel_link");
	exit;
}

if ( $image_verification ) 
{
	require_once("{$path_escape}captcha.cls.php");
	$captcha = new captcha();
}


if ($xview == 'forgot')
{	

	session_start();
	
	
	if ( $forgot_login )
	{
	    
		if ( empty($username) )
		{
			$forgot_query = "email ='$email'";
		}
		elseif ( empty($password) )
		{
			$forgot_query = "username ='$username'";
		}
		else
		{
			$forgot_query = "username ='$username' AND email ='$email'";
		}
		
		// this if statment is only to not query empty results and added security using eregi function
		if ( !empty($username) || !empty($email) )
		{
			if ( !ereg('[^A-Za-z0-9]', $username) && $only_num_letters == 1 )
			{
				$sql = "SELECT user_id, username, password, email, joined FROM ". $t_users ." WHERE $forgot_query LIMIT 1";
				$result = mysql_query($sql);
				$row = mysql_fetch_array($result);
				$forgot_total = mysql_num_rows($result);
			}
		}

	
		// BEGIN ERROR CHECKS
		if ( empty($username) && empty($email) )
		{
			 $error_message = $lang['ACC_ERROR_BOTH'];
			 $error = TRUE;
		}
		elseif( ereg('[^A-Za-z0-9]', $username) && $only_num_letters == 1 ) 
		{
			 $error_message = $lang['ACC_ERROR_NUMS']; 
	         $error = TRUE;
		}
		elseif($image_verification && !$captcha->verify($_POST['captcha']))
		{
			 $error_message .= "{$lang['ERROR_IMAGE_VERIFICATION_FAILED']}<br>";
			 $error = TRUE;
		}	
		elseif ( $forgot_total <= 0 )
		{ 
		     $error_message = $lang['ACC_ERROR_RECS']; 
	         $error = TRUE;
		}
		// END ERROR CHECKS	
		
		if ( $error == 1 )
		{
			general_message($lang['ACC_ERROR'], $error_message);
		}
		
		
		if ( $forgot_login && $error != TRUE ) 
		{
			$user_ip = $_SERVER['REMOTE_ADDR'];
			$time_now = date('m-d-Y g:i A', time());
			
			$set_forgot_login = "UPDATE " .$t_users. " SET forgot_login = '1' WHERE $forgot_query LIMIT 1";
			mysql_query($set_forgot_login);
			
			$forgot_link = $script_url .'/index.php?view=forgot&action=forgot_finish&confirm=' . $row['password'] . '&conf_join=' . $row['joined'];
			
			// BEGIN AUTOMATED EMAIL
			$to = $row['email'];
			$subject = $lang['ACC_MAIL_SUB'];
			
			$message = "Hello {$row['username']},\n\nYou or someone else has used this email to generate a new password for your account.\n";
			$message .= "\nIf you wish to reset your login info please click on the following link: \n\n$forgot_link \n\n\nOnce you have logged into your account you may edit your profile settings if you would like to change your password.\n\nThanks!\n\nNew login request sent by: \n ------------------------ \nIP: {$user_ip} \nDate: $time_now";
			$mailheaders = "Return-path: $main_acc_email\n"; 
			$mailheaders .= "From: $main_acc_email\n"; 
			$mailheaders .= "Reply-To: $main_acc_email\n"; 
			
			mail($to, $subject, $message, $mailheaders);
			// END AUTOMATED EMAIL
			
			if ( empty($email) )
			{
				$show_email_text = $lang['ACC_MAIL_SHOW'];
			}
			else
			{
				$show_email_text = "($to)";
			}
			
			general_message($lang['ACC_DISP_ONE'], "Your new password has been emailed to $show_email_text <br><br>Your IP Address ($user_ip) has been recorded for security purposes.");
			header("Refresh: 15; url=$acc_login_link"); 
			
			session_destroy();

		}	
	}


}	


if ($action == 'forgot_finish')
{
		
	  $finish_query = "WHERE password= '$confirm' AND joined='$conf_join'";
	  $sql = "SELECT username, password, joined, forgot_login FROM " .$t_users. " $finish_query LIMIT 1";
	  $result = mysql_query($sql);
	  $row = mysql_fetch_array($result);
	  
	  if ( mysql_num_rows($result) <= 0)
	  {
	  	   general_message($lang['ACC_ERROR'], $lang['ACC_ERROR_RECS']);
		   header("Refresh: 10; url=$acc_login_link"); 
	  }
	  elseif ( $row['forgot_login'] == 0 )
	  {
	       general_message($lang['ACC_ERROR'], $lang['ACC_REC_PASS']);
		   header("Refresh: 25; url=$acc_login_link"); 
	  }
	  else
	  {
		  $new_rand_pass = substr(md5(rand(0,9999999)), 0, 6);
		  define('NEW_PASS', $new_rand_pass);
		  $update_pass = md5(NEW_PASS . SALT);
		  $sql = "UPDATE " .$t_users. " SET password = '$update_pass', forgot_login = NULL $finish_query LIMIT 1";
		  
		  mysql_query($sql) or die($lang['ACC_PROBLEM_UP']);
		  
		  general_message($lang['ACC_REQ_SUCCESS'], "
		    <table width=\"50%\" border=\"0\">
		  	  <tr>
				<td>{$lang['ACC_YOUR_USER']}:</td>
				<td>" . $row['username'] . "</td>
			  </tr>
			  <tr>
				<td>{$lang['ACC_YOUR_PASS']}:</td>
				<td>" . NEW_PASS . "</td>
			  </tr>
			</table>
		  <br><br>{$lang['ACC_SAVE_IMP']}
		  <br><br><a href=\"" . $acc_login_link . "\">{$lang['ACC_CLICK_LOGIN']}</a><br>");
	  }

}


ob_end_flush();

if ( $action != 'forgot_finish')
{

?>

<form action="<?= $_SERVER["REQUEST_URI"] ?>"  method="post">
<table width="65%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr>
    <th colspan="2" valign="middle"><?= $lang['ACC_FORGOT_LOGIN'] ?></th>
  </tr>
  <tr>
    <td colspan="2"><?= $lang['ACC_RESET_PASS'] ?></td>
  </tr>
  <tr>
    <td width="140"><?= $lang['ACC_USERNAME'] ?>:</td>
    <td><input type="text" style="width:250px" name="username" size="25" maxlength="30" value="<?= $_POST['username'] ?>" ></td>
  </tr>
  <tr>
    <td><?= $lang['ACC_EMAIL'] ?>:</td>
    <td><input type="text" style="width:250px" name="email" size="25" maxlength="150" value="" ></td>
  </tr>
	<?php if($image_verification) { ?>
    <tr>
      <td height="50" colspan="2" align="center">
        <img src="<?= $back_path ?>captcha.png.php?<?php echo rand(0,999); ?>"></td>
    </tr>
    <tr>
      <td><?= $lang['POST_VERIFY_IMAGE'] ?>:
      </td>
      <td><span class="hint"><?= $lang['ACC_CODE'] ?></span><br><input type="text" style="width: 250px" id="captcha" name="captcha" size="6" maxlength="6" value="" /></td>
    </tr>
	<?php } ?>
  <tr>
    <td colspan="2"><button type="submit" name="forgot_login" value="Submit"><?= $lang['ACC_SUBMIT'] ?></button></td>
  </tr>
</table>
</form>
<br>
	
<?php

}

?>