<?php 



require_once("admin.inc.php");

require_once("aauth.inc.php");

include_once("aheader.inc.php");



$your_message = stripslashes($_POST['your_message']);

if ( $your_message != '' )
{
$title = $_POST['title'];
$delay = $_POST['delay'];
$email_test = $_POST['email_test'];




if ( $email_test == 'Email All Users' )
{
	$result = mysql_query("SELECT DISTINCT email FROM $t_ads WHERE email != '' AND newsletter = '1'");
}
elseif ( $email_test == 'Test Email' )
{
	$result = mysql_query("SELECT DISTINCT email FROM $t_ads where email = '$site_email'");
}


$numrows = mysql_num_rows($result);

	if ( $email_test == 'Test Email' && $numrows == 0 )
	{
	echo "<b><font color=\"red\">There are no ads found using the email ($site_email)</font></b>.  <br><br>Since you are in test mode make sure to create a test ad first with the following email address:<br> <b>$site_email</b></b><br><br>You will then receive your test newsletter to this email.";
	
	exit;
	}

	while( $row = mysql_fetch_assoc($result) )
	{
		$email = $row['email'];
		$newres = mysql_query("SELECT adid, cityid, code FROM $t_ads WHERE email='$email'");
			
			while( $newrow = mysql_fetch_assoc($newres) )
			{
			$codemd5 = md5($newrow['code']);
				
			$unsubscribe = "To unsubscribe from this newsletter, click on the following link to visit your ad's edit page:

".$script_url."/?view=edit&isevent=&adid=".$newrow['adid']."&codemd5=".$codemd5."&cityid=".$newrow['cityid']."

Locate the option to receive newsletters and uncheck that checkbox and click on the update button to save changes.  
You will no longer receive newsletters from that listing again.";	
			}
			
				
// BEGIN Email that reader sees
$message = "
$your_message


$unsubscribe
";
// END Email that reader sees

$title = stripslashes($title);
$message = stripslashes($message);


$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text; charset=utf-8\r\n";  

echo $row['email'].'<br>';
mail($email, $title, $message, $headers . "From: $site_email\r\n" . "Reply-To: $site_email\r\n" . "X-Mailer: PHP/ 5.2.5");
usleep($delay);

	}

}

echo '<br><br>';

include_once("afooter.inc.php"); 
 
?>