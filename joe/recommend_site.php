
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Recommend Our Site</title>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function checkEmail(myForm) {
if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(myForm.friendemail.value)){
return (true)
}
alert("Invalid Friend/Your E-mail Address! Please re-enter.")
return (false)
}
//  End -->
</script>
</head>

<body>
<p><h2>Recommend Our Site</h2>
<p><br />
  You can now directly suggest to your friends about our website. Please fill in the details below and start suggesting.</p>
</p>
<p><?php

$username = $_POST['username'];
$useremail = $_POST['useremail'];
$friendname = $_POST['friendname'];
$friendemail = $_POST['friendemail'];
$message = $_POST['message'];
$site = $_SERVER['HTTP_HOST'];
$self = $_SERVER['PHP_SELF'];

if (!isset($_POST['friendname'])) {
		echo "";
	}
	
	elseif (!isset($_POST['friendemail'])) {
		echo "<font color=\"red\"><b>ERROR! Please fill in all the fields correctly in order to send the e-mail to your friend.</b></font>";
	}
    
    else {
    
    mail ($friendemail, "$username is recommending you the website $site", "Sender Name: $username \nSender E-mail: $useremail \nMessage: $message\nSuggested Website: http://$site\n\n", "From:$username <$useremail>");
	
	echo "<font color=\"red\"><b>Thank you <u><a href=\"mailto:$useremail\" target=\"_blank\">$username</a></u>, An e-mail has been successfully sent to <a href=\"mailto:$friendemail\" target=\"_blank\">$friendname</a>.</b></font>";
	}
?>
</p>
<form action="<?php echo "$self?view=page&pagename=recommend_site"; ?>" onSubmit="return checkEmail(this)" method="post" name="myForm" target="_self" id="recommend_site">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>Your Name</td>
      <td><label>
        <input type="text" name="username" id="username" />
      </label></td>
    </tr>
    <tr>
      <td>Your E-mail</td>
      <td><label>
        <input type="text" name="useremail" id="useremail" />
      </label></td>
    </tr>
    <tr>
      <td>Friend Name</td>
      <td><label>
        <input type="text" name="friendname" id="friendname" />
      </label></td>
    </tr>
    <tr>
      <td>Friend E-mail</td>
      <td><label>
        <input type="text" name="friendemail" id="friendemail" />
      </label></td>
    </tr>
    <tr>
      <td><p>Message Details</p></td>
      <td><label>
        <textarea name="message" id="message" cols="45" rows="5">Hello My Friend,

This website <?php echo "$site"; ?> is very good from my opinion. I just felt like recommending you about this website. I hope you will like the classifieds website.

Regards,
Your Friend,
<?php echo "$username."; ?></textarea>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><label>
        <button type="submit" name="submit" value="submit">Submit</button>
&nbsp; 
<button type="reset" name="reset" value="reset">Reset</button>
      </label></td>
    </tr>
  </table>
</form>



<p>&nbsp;</p>
</body>
</html>
