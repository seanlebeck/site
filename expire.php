<?php

require_once("initvars.inc.php");

require_once("config.inc.php");

$expiry_date = (time()+$expire_ads_ahead);
$upcoming_date = date("l, j F, Y H:i", $expiry_date); 

$res_sql = "SELECT * FROM ".$t_ads." 
            WHERE UNIX_TIMESTAMP(expireson) <= '".$expiry_date."' 
			AND reminder = '0'";
			
$resc = mysql_query($res_sql);
 
while ( $row = mysql_fetch_assoc($resc) )
{
$codemd5 = $row["code"];
$codemd5 = md5($codemd5);

$subj = $lang['RENEW_EMAIL_SUBJ'] . ' (' . $row["adtitle"] . ')';
$msg = "Hello, this is a friendly reminder that your ad is about to expire on (".$upcoming_date.") at ".$site_name.". 

Please click on the following link to visit your ad's edit page if you wish to renew it: 

".$script_url."/?view=edit&isevent=&adid=".$row["adid"]."&codemd5=".$codemd5."&cityid=".$row["cityid"]."

On the edit page click on the \"Renew Ad\" located at the top to renew your listing.  You may also click on the \"Promote your ad\" to upgrade your current basic listing to a featured listing.  

Thank you,
Webmaster, ".$site_name."
";


$to = $row["email"];
//$to='noone@blah.com'; // testing
$from = $site_email;


mail($to, $subj, $msg, "From: $from\r\n");

echo $row["adid"].'  '.$row["createdon"].' '.$row["expireson"].' - email sent to '.$row["email"].'<br>'; // output

mysql_query("UPDATE ".$t_ads." SET reminder='1' WHERE adid='".$row["adid"]."'");
$i++;
}

	if ( $reminder_email_master == 1 )
	{
		if ( $i <= 0 )
		{
			$i = "No";
		}	
		$master_subject = "Reminder Cron Job Ran";
		$master_msg = "$i reminder emails have been sent.
		
		This is an automated message sent from $site_name";
		mail($site_email, $master_subject, $master_msg, "From: $site_email\r\n");
	}

?>