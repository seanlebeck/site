<?php



require_once("../initvars.inc.php");
$path_escape = "../";
if (!defined('CONFIG_LOADED'))
{
	require_once("{$path_escape}config.inc.php");
	$cleanup_logfile = "{$path_escape}log/cleanup.txt";
}
else
{
	$cleanup_logfile = "{$path_escape}log/cleanup.txt";
}

ob_start();

// Get last run time
$fp = @fopen($cleanup_logfile, "r");
if ($fp)
{
	$lastrun = 0+trim(fgets($fp, 1024));
	fclose($fp);
}
else
{
	$lastrun = 0;
}


// Cleanup if last run was before 23hrs
if ($lastrun < time()-23*60*60)
{
	// Log header
	$cleanup_start_time = time();
	echo $cleanup_start_time . "\r\n";
	echo "\r\n";
	echo "Time: " . gmdate("r", $cleanup_start_time) . "\r\n";
	echo "Last run: " . ($lastrun ? gmdate("r", $lastrun) : "-") . "\r\n";
	echo "\r\n";


	// Ads
	$sql = "SELECT adid FROM $t_ads WHERE expireson < NOW()";
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res))
	{
		$adid = $row['adid'];

		echo "$t_adxfields: ";
		$sql = "DELETE FROM $t_adxfields WHERE adid = $adid";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";

		$sql = "SELECT picfile FROM $t_adpics WHERE adid = $adid AND isevent = '0'";
		$pres = mysql_query($sql);
		while($p=mysql_fetch_array($pres))
		{
			unlink("{$path_escape}{$datadir[adpics]}/$p[picfile]");
		}

		echo "$t_adpics (A): ";
		$sql = "DELETE FROM $t_adpics WHERE adid = $adid AND isevent = '0'";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";

		echo "$t_featured (A): ";
		$sql = "DELETE FROM $t_featured WHERE adid = $adid AND adtype = 'A'";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";

		echo "$t_promos_featured (A): ";
		$sql = "DELETE FROM $t_promos_featured WHERE adid = $adid AND adtype = 'A'";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";

		echo "$t_promos_extended (A): ";
		$sql = "DELETE FROM $t_promos_extended WHERE adid = $adid AND adtype = 'A'";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";
	
	}

	echo "$t_ads: ";
	$sql = "DELETE FROM $t_ads WHERE expireson < NOW()";
	if(mysql_query($sql)) echo mysql_affected_rows();
	else echo $sql." ".mysql_error();
	echo "\r\n";


	// Events
	$sql = "SELECT adid FROM $t_events WHERE expireson < NOW()";
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res))
	{
		$adid = $row['adid'];

		$sql = "SELECT picfile FROM $t_adpics WHERE adid = $adid AND isevent = '1'";
		$pres = mysql_query($sql);
		while($p=mysql_fetch_array($pres))
		{
			unlink("{$path_escape}{$datadir[adpics]}/$p[picfile]");
		}

		echo "$t_adpics (E): ";
		$sql = "DELETE FROM $t_adpics WHERE adid = $adid AND isevent = '1'";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";
		
		echo "$t_featured (E): ";
		$sql = "DELETE FROM $t_featured WHERE adid = $adid AND adtype = 'E'";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";

		echo "$t_promos_featured (E): ";
		$sql = "DELETE FROM $t_promos_featured WHERE adid = $adid AND adtype = 'E'";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";

		echo "$t_promos_extended (E): ";
		$sql = "DELETE FROM $t_promos_extended WHERE adid = $adid AND adtype = 'E'";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";

	}

	echo "$t_events: ";
	$sql = "DELETE FROM $t_events WHERE expireson < NOW()";
	if(mysql_query($sql)) echo mysql_affected_rows();
	else echo $sql." ".mysql_error();
	echo "\r\n";


	// Images
	$sql = "SELECT imgid, imgfilename FROM $t_imgs WHERE expireson < NOW()";
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res))
	{
		$imgid = $row['imgid'];

		unlink("{$path_escape}userimgs/$row[imgfilename]");

		echo "$t_imgcomments: ";
		$sql = "DELETE FROM $t_imgcomments WHERE imgid = $imgid";
		if(mysql_query($sql)) echo mysql_affected_rows();
		else echo $sql." ".mysql_error();
		echo "\r\n";
		
	}
	
	echo "$t_imgs: ";
	$sql = "DELETE FROM $t_imgs WHERE expireson < NOW()";
	if(mysql_query($sql)) echo mysql_affected_rows();
	else echo $sql." ".mysql_error();
	echo "\r\n";


	$op = ob_get_contents();
	$fp = @fopen($cleanup_logfile, "w");
	@fwrite($fp, $op);
	fclose($fp);

}

unset($fp);

ob_clean();

?>