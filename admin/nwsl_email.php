

<?php 



require_once("admin.inc.php");

require_once("aauth.inc.php");

include_once("aheader.inc.php");


$title = str_replace('"',"'", $_POST['title']);
$your_message = str_replace('"',"'", $_POST['your_message']);
$email_test = $_POST['email_test'];



		$resd = mysql_query("SELECT COUNT(adid) AS total_subs FROM $t_ads WHERE newsletter = '1'");
      	$rowd = mysql_fetch_array($resd);
	    $total_subscribers = $rowd["total_subs"];



if ( $your_message != '' )
{

if ( $email_test == 'Test Email' )
{
	echo "<b><font color =\"green\">You are currently in test email mode so this newsletter will only be emailed to $site_email.</font</b>";
}

?>

<form action="nwsl_sendmail.php" method="post" />
	<table cellpadding="4" cellspacing="0" border="0" width="90%">
        <tr>
          <td><h2>Email Preview</h2></td>
        </tr>
        <tr>
          <td class="gridcellalt">
<?php
$preview = "Title : $title<br>
From : $site_email 
</td></tr>
<tr>
<td><br>
$your_message

<br><br>";

echo nl2br(stripslashes($preview));

?>
          </td>
        </tr>
        <tr>
          <td class="gridcellalt"><br>
            <b>If all is correct...</b>
            <input type="hidden" name="title" value="<?php echo $title ?>" />
            <input type="hidden" name="your_message" value="<?php echo $your_message ?>" />
            <input type="hidden" name="delay" value="<?php echo $_POST['delay'] ?>" />
            <input type="hidden" name="email_test" value="<?php echo $_POST['email_test'] ?>" /> 
            <br>
            <br>
            <br>
            <input type="submit" class="button" name="Submit" value="Send The Message" />
          </td>
        </tr>
   </table>
</form>
<?php

 if ( empty($title) ) 
 { 
 	echo 'Title Field is empty'; 
 }
 elseif ( empty($your_message) )
 {
 	echo 'Message Field is empty'; 
 }

}
else 
{
?>
<form action="" method="post">
  <table cellpadding="1" cellspacing="0" border="0" width="90%">
    <tr>
      <td><table cellpadding="4" cellspacing="0" border="0" width="100%">
          <tr>
            <td colspan="2"><h2>Create a Newsletter</h2></td>
          </tr>
          <tr>
            <td><a href="nwsl_exclude.php">Click here</a> to exclude an email<br></td>
			<td align="right">You currently have <b><?php echo $total_subscribers; ?></b> total subscribers</td>
		  </tr>
		    <td colspan="2">	

              <br>
              <br>
              Email Title:<br>
              <br>
              <input name="title" type="text" value="<?php echo $title ?>" size="120" />
              <br>
              <br>
              Enter Message:<br>
              <br>
              <textarea name="your_message" rows="10" cols="100" value="" tabindex="4"></textarea>
			  <br>
              <br>
			  Send Test Email First?: (Before you send an email to everyone you can test it first)
			  <br>
			  <br>
			  <select name="email_test">
				  <option value="Test Email">Test Email First</option>
				  <option value="Email All Users">Email All Users</option>
			  </select> 
              <br>
              <br>
              <br>
              Delay Time: (If you wish to add a delay to keep your server load down.  Leaving at 0 is fine for most servers)<br>
              <br>
              <input name="delay" value="0" size="15" maxlength="15">
              milliseconds &nbsp;&nbsp;(1 second = 1000 milliseconds)<br>
            </td>
          </tr>
          <tr>
            <td valign="top" colspan="2" style="padding:5px"><input type="submit" name="Submit" value="Submit" class="button" /></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
<?php 

}
 include_once("afooter.inc.php"); 
 
?>