<?php

$auth = 0;

// Signout
if ($_GET['do'] == "signout")
{
	// Clear cookies
	setcookie($ck_edit_adid, "", 0, "/");
	setcookie($ck_edit_isevent, "", 0, "/");
	setcookie($ck_edit_codemd5, "", 0, "/");

	header("Location: $script_url/?view=main&cityid=$xcityid");
	exit;
}


// Login
if ($_GET['adid'] && $_GET['codemd5'])
{
	if($_GET['isevent'])
	{
		$table = $t_events;
		$view = "showevent";
	}
	else
	{
		$table = $t_ads;
		$view = "showad";
		$_GET['isevent'] = 0;
	}
	
	$sql = "SELECT adid FROM $table WHERE adid = $_GET[adid] AND MD5(code) = '{$_GET['codemd5']}'";
	list($adid) = @mysql_fetch_row(mysql_query($sql));

	if ($adid)
	{
		// Set cookies
		setcookie($ck_edit_adid, $adid, 0, "/");
		setcookie($ck_edit_isevent, $_GET['isevent'], 0, "/");
		setcookie($ck_edit_codemd5, $_GET['codemd5'], 0, "/");
		header("Location: $script_url/?view=edit&target=$_GET[target]&cityid=$xcityid");
		
		exit;
	}

}

// Validate user
if ($_COOKIE[$ck_edit_adid] && $_COOKIE[$ck_edit_codemd5])
{
	if($_COOKIE[$ck_edit_isevent])
	{
		$table = $t_events;
		$view = "showevent";
	}
	else
	{
		$table = $t_ads;
		$view = "showad";
	}

	$sql = "SELECT COUNT(*) FROM $table WHERE adid = $_COOKIE[$ck_edit_adid] AND MD5(code) = '$_COOKIE[$ck_edit_codemd5]'";
	list($auth) = @mysql_fetch_row(mysql_query($sql));

	$adid = $_COOKIE[$ck_edit_adid];
	$isevent = 0+$_COOKIE[$ck_edit_isevent];
	$adtype = ($isevent ? "E" : "A");
	$codemd5 = $_COOKIE[$ck_edit_codemd5];

   
	$sql = "SELECT *, UNIX_TIMESTAMP(createdon) AS createdon_ts, 
	            UNIX_TIMESTAMP(expireson) AS expireson_ts 
	        FROM $table WHERE adid = $adid";
	$ad = mysql_fetch_array(mysql_query($sql));
   	
}

?>