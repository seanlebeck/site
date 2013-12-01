<?php 

require_once("admin.inc.php");

require_once("aauth.inc.php");

include_once("aheader.inc.php");

$result = mysql_query("SELECT DISTINCT email FROM $t_ads WHERE email != '' AND newsletter = '1'");

$numrows = mysql_num_rows($result);

while( $row = mysql_fetch_assoc($result) )
	{
		$email = $row['email'];
		$newres = mysql_query("SELECT adid, cityid, code FROM $t_ads WHERE email='$email'");
			
			while( $newrow = mysql_fetch_assoc($newres) )
			{
			$codemd5 = md5($newrow['code']);
				
						}

echo $row['email']; ?> <br> <?php
}

?>