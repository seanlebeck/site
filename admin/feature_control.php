<?php

require_once("admin.inc.php");
require_once("aauth.inc.php");

?>

<?php include_once("aheader.inc.php"); ?>

<?php

if (isset($_POST['feature_sub']))
		{
			
			
		$feature_upd = mysql_query("update vivaru_feature_control set default_city = '".$_POST['default_city']."', max_abuse_reports = '".$_POST['max_abuse_reports']."',
		sef = '".$_POST['sef']."', site_calendar = '".$_POST['site_calendar']."', post_image = '".$_POST['post_image']."',
		numbers_directory = '".$_POST['numbers_directory']."', numbers_location = '".$_POST['numbers_location']."', numbers_picture = '".$_POST['numbers_picture']."',
		currency_symbol = '".$_POST['currency_symbol']."', rich_text = '".$_POST['rich_text']."', expire_ads_after = '".$_POST['expire_ads_after']."', expire_events_after = '".$_POST['expire_events_after']."', 
		expire_images_after = '".$_POST['expire_images_after']."', show_right_sidebar = '".$_POST['show_right_sidebar']."', 
		show_cat_adcount = '".$_POST['show_cat_adcount']."', show_subcat_adcount = '".$_POST['show_subcat_adcount']."', pic_maxsize = '".$_POST['pic_maxsize']."', 
		images_max_width = '".$_POST['images_max_width']."', images_max_height = '".$_POST['images_max_height']."', ad_preview_chars = '".$_POST['ad_preview_chars']."', 
		moderate_ads = '".$_POST['moderate_ads']."', moderate_events = '".$_POST['moderate_events']."', moderate_images = '".$_POST['moderate_images']."', 
		enable_promotions = '".$_POST['enable_promotions']."', enable_featured_ads = '".$_POST['enable_featured_ads']."', enable_extended_ads = '".$_POST['enable_extended_ads']."', currency = '".$_POST['currency']."'");
			
			
		}

		$feature_data_que = mysql_query("select * from vivaru_feature_control");
		$feature_data = mysql_fetch_array($feature_data_que);
		$city_que = mysql_query("select * from vivaru_cities");
		$city_name = mysql_fetch_array(mysql_query("select * from vivaru_cities where cityid = $feature_data[0]"));
?>

<h2>Feature Control</h2>
<p class="tip"><img src="images/tip.gif" border="0" align="absmiddle"> You can control most of the features of your classifieds site from this page.</p>

<?php if ($feature_upd) { echo "<p><div class=\"msg\" style='border:1px solid green;padding:5px;font-size:14px;font-weight:bold;background:lightgreen;color:black;'>Information updated successfully</div></p>"; } ?>
<form class="box" name="feature_control" action="feature_control.php" method="post">
<table width="800" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Default City</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;">
	
    <select name="default_city" style="width:164px;">
    <option value="<?php echo $city_name[0]; ?>"><?php if ($city_name[0] == 0) { echo "None"; } else { echo $city_name[1]; } ?></option>
    <option value="0">None</option>
	<?php while ($city_data = mysql_fetch_array($city_que)) { ?>
    <option value="<?php echo $city_data[0]; ?>"><?php echo $city_data[1]; ?></option>
    <?php } ?>
    </select>
	<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">You can set default city for your users.</font></span>
    </td>
    </tr>
    <tr>
      <td style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Maximum Abuse Reports</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="4" name="max_abuse_reports" value="<?php echo $feature_data[1]; ?>" />
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Number of abuse reports before ad is suspended. Set to 0 to disable.</font></span>
        </td>
    </tr>
    <tr>
      <td style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Category Columns</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="4" name="numbers_directory" value="<?php echo $feature_data[5]; ?>" />
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Number of columns for Categories on main page.</font></span>
		</td>
    </tr>
    <tr>
      <td style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Location Columns</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="4" name="numbers_location" value="<?php echo $feature_data[6]; ?>" />
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Number of columns for Locations (popup box) on main page.</font></span>
		</td>
    </tr>
    <tr>
      <td style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Ad Upload Fields</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="4" name="numbers_picture" value="<?php echo $feature_data[7]; ?>" />
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Number of image upload fields on post ad page.</font></span>
		</td>
    </tr>

    <tr>
      <td valign="top" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Search Engine Friendly URL</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;">
        <label class="switchGREEN"><input type="radio" <?php if ($feature_data[2] == '1') { echo "checked='checked'"; } ?> name="sef" value="1" />On</label>
        <label class="switchRED"><input type="radio" <?php if ($feature_data[2] == '0') { echo "checked='checked'"; } ?> name="sef" value="0" />Off</label>
        </td>
    </tr>
    <tr>
    <td valign="top" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Event Posting</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;">
    <label class="switchGREEN"><input type="radio" <?php if ($feature_data[3] == '1') { echo "checked='checked'"; } ?> name="site_calendar" value="1" />On</label>
    <label class="switchRED"><input type="radio" <?php if ($feature_data[3] == '0') { echo "checked='checked'"; } ?> name="site_calendar" value="0" />Off</label>
    </td>
    </tr>
    <tr>
    <td valign="top" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Image posting</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;">
    <label class="switchGREEN"><input type="radio" <?php if ($feature_data[4] == '1') { echo "checked='checked'"; } ?> name="post_image" value="1" />On</label>
    <label class="switchRED"><input type="radio" <?php if ($feature_data[4] == '0') { echo "checked='checked'"; } ?> name="post_image" value="0" />Off</label>
    </td>
    </tr>
    <tr>
      <td valign="top" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Rich Text</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;"><label class="switchGREEN">
        <input type="radio" <?php if ($feature_data[9] == '1') { echo "checked='checked'"; } ?> name="rich_text" value="1" />On</label>
        <label class="switchRED">
          <input type="radio" <?php if ($feature_data[9] == '0') { echo "checked='checked'"; } ?> name="rich_text" value="0" />Off</label></td>
    </tr>
<!--    <tr>
    <td>Change Currency Symbol</td>
    <td>
    <select style="width:166px;" name="currency_symbol">
     <option value="<?php //echo $feature_data[8]; ?>"><?php //echo $feature_data[8]; ?></option>
     <option value="$">$</option>
     <option value="&#128;">&euro;</option>
    </select>
    </td>
    </tr> -->
	
	<!-- ads, images, events expiration length starts here -->
	
    <tr>
      <td style="padding:5px;background:azure;font-weight:bold;">Expire ads after</td>
      <td width="600" style="padding:5px;"><input type="text" size="4" name="expire_ads_after" value="<?php echo $feature_data[10]; ?>" /> Days. 
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Amount of days after which ads are to auto expire. Can also be set per subcategory.</font></span>
		</td>
    </tr>
	
	    <tr>
      <td style="padding:5px;background:azure;font-weight:bold;">Expire events after</td>
      <td width="600" style="padding:5px;"><input type="text" size="4" name="expire_events_after" value="<?php echo $feature_data[11]; ?>" /> Days. 
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Amount of days after which events are to auto expire.</font></span>
		</td>
    </tr>
	
	    <tr>
      <td style="border-bottom:1px dotted black; padding:5px;background:azure;font-weight:bold;">Expire images after</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="4" name="expire_images_after" value="<?php echo $feature_data[12]; ?>" /> Days. 
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Amount of days after which images are to auto expire.</font></span>
		</td>
    </tr>
	
		<!-- ads, images, events expiration length ends here -->
		
		    <tr>
      <td valign="top" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Show Featured Ads Sidebar</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;">
        <input type="radio" <?php if ($feature_data[13] == '1') { echo "checked='checked'"; } ?> name="show_right_sidebar" value="1" />Yes
        <input type="radio" <?php if ($feature_data[13] == '0') { echo "checked='checked'"; } ?> name="show_right_sidebar" value="0" />No</td>
    </tr>
	
			    <tr>
      <td valign="top" style="padding:5px;background:azure;font-weight:bold;">Show Ad count next to category</td>
      <td width="600" style="padding:5px;">
        <input type="radio" <?php if ($feature_data[14] == '1') { echo "checked='checked'"; } ?> name="show_cat_adcount" value="1" />Yes
        <input type="radio" <?php if ($feature_data[14] == '0') { echo "checked='checked'"; } ?> name="show_cat_adcount" value="0" />No</td>
    </tr>
	
				    <tr>
      <td valign="top" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Show Ad count next to subcategory</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;">
        <input type="radio" <?php if ($feature_data[15] == '1') { echo "checked='checked'"; } ?> name="show_subcat_adcount" value="1" />Yes
        <input type="radio" <?php if ($feature_data[15] == '0') { echo "checked='checked'"; } ?> name="show_subcat_adcount" value="0" />No</td>
    </tr>
	
	    <tr>
      <td style="padding:5px;background:azure;font-weight:bold;">Max picture size (in KB)</td>
      <td width="600" style="padding:5px;"><input type="text" size="4" name="pic_maxsize" value="<?php echo $feature_data[16]; ?>" /> KB. 
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Maximum size of uploaded pictures.</font></span>
		</td>
    </tr>
	
		    <tr>
      <td style="padding:5px;background:azure;font-weight:bold;">Image Max Width</td>
      <td width="600" style="padding:5px;"><input type="text" size="4" name="images_max_width" value="<?php echo $feature_data[17]; ?>" /> Pixels. 
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Maximum width to which pictures uploaded to the images category as well as those attached to ads are to be resized</font></span>
		</td>
    </tr>
	
			    <tr>
      <td style="border-bottom:1px dotted black; padding:5px;background:azure;font-weight:bold;">Image Max Height</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="4" name="images_max_height" value="<?php echo $feature_data[18]; ?>" /> Pixels. 
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Maximum height to which pictures uploaded to the images category as well as those attached to ads are to be resized</font></span>
		</td>
    </tr>
	
		<tr>
      <td style="border-bottom:1px dotted black; padding:5px;background:azure;font-weight:bold;">Ad Preview Chars.</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;"><input type="text" size="4" name="ad_preview_chars" value="<?php echo $feature_data[19]; ?>" /> 
	  <span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Show preview of ads in category pages. Specify number of characters to show. Set to 0 to disable.</font></span>
		</td>
    </tr>
	
							    <tr>
      <td valign="top" style="padding:5px;background:azure;font-weight:bold;">Moderate Ads</td>
      <td width="600" style="padding:5px;">
        <input type="radio" <?php if ($feature_data[20] == '1') { echo "checked='checked'"; } ?> name="moderate_ads" value="1" />Yes
        <input type="radio" <?php if ($feature_data[20] == '0') { echo "checked='checked'"; } ?> name="moderate_ads" value="0" />No</td>
    </tr>
	
								    <tr>
      <td valign="top" style="padding:5px;background:azure;font-weight:bold;">Moderate Events</td>
      <td width="600" style="padding:5px;">
        <input type="radio" <?php if ($feature_data[21] == '1') { echo "checked='checked'"; } ?> name="moderate_events" value="1" />Yes
        <input type="radio" <?php if ($feature_data[21] == '0') { echo "checked='checked'"; } ?> name="moderate_events" value="0" />No</td>
    </tr>
	
	<tr>
      <td valign="top" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Moderate Images</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;">
        <input type="radio" <?php if ($feature_data[22] == '1') { echo "checked='checked'"; } ?> name="moderate_images" value="1" />Yes
        <input type="radio" <?php if ($feature_data[22] == '0') { echo "checked='checked'"; } ?> name="moderate_images" value="0" />No</td>
    </tr>
	
	<tr>
	      <td valign="top" style="padding:5px;background:azure;font-weight:bold;">Enable Paid Promotions</td>
      <td width="600" style="padding:5px;">
        <input type="radio" <?php if ($feature_data[23] == '1') { echo "checked='checked'"; } ?> name="enable_promotions" value="1" />Yes
        <input type="radio" <?php if ($feature_data[23] == '0') { echo "checked='checked'"; } ?> name="enable_promotions" value="0" />No</td>
    </tr>
	
	<tr>
		      <td valign="top" style="padding:5px;background:azure;font-weight:bold;">Enable Featured Ads</td>
      <td width="600" style="padding:5px;">
        <input type="radio" <?php if ($feature_data[24] == '1') { echo "checked='checked'"; } ?> name="enable_featured_ads" value="1" />Yes
        <input type="radio" <?php if ($feature_data[24] == '0') { echo "checked='checked'"; } ?> name="enable_featured_ads" value="0" />No</td>
    </tr>
	
		<tr>
		      <td valign="top" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Enable Extended Ads</td>
      <td width="600" style="border-bottom:1px dotted black; padding:5px;">
        <input type="radio" <?php if ($feature_data[25] == '1') { echo "checked='checked'"; } ?> name="enable_extended_ads" value="1" />Yes
        <input type="radio" <?php if ($feature_data[25] == '0') { echo "checked='checked'"; } ?> name="enable_extended_ads" value="0" />No</td>
    </tr>
	
	 <tr>
    <td width="200" style="border-bottom:1px dotted black;padding:5px;background:azure;font-weight:bold;">Website Currency Symbol</td>
    <td width="600" style="border-bottom:1px dotted black; padding:5px;">
    <select style="width:50px;" name="currency">
     <option value="<?php echo $site_data[26]; ?>"><?php echo $feature_data[26]; ?></option>
     <option value="$">$</option>
     <option value="&#8364;">&#8364;</option>
	 <option value="&#163;">&#163;</option>
    </select>
<span> <img src="images/tip.gif" border="0" align="absmiddle"> <font size="1" color="brown">Symbol for currency to use for prices. Will display across the website.</font></span>
    </td>
    </tr>
	
   
    <tr>
  
    <td colspan="2" width="800" style="padding:5px;"><input type="submit" value="Save Settings" name="feature_sub" /></td>
    </tr>    
</table>
</form>
