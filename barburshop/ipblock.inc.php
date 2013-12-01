<?php




if(!defined('CONFIG_LOADED'))
{
	die("&laquo;");
}



function encodeIP($ip)
{
	preg_match("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/U", $ip, $ipp);
	$ipval = $ipp[4] + $ipp[3]*256 + $ipp[2]*256*256 + $ipp[1]*256*256*256;
	return  $ipval;
}

$ip = $_SERVER['REMOTE_ADDR'];
$ipval = encodeIP($ip);


$sql = "SELECT ipid FROM $t_ipblock WHERE ipstart <= $ipval && ipend >= $ipval";

$ipres = mysql_query($sql);

if (@mysql_num_rows($ipres))
{
	list($ipid) = @mysql_fetch_array($ipres);
	$sql = "UPDATE $t_ipblock SET blocks=blocks+1 WHERE ipid='$ipid'";
	mysql_query($sql);

	 echo $lang['USER_BLOCKED'];

	die;
}



?>