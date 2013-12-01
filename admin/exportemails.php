<?php

require_once("../config.inc.php");

$emails[] = array();
$cnt = 0;

$sql = "SELECT DISTINCT email FROM $t_ads";
$res = mysql_query($sql);

while ($row=mysql_fetch_array($res))
{
	if ($row['email'])
	{
		$emails[$row['email']] = $cnt;
		$cnt++;
	}
}

$sql = "SELECT DISTINCT email FROM $t_events";
$res = mysql_query($sql);

while ($row=mysql_fetch_array($res))
{
	if ($row['email'])
	{
		$emails[$row['email']] = $cnt;
		$cnt++;
	}
}

$sql = "SELECT DISTINCT posteremail FROM $t_imgs";
$res = mysql_query($sql);

while ($row=mysql_fetch_array($res))
{
	if ($row['email'])
	{
		$emails[$row['posteremail']] = $cnt;
		$cnt++;
	}
}


header("Content-type: text/plain");
foreach ($emails as $k=>$v)
{
	echo $k."\n";
}

?>