<?php 


require_once("admin.inc.php");

require_once("aauth.inc.php");

include_once("aheader.inc.php");



$email = mysql_real_escape_string(htmlspecialchars(trim($_POST['email'])));

if ( $email != '' )
{
	$result = mysql_query("SELECT email FROM $t_ads WHERE email='$email'");
	$numrows = mysql_num_rows($result);
	
	if ( $numrows != 0 )
	{
		mysql_query("UPDATE $t_ads SET newsletter = '0' WHERE email='$email'");
		echo 'Email removed from mailing list<br><br> <a href="nwsl_email.php">Go to the main page?</a> or <a href="nwsl_exclude.php">Exclude another</a>';
	}
	else 
	{
		echo '<b><font color="red">No such email exists.</font></b><br><br>';
		echo '<a href="nwsl_email.php">Go to the main page?</a> - or - <a href="nwsl_exclude.php">Exclude another email</a>';
		echo '<br><br><br><br>';
	}
}
else 
{
?>

<form action="" method="post" class="box">
	   <table cellpadding="4" cellspacing="0" border="0" width="90%">
          <tr>
            <td><h2>Remove email from newsletter</h2></td>
          </tr>
          <tr>
            <td valign="top" style="padding:5px"><br>
              Email:
			  <br>
              <br>
              <input size="70" name="email">
              <br>
              <br>
            </td>
          </tr>
          <tr>
            <td valign="top" style="padding:5px"><input type="submit" class="button" name="Submit" value="Submit" />
            </td>
          </tr>
      </table>
</form>
<?php 

echo '<br>&nbsp;<a href="nwsl_email.php">Go back to the main newsletter page?</a><br><br>';

}
	
 include_once("afooter.inc.php"); 
 
?>