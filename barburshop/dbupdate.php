<?

require_once("initvars.inc.php");
require_once("config.inc.php");

?>
<html>
<head>
<title>Vivaru PHP Classifieds Database Update</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<br>
<div style="margin:20px 100px;">
<h2>Vivaru PHP Classifieds Database Update from v 2.1 to v 2.2</h2>
<?

if($_POST['confirm'])
	{
	?><p><?

	$error = 0;
	
	$sqla = @mysql_query ("ALTER TABLE `$t_cats` 
							CHANGE `catname` `catname` TEXT NOT NULL;") or die ($sql.mysql_error());

	$sqlb = @mysql_query ("ALTER TABLE `$t_subcats` 
							CHANGE `subcatname` `subcatname` TEXT NOT NULL,
							CHANGE `pricelabel` `pricelabel` TEXT NOT NULL;") or die ($sql.mysql_error());

	$sqlc = @mysql_query ("ALTER TABLE `$t_subcatxfields` 
							CHANGE `name` `name` TEXT NOT NULL, 
							CHANGE `vals` `vals` TEXT NOT NULL") or die ($sql.mysql_error());

	$sqld = @mysql_query ("ALTER TABLE `$t_adxfields` 
							CHANGE `f1` `f1` TEXT NOT NULL, 
							CHANGE `f2` `f2` TEXT NOT NULL, 
							CHANGE `f3` `f3` TEXT NOT NULL, 
							CHANGE `f4` `f4` TEXT NOT NULL, 
							CHANGE `f5` `f5` TEXT NOT NULL, 
							CHANGE `f6` `f6` TEXT NOT NULL, 
							CHANGE `f7` `f7` TEXT NOT NULL, 
							CHANGE `f8` `f8` TEXT NOT NULL, 
							CHANGE `f9` `f9` TEXT NOT NULL, 
							CHANGE `f10` `f10` TEXT NOT NULL, 
							CHANGE `f11` `f11` TEXT NOT NULL, 
							CHANGE `f12` `f12` TEXT NOT NULL, 
							CHANGE `f13` `f13` TEXT NOT NULL, 
							CHANGE `f14` `f14` TEXT NOT NULL, 
							CHANGE `f15` `f15` TEXT NOT NULL") or die ($sql.mysql_error());
							
$sqle = @mysql_query ("ALTER TABLE `$t_feature_control` 
							DROP COLUMN smtp_function,
							DROP COLUMN smtp_host,
							DROP COLUMN smtp_port,
							DROP COLUMN smtp_authenticate,
							DROP COLUMN smtp_username,
							DROP COLUMN smtp_password") or die ($sql.mysql_error());			

		$sqlf = @mysql_query ("ALTER TABLE `$t_feature_control` 
  ADD expire_ads_after varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD expire_events_after varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD expire_images_after varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD show_right_sidebar varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD show_cat_adcount varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD show_subcat_adcount varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD pic_maxsize varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD images_max_width varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD images_max_height varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD ad_preview_chars varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD moderate_ads varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD moderate_events varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD moderate_images varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD enable_promotions varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD enable_featured_ads varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD enable_extended_ads varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  ADD currency varchar(100) character set utf8 collate utf8_unicode_ci default NULL") or die ($sql.mysql_error());	
  
  		$sqlg = @mysql_query ("ALTER TABLE `$t_site_control` 
 ADD mobile_site_name varchar(200) character set utf8 collate utf8_unicode_ci default NULL,
 ADD mobile_site_url varchar(200) character set utf8 collate utf8_unicode_ci default NULL,
 ADD mobile_site_email varchar(200) character set utf8 collate utf8_unicode_ci default NULL,
 ADD comments_system varchar(16) character set utf8 collate utf8_unicode_ci default NULL,
 ADD cookie_domain varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
 ADD user_email_activation varchar(16) character set utf8 collate utf8_unicode_ci default NULL,
 ADD force_user_login varchar(16) character set utf8 collate utf8_unicode_ci default NULL") or die ($sql.mysql_error());	
 
   		$sqlg = @mysql_query ("UPDATE `$t_site_control` 
		SET mobile_site_name='My Mobile Classifieds', 
		mobile_site_url='http://www.website.com/mobile', 
		mobile_site_email='mobile@website.com', 
		comments_system='FALSE', 
		cookie_domain='.website.com', 
		user_email_activation='0',
		force_user_login='0'") or die ($sql.mysql_error());	

		$sqlk = @mysql_query ("UPDATE `$t_feature_control` 
		SET expire_ads_after='60', 
		expire_events_after='60', 
		expire_images_after='60', 
		show_right_sidebar='1', 
		show_cat_adcount='1', 
		show_subcat_adcount='0', 
		pic_maxsize='3000', 
		images_max_width='300', 
		images_max_height='700', 
		ad_preview_chars='80', 
		moderate_ads='1', 
		moderate_events='1', 
		moderate_images='1', 
		enable_promotions='1', 
		enable_featured_ads='1', 
		enable_extended_ads='1', 
		currency='$'") or die ($sql.mysql_error());					
		
		if(mysql_error())
			{
			echo ("<div class=\"err\">Error! The database WAS NOT updated successfully.</div>");
			$error = 1;
			}
		else
			echo "<div class=\"msg\"><b>Database Updated Successfully</b></div>";

	if($error)
		echo "<p>The database update was <span class=\"err\">NOT successful</span>. Please change any wrong settings in the config and try again.</p>";
	else
		echo "<p>Everything appears to be ok!</p><br><h1><font color=\"red\">DELETE THIS FILE NOW FROM YOUR SERVER (dbupdate.php) FOR SECURITY REASONS!</font></h1>";
}
if(!$_POST['confirm'])
	{
	?>
	<form action="" method="post">
	
	<p>The following are the database connection settings specified in the config.inc.php file.<br>
	Verify the details and then click once on<b>Update DB</b> to setup the database. <br>
	Please note that you must get a success message on the next screen in order for the database to be updated successfully.
</p>
	
	<p>
	<h3><font color="red">BE SURE TO BACKUP YOUR DATABASE FIRST!</font></h3>
	We will not take responsibility if you mess up your database.<br>Please be sure to make a backup first in case something goes wrong.
	<br><br>
	
	<table>
		<tr>
			<td><b>MySQL Host:</b></td><td><?=$db_host?></td>
		</tr>
		<tr>
			<td><b>MySQL Username:</b></td><td><?=$db_user?></td>
		</tr>
		<tr>
			<td><b>MySQL Password:</b></td><td><?=$db_pass?></td>
		</tr>
		<tr>
			<td><b>MySQL Database:</b></td><td><?=$db_name?></td>
		</tr>
		<tr>
			<td><b>Table Prefix:</b></td><td><?=$tprefix?></td>
		</tr>
	</table>
	</p>
	
	<button type="submit" name="confirm" value="Setup">Update DB</button>

	</form>
<?	}?>

</div>
</body>
</html>