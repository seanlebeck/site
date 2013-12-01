<?php
$mysql_hostname = "localhost";
$mysql_user = "dojoep_dojoep";
$mysql_password = "5s[NqPUt[AR9";
$mysql_database = "dojoep_messaging_db";
$mysql_database2 = 'dojoep_vermont.vivaru_ads';
$prefix = "";
$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Opps some thing went wrong");
mysql_select_db($mysql_database, $bd) or die("Opps some thing went wrong");
$datadir['adpics'] = "adpics";
?>

