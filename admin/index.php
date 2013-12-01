<?php

require_once("admin.inc.php");


if ($_POST['admin_pass'] && $_POST['admin_pass'] == $admin_pass)
// Login admin
{
   
    if ($strict_login)
    {
    	invalidateSession();
    	session_start();
    	session_regenerate_id();
    }
   

	setcookie($ck_admin, encryptForCookie($admin_pass, "admin", true), 0, "/");
	header("Location: home.php");
	exit;

}
elseif ($_GET['signout'])
// Signout admin
{
	setcookie($ck_admin, "", 0, "/");
	clearSalt("admin");
			
	
	if ($strict_login)
	{
	    invalidateSession();
	}


	header("Location: index.php");
	exit;
}
elseif (isAdmin())
// Already logged in. Redirect to admin home page
{
	header("Location: home.php");
	exit;
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 TRANSITIONAL//EN">
<html>
<head>
<title><?php echo $app_fullname; ?> Admin</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langx['charset']; ?>">
<?php echo '<div style="display:none;">#&88;#&90;#&101;#&114;#&111;".
"#&83;#&99;#&114;#&105;#&112;#&116;#&115;#&46;#&99;#&111;#&109;</div>'; ?>
<link rel="stylesheet" type="text/css" href="astyle.default.css">
<link rel="stylesheet" type="text/css" href="apager.css">
</head>

<body id="loginpage">
<br><br><br><br><br><br><br><br><br>
<table align="center" ><tr><td align="center">
<div id="logo"><h2><?php echo $app_fullname; ?> Admin</h2></div><br>
<form name="frmAdminLogin" action="" method="post" id="loginform" style="color:gray;border:10px solid white;border:10px solid rgba(255, 255, 255, 0.498);-moz-border-radius:8px;-webkit-border-radius:8px;border-radius:8px;">
<p><b>Username</b> <br>
  <br>
  <input type="text" name="admin_user" size="25" style="border:1px solid darkred;color:gray;font-size:12px;padding:3px;"  <?php if($demo) { echo "value=\"admin\""; } ?>>
  <br>
</p>
<p><b>Password</b> <br><br>
  <input type="password" name="admin_pass" size="25" style="border:1px solid darkred;color:gray;font-size:12px;padding:3px;" <?php if($demo) { echo "value=\"$admin_pass\""; } ?>><br><br>
</p>
<button type="submit" style="border:1px solid gray;background:aliceblue;color:darkred;padding:3px;cursor:pointer;">Login</button>
</form>
<br><br>


<?php /*
<select name="theme" onchange="if(this.value)location.href='home.php?theme='+this.value;">
<option value="">- Theme -</option>
<option value="blue">Blue</option>
<option value="cream">Cream</option>
</select>
*/ ?>


</td></tr></table>
</body>

</html>