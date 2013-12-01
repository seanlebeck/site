<?php

require_once("admin.inc.php");
require_once("aauth.inc.php");

?>

<?php include_once("aheader.inc.php"); ?>

<?php

if (isset($_POST['site_sub']))

	if ($_POST['site_title'] == '' || $_POST['site_title'] == ' ') 
	 {
	 $show_err = 'Please enter Site Title';
	 }
	elseif ($_POST['site_email'] == '' || $_POST['site_email'] == ' ') 
	 {
	 $show_err = 'Please enter Webmaster Email';
	 }
	 elseif ($_POST['script_url'] == '' || $_POST['script_url'] == ' ') 
	 {
	 $show_err = 'Please enter Script URL';
	 }
	 elseif ($_POST['mobile_site_name'] == '' || $_POST['mobile_site_name'] == ' ') 
	 {
	 $show_err = 'Please enter Mobile Site Title';
	 }
	 elseif ($_POST['cookie_domain'] == '' || $_POST['cookie_domain'] == ' ') 
	 {
	 $show_err = 'Please enter Cookie Domain';
	 }
	  elseif ($_POST['mobile_site_url'] == '' || $_POST['mobile_site_url'] == ' ') 
	 {
	 $show_err = 'Please enter Mobile Site URL';
	 }
	   elseif ($_POST['mobile_site_email'] == '' || $_POST['mobile_site_email'] == ' ') 
	 {
	 $show_err = 'Please enter Mobile Site email';
	 }
	 elseif ($_POST['admin_password'] != $_POST['retype_password']) 
	 {
	 $show_err = 'Both Password Field must be same.';
	 }
	 
	 else
	 {
		{
		$offmesg = strip_tags($_POST['offline_mesg']); 
		$site_upd = mysql_query("update vivaru_site_control set site_name = '".$_POST['site_title']."', site_email = '".$_POST['site_email']."',
		script_url = '".$_POST['script_url']."', language = '".$_POST['language']."', meta_keywords = '".$_POST['meta_keywords']."',
		meta_description = '".$_POST['meta_description']."', turn_site = '".$_POST['onoff']."', offline_mesg = '$offmesg',
		paypal_email = '".$_POST['paypal_email']."', paypal_currency_symbol = '".$_POST['paypal_currency_symbol']."',
		currency_word = '".$_POST['currency_word']."', mobile_site_name = '".$_POST['mobile_site_name']."', mobile_site_url = '".$_POST['mobile_site_url']."', mobile_site_email = '".$_POST['mobile_site_email']."', comments_system = '".$_POST['conoff']."', cookie_domain = '".$_POST['cookie_domain']."', user_email_activation = '".$_POST['aeonoff']."', force_user_login = '".$_POST['fuonoff']."'");
			if ($_POST['admin_password'] != '')
			{
			  mysql_query("update vivaru_site_control set admin_password = '".$_POST['admin_password']."'");
			}
		}
		if (!$site_upd)
		 {
		  $show_err = 'Could not connect database.';
		 }

		$site_data_que = mysql_query("select * from vivaru_site_control");
		$site_data = mysql_fetch_array($site_data_que);
	 }

?>

<h2>General Settings</h2>
<p class="tip"><img src="images/tip.gif" border="0" align="absmiddle"> Change the the settings of your classifieds site.</p>

<?php if ($site_upd) { echo "<p><div class=\"msg\" style='border:1px solid green;padding:5px;font-size:14px;font-weight:bold;background:lightgreen;color:black;'>Information updated successfully</div></p>"; }

else { echo '<p class="err">'.$show_err.'</p>'; } ?>
<form class="box" action="site_control.php" method="post">
<table width="800" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;"><label for="site_title">Site Title</label></td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="41" name="site_title" value="<?php echo $site_data[0]; ?>" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Website title. Will show in browser title bar.</font></span>
  </td>
    </tr>
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Site Email</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="41" name="site_email" value="<?php echo $site_data[1]; ?>" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Webmaster email address. All email will be sent from this address.</font></span>
	</td>
    </tr>
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Script URL</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="41" name="script_url" value="<?php echo $site_data[2]; ?>" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">URL of script installation including folders (without trailing slashes). Must start with <b>http://</b></font></span>
	</td>
    </tr>
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Language</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;">
    <select style="width:220px;" name="language">
     <option value="<?php echo $site_data[3]; ?>"><?php echo $site_data[3]; ?></option>
     <option value="en">EN</option>
    </select>
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Site Language. Currently English only.</font></span>
    </td>
    </tr>
    </tr>
    <tr>
    <td width="200" style="padding:5px;background:azure;font-weight:bold;">Meta Keywords</td>
    <td width="600" style="padding:5px;"><input type="text" size="41" name="meta_keywords" value="<?php echo $site_data[4]; ?>" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Enter the keywords for your website.</font></span>
	</td>
    </tr>
    </tr>
    <tr>
    <td valign="top" width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;" >Meta Description</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><textarea name="meta_description" cols="28" rows="3"><?php echo $site_data[5]; ?></textarea>
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Site descriptionfor Search Engines.</font></span>
	</td>
    </tr>
    </tr>
    <tr>
    <td valign="top" width="200" style="padding:5px;background:azure;font-weight:bold;">Site Offline</td>
    <td width="600" style="padding:5px;">
    <input type="radio" <?php if ($site_data[6] == 'yes') { echo "checked='checked'"; } ?> name="onoff" value="yes" />Yes
    <input type="radio" <?php if ($site_data[6] == 'no') { echo "checked='checked'"; } ?> name="onoff" value="no" />No
	<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Turn website on/off for maintenance.</font></span>
    </td>
    </tr>
    
    <tr>
    <td valign="top" width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Site Offline  Message</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><textarea cols="28" rows="3" name="offline_mesg"><?php echo $site_data[7]; ?></textarea>
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">No HTML allowed.</font></span>
	</td>
    </tr>
  
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Paypal Email</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="30" name="paypal_email" value="<?php echo $site_data[8]; ?>" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Your Paypal email address for receiving payments.</font></span>
	</td>
    </tr>
    
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">PayPal Currency Code</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;">
    <select style="width:220px;" name="currency_word">
     <option value="<?php echo $site_data[12]; ?>"><?php echo $site_data[12]; ?></option>
     <option value="USD">USD</option>
     <option value="EUR">EUR</option>
	 <option value="EUR">GBP</option>
    </select>
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">PayPal currency Code. Must match PayPal currency symbol below</font></span>
    </td>
    </tr>
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">PayPal Currency Symbol</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;">
    <select style="width:50px;" name="paypal_currency_symbol">
     <option value="<?php echo $site_data[9]; ?>"><?php echo $site_data[9]; ?></option>
     <option value="$">$</option>
     <option value="&#8364;">&#8364;</option>
	 <option value="&#163;">&#163;</option>
    </select>
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">PayPal currency Symbol. Must match PayPal currency Code above.</font></span>
    </td>
    </tr>
    
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Admin Username</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="30" name="user_name" value="admin" disabled="disabled" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Cannot be changed in this version.</font></span>
	</td>
    </tr>
    </tr>
    <tr>
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Admin Password</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="password" size="30" value="" name="admin_password" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Only if you want to change Admin password.</font></span>
  </td>
    </tr>
    </tr>
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Re-type Admin Password</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="password" value="" name="retype_password" size="30" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Only if you want to change Admin password.</font></span>
	</td>
    </tr>
    </tr>
	
<!-- mobile site settings start -->	

  <tr>
    <td width="200" style="padding:5px;background:azure;font-weight:bold;">Mobile Site Title</td>
    <td width="600" style="padding:5px;"><input type="text" size="41" name="mobile_site_name" value="<?php echo $site_data[13]; ?>" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Mobile Website title. Will show in browser title bar and mobile site header.</font></span>
	</td>
    </tr>
	
	    <tr>
    <td width="200" style="padding:5px;background:azure;font-weight:bold;">Mobile Site URL</td>
    <td width="600" style="padding:5px;"><input type="text" size="41" name="mobile_site_url" value="<?php echo $site_data[14]; ?>" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">URL of mobile site (without trailing slashes). Must start with <b><font color='blue'>http://</font></b> Normally this will be http://www.yourwebsitename.com/mobile/</font></span>
	</td>
    </tr>
	
	    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Mobile Site Email</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="41" name="mobile_site_email" value="<?php echo $site_data[15]; ?>" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Webmaster email address for mobile site. All mobile site emails will be sent from this address.</font></span>
	</td>
    </tr>
	
<!-- mobile site settings end -->	
	
<!-- Comments settings start -->

	 <tr>
    <td valign="top" width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Enable Comments for ads</td>
    <td width="600" style="border-bottom:1px dotted black;padding:5px;">
    <input type="radio" <?php if ($site_data[16] == 'TRUE') { echo "checked='checked'"; } ?> name="conoff" value="TRUE" />Yes
    <input type="radio" <?php if ($site_data[16] == 'FALSE') { echo "checked='checked'"; } ?> name="conoff" value="FALSE" />No
	<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Turn ad comments on/off.</font></span>
    </td>
    </tr>
	
<!-- Comments settings end -->
	
<!-- Cookie Domain settings start -->
	
   <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Cookie Domain</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="41" name="cookie_domain" value="<?php echo $site_data[17]; ?>" />
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Leave out the www part. Must start with a period. Will be something like this: <font color='blue'><b>.yourwebsite.com</b></font></font></span>
	</td>
    </tr>
	
<!-- Cookie Domain settings end -->
	
<!-- Accounts settings start -->
	
 <tr>
    <td valign="top" width="200" style="padding:5px;background:azure;font-weight:bold;">Account email activation</td>
    <td width="600" style="padding:5px;">
    <input type="radio" <?php if ($site_data[18] == '1') { echo "checked='checked'"; } ?> name="aeonoff" value="1" />Yes
    <input type="radio" <?php if ($site_data[18] == '0') { echo "checked='checked'"; } ?> name="aeonoff" value="0" />No
	<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">If disabled, users can signup without having to verfiy email address.</font></span>
    </td>
    </tr>
	
	 <tr>
    <td valign="top" width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Force user login</td>
    <td width="600" style="border-bottom:1px dotted black;padding:5px;">
    <input type="radio" <?php if ($site_data[19] == '1') { echo "checked='checked'"; } ?> name="fuonoff" value="1" />Yes
    <input type="radio" <?php if ($site_data[19] == '0') { echo "checked='checked'"; } ?> name="fuonoff" value="0" />No
	<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Force users to login first before they can post.</font></span>
    </td>
    </tr>
	
<!-- Accounts settings end -->
	
	
	
    <tr>
    <td colspan="2" width="800" style="border-bottom:1px dotted black; padding:5px;"><input type="submit" value="Save Settings" name="site_sub" /></td>
    </tr>    
</table>
</form>
</body>
</html>
