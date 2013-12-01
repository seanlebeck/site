<?php

session_start();

define('IN_SCRIPT', true); 

include 'includes/config.php';

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




if ( !empty($_COOKIE[$ck_username]) || !empty($_COOKIE[$ck_session]) || !empty($_COOKIE[$ck_userid]) )
{
	header("Location: $acc_login_link");
	exit;
}
	


$user_ip = $_SERVER['REMOTE_ADDR'];


if ( $submit )
{
	
	$success = '';
	
	// BEGIN ERROR CHECKS
	if ( strlen($username) <= 2 )
    { 
		$error_message .= $lang['ACC_ERROR_USER_MORE']; 
	}
	if ( (!ereg(".+\@.+\..+", $email)) || (!ereg("^[a-zA-Z0-9_@.-]+$", $email)) )
	{ 
		$error_message .= $lang['ACC_ERROR_EMAIL_VALID']; 
	}
	$sql = "SELECT email FROM " . $t_users . " WHERE email = '$email'";
	$result = query_db($sql);
	if ( mysql_num_rows($result) ) 
	{
		$email_error_msg = str_replace('%%EMAIL%%', $email, $lang['ACC_ERROR_EMAIL_USE']);
		$error_message .= $email_error_msg; 
	}
	$sql2 = "SELECT username FROM " . $t_users . " WHERE username = '$username'";
	$result2 = query_db($sql2);
	if ( mysql_num_rows($result2) ) 
	{
		$user_error_msg = str_replace('%%USERNAME%%', $username, $lang['ACC_ERROR_USER_USE']);
		$error_message .= $user_error_msg; 
	}
	if ( ereg('[^A-Za-z0-9_ -]', $username) && $only_num_letters == 1 ) 
	{
		$error_message .= $lang['ACC_ERROR_NUMS']; 
	}
	if ( strlen($password) <= 2 )
	{ 
	    $error_message .= $lang['ACC_ERROR_PASS_MORE']; 
	}
	if ( $password != $password_confirm )
	{ 
	    $error_message .= $lang['ACC_ERROR_PASS_MATCH']; 
	}
	if ( $username == $password && !empty($username) )
    { 
		$error_message .= $lang['ACC_ERROR_IDENTICAL']; 
	}
	
	// END ERROR CHECKS
	
	if ( $error_message )
	{
		

?>

<center>
<div style="text-align:left;">

<?php


		general_message($lang['ACC_ERROR'], $error_message);

?>

</div>
</center>

<?php


	}
	else
	{
		
		$md5_password = md5($password . SALT);
		$join_date = time();
		$user_ip = $_SERVER['REMOTE_ADDR'];
		if ( $newsletter == 1 )
		{
			// if user subscribed to newsletter, add these fields in insert.  Otherwise leave blank for NULL value.
			$news_ltr_option = "newsletter,";
			$news_ltr_value = "'1',";
		}
		
		if ( !empty($how_found) )
		{
			$how_option = "how_found,";
		    $how_value = "'$how_found',";	
		}
		
		$sql = "INSERT INTO " . $t_users . " 
				( 
				 username, 
				 password, 
				 email, 
				 joined,
				 $news_ltr_option
				 $how_option
				 user_ip
				) 
				VALUES 
				(
				 '$username',
				 '$md5_password',
				 '$email',
				 '$join_date',
				 $news_ltr_value
				 $how_value
				 '$user_ip'
			    )";
		query_db($sql);
		
		unset($_SESSION['captcha']);
		
		
		
		if ($user_email_activation == 1)
		{
			
			$verify_link = $script_url . '/index.php?view=signup&action=email_activation&confirm=' . $md5_password . '&conf_join=' . $join_date;
			
			$to = $email;
			$subject = $lang['ACC_VER_SUB'];
			
			$email_array = array('%%USERNAME%%', '%%VERIFY%%', '%%PASSWORD%%');
			$email_replace = array($username, $verify_link, $password);
			$user_email_msg = str_replace($email_array, $email_replace, $lang['ACC_SIGNUP_EMAIL']);

			
			$message = $user_email_msg;
			$mailheaders  = "Content-Type: text/html\n";
			$mailheaders .= "Return-path: $main_acc_email\n"; 
			$mailheaders .= "From: $main_acc_email\n"; 
			$mailheaders .= "Reply-To: $main_acc_email\n"; 
			
			
			mail($to, $subject, $message, $mailheaders);
			
			$to_email_msg = str_replace('%%TO%%', $to, $lang['ACC_EMAIL_CUR_INACTIVE']);
			
			general_message($lang['ACC_EMAIL_DISPATCH'], $to_email_msg);	
			$success = 1;
		}
		else
		{
			general_message($lang['ACC_SIGNUP_SUCCESS'], $lang['ACC_PENDING_ADMIN']);
			?><center><a href='account.html'><b>Click here to login to your account</b></a><br><br></center><?php
		}

		
	}
}

switch ($action) 
{
case 'email_activation':

	
	$sql = "SELECT user_id, username, password, active FROM " . $t_users . " 
			WHERE password = '$confirm'
			AND joined = '$conf_join'
			LIMIT 1";
	$result = query_db($sql);
	$row = mysql_fetch_array($result);
	

	if ( $row['active'] == 0 && mysql_num_rows($result) > 0 )
	{
		$sql = "UPDATE " . $t_users . " SET active ='1' WHERE user_id = '".$row['user_id']."' LIMIT 1";
		$result = query_db($sql);
		general_message($lang['ACC_SIGNUP_SUCCESS'], $lang['ACC_THANKS_JOIN'] ."<br><br><a href=\"".$script_url."/".$acc_login_link."\">{$lang['ACC_CLICK_LOGIN']}</a><br>");
	}
	elseif ( $row['active'] == 1 && mysql_num_rows($result) > 0 )
	{
		general_message($lang['ACC_ERROR'], $lang['ACC_ACTIVATED']);
	}
	else
	{
		general_message($lang['ACC_ERROR'], $lang['ACC_EXPIRED']);
	}
	

break;

default:
		
if ( $action != 'email_activation' && $success != 1 )
{
?>

<center>
<table width="75%" style="padding:10px;border:2px outset orangered;background:Azure;border-radius:5px;">
<tr><td>

<form action="<?= $_SERVER["REQUEST_URI"] ?>"  method="post">
  <table width="75%" border="0" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <th colspan="2" height="25" valign="middle"><?= $lang['ACC_REGISTER'] ?>. For quick access click Facebook button.</th>
    </tr>
    <tr>
      <td width="38%"><?= $lang['ACC_USERNAME'] ?>: <span class="marker">*</span></td>
      <td><input type="text" style="width:250px" name="username" size="25" maxlength="30" value="<?= $_POST['username']; ?>" /></td>
    </tr>
    <tr>
      <td><?= $lang['ACC_EMAIL_TWO'] ?>: <span class="marker">*</span></td>
      <td><input type="text" style="width:250px" name="email" size="25" maxlength="150" value="<?= $_POST['email']; ?>" /></td>
    </tr>
    <tr>
      <td><?= $lang['ACC_PASSWORD'] ?>: <span class="marker">*</span></td>
      <td><input type="password" style="width: 250px" name="password" size="25" maxlength="20" value="" />
      </td>
    </tr>
    <tr>
      <td><?= $lang['ACC_PASSWORD_CONF'] ?>: <span class="marker">*</span></td>
      <td><input type="password" style="width: 250px" name="password_confirm" size="25" maxlength="30" value="" />
      </td>
    </tr>
	
    <tr>
      <td><?= $lang['ACC_HOW'] ?>:</td>
      <td class="row2 bigtext"><textarea name="how_found" cols="25" rows="2" id="how_found" style="width: 250px"><?= $_POST['how_found']; ?></textarea></td>
    </tr>
    <tr>
      <td align="center" valign="middle"><button type="submit" name="submit" value="Submit"><?= $lang['ACC_SUBMIT'] ?></button>
	  &nbsp;&nbsp;
	  <button type="reset" value="Reset" name="reset"><?= $lang['ACC_RESET'] ?></button>
</td>
		<td align="center" valign="middle">
        
		
		<fb:login-button size="medium" perms="email" onlogin="window.location='accounts/fbReg.php';"></fb:login-button>


<div id="fb-root"></div>

<script type="text/javascript">
FB.init({appId: '<?php echo $fb_app_api; ?>', status: true,
cookie: true, xfbml: true});
 
</script>

<?php ob_flush(); ?>
		
		</td>
    </tr>
  </table>
</form>

</td></tr>
</table>
</center>

<br />

<?php

}

break;

}

 
?>