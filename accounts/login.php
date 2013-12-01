<br>

<?php

ob_start();

define('IN_SCRIPT', true); 

include 'includes/config.php';

if ( $acc_construction )
{
	general_message($lang['ACC_CONSTRUCTION'], $lang['ACC_CONSTRUCTION_MSG'] . $go_back);
	exit;
}



if( !empty($_COOKIE[$ck_username]) && !empty($_COOKIE[$ck_session]) && !empty($_COOKIE[$ck_userid]) )
{
	header("Location: $acc_panel_link");
	exit;
}


$user_ip = $_SERVER['REMOTE_ADDR'];

if ( $username_login_only == 1 )
{
	$u_row = $lang['ACC_USERNAME'];
	$u_field = '<input type="text" class="post" name="username" size="25" maxlength="30" value="'.$_POST['username'].'">';		
}
else
{
	$u_row = $lang['ACC_EMAIL'];
	$u_field = '<input type="text" class="post" name="update_email" size="25" maxlength="30" value="'.$_POST['update_email'].'">';
}



if ( $submit )
{
	
	
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

	// BEGIN ERROR CHECKS
	$pro_pass = md5($password . SALT);
	$sql = "SELECT user_id, username, email, password, active, user_ip FROM $t_users 
			WHERE $u_sql AND password = '$pro_pass' LIMIT 1";
			
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);


	if ( empty($username) && $username_login_only == 1 )
	{ 
         $error_message .= $lang['ACC_ERROR_USERNAME']; 
	} 
	if ( empty($update_email) && !$username_login_only )
	{ 
         $error_message .= $lang['ACC_ERROR_EMAIL_VALID']; 
	} 
	if ( empty($password) )
	{ 
	     $error_message .= $lang['ACC_ERROR_PASS']; 
	}
	if ( $row['active'] == 0 && $u_success == 1 && $pro_pass == $row['password'] )
	{ 
	     $error_message .= $lang['ACC_ERROR_NOT_ACTIVE']; 
	}
	if ( $u_not_empty == 1 && !empty($password) && mysql_num_rows($result) <= 0 )
	{ 
	     $error_message .= $lang['ACC_ERROR_LOGIN']; 
	}
	
	if ( $error_message )
	{
		general_message($lang['ACC_ERROR'], $error_message);
	}
	else
	{   
		if ( $submit && $error != TRUE ) 
		{
			
			$md5_password = md5($password . SALT);
			
			if ( $savecookies == 1 )
			{
				setcookie($ck_username, $row['username'], time() + $cookie_expire_time, $cookie_path, $cookie_domain);
				setcookie($ck_session, $md5_password, time() + $cookie_expire_time, $cookie_path, $cookie_domain);
				setcookie($ck_userid, $row['user_id'], time() + $cookie_expire_time, $cookie_path, $cookie_domain);
			}
			else
			{
				setcookie($ck_username, $row['username'], 0);
				setcookie($ck_session, $md5_password, 0);
				setcookie($ck_userid, $row['user_id'], 0);
			}
	
			
			$update_empty_ip = ($row['user_ip'] <= 0) ? ", user_ip = '$user_ip'" : "";
			
			$sql = "UPDATE " . $t_users . " SET last_login = '".time()."' $update_empty_ip WHERE user_id ='".$row['user_id']."'";
	        mysql_query($sql);
			header("Location: $acc_panel_link");
			exit;
		}
	}

}

?>

<table width="100%" cellspacing="10" style="border:2px outset orangered;background:Azure;border-radius:5px;">

<td width="50%">

<form action="<?= $_SERVER["REQUEST_URI"] ?>" method="post">
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="2">
  <tr>
    <td height="45" colspan="2" valign="top"><b><?= $lang['ACC_LOGIN'] ?></b></td>
  </tr> 
  <tr>
    <td width="80" style="padding-bottom:16px"><?= $u_row ?>: </td>
    <td><?= $u_field ?></td>
  </tr>
  <tr>
    <td style="padding-bottom:6px"><?= $lang['ACC_PASSWORD'] ?>: </td>
    <td><input type="password" class="post" name="password" size="25" maxlength="30" value="" >
<input type="hidden" name="savecookies" value="1">


</td>
  </tr>
  <tr>
  <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="middle"><button type="submit" name="submit" value="Submit"><?= $lang['ACC_SUBMIT'] ?></button></td>
	<td align="centr" valign="middle">
	<fb:login-button size="medium" perms="email" onlogin="window.location='accounts/fbReg.php';"></fb:login-button>

<div id="fb-root"></div>

<script type="text/javascript">
    FB.init({appId: '<?php echo $fb_app_api; ?>', status: true,
        cookie: true, xfbml: true});
</script>
	</td>
    </tr>
</table>


<br>
</form>

</td>

<td width="50%" style="padding:10px;" valign="top">
<div>
<br>
<?php echo $lang['NO_ACCAUNT']; ?>
<br>
<center>
<a href="<?= $acc_signup_link ?>"><img src="images/register_now_button.png" border="0"></a>
<br><br><br>
</center>
</div>
</td>

</tr>
</table>


<?php

ob_end_flush();

?>