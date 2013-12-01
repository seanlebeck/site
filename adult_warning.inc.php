<?php

require_once("initvars.inc.php");
require_once("config.inc.php");

ob_start();

echo $_SESSION['ses_verified'];
echo $_SESSION['start'];

$xcatid = intval($_GET['catid']);
$xsubcatid = intval($_GET['subcatid']);
$xcityid = intval($_GET['cityid']);

if ( !$xsubcatid )
{
	$sql = "SELECT alerttitle, alertdesc FROM $t_cats WHERE catid='$xcatid'";
	$location_url = "index.php?view=ads&catid=$xcatid&cityid=$xcityid";
}
else
{
	$sql = "SELECT alerttitle, alertdesc FROM $t_subcats WHERE subcatid='$xsubcatid'";
	$location_url = "index.php?view=ads&subcatid=$xsubcatid&cityid=$xcityid";
}

$res = mysql_query($sql) or die($sql.mysql_error());
list($alerttitle, $alertdesc) = mysql_fetch_array($res);

if ( $_POST['submit'] ) 
{
	if ( $alert_use_cookies )
	{
		setcookie("ck_adultverified", "Yes", 0, "/");
	}
	else
	{
		$_SESSION["adultverified"] = 1;
	}
	
	header("Location: $script_url/$location_url");
	exit;
}

ob_flush();
?>

<style>
/* Core Formatting */
html {height: 100%;margin-bottom: 1px;}
body {margin: 0;line-height: 135%;}
body { min-width:982px;}
body {padding:0;margin:0;font-family:arial,sans-serif;background:#ffffff;font-size:62.5%;}
form {margin: 0;padding: 0;}
body {font-size: 12px; text-align:center}
p {margin-top: 10px;margin-bottom: 15px;}
h1, h2, h3, h4, h5 {padding-bottom: 5px;margin: 25px 0 10px 0;font-weight: normal;line-height: 135%;}
h1 {font-size: 250%;}
h2 {font-size: 200%;}
h3 {font-size: 175%;}
h4 {font-size: 120%;line-height: 130%;}
h5 {font-size: 100%;}
a {text-decoration: none;}
a:hover {text-decoration: none;}
a {color: #9FB400;}
a { outline:0; /* remove dotted line */ }
.clr {clear:both;position:relative;font-size: 0;}
img {display:block;border:0}

#title {
	font-weight:bold;
	margin-top:50px;
}

#desc {
	margin-top:10px;
	margin-bottom:20px;
	width: 440px;
}

#img img {
	margin-top:20px;
	margin-bottom:20px;
}

#title, #desc, #img img, #buttons {
	text-align: center;
	margin-left: auto;
	margin-right: auto;
}
</style>

<html>
<head>
<title><?php echo $site_name . ' - ' . $alerttitle; ?></title>

<body>

<div id="title">
<?php echo $alerttitle; ?>
</div>

<div id="img">
	<img src="images/red-alert.jpg" alt=""  />
</div>

<div id="desc"><?php echo nl2br($alertdesc); ?></div>

<div id="buttons">
<form id="adult" name="adult" method="post" action="<?= $_SERVER["REQUEST_URI"] ?>">
<input type="submit" name="submit" id="submit" value="I Understand" />&nbsp;&nbsp;
<input type=button value="Go Back" onClick="history.go(-1)">&nbsp;</td>
</form>
</div>

</body>
</html>