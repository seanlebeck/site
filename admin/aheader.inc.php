<?php

require_once("../config.inc.php");

	$prun_sql = "SELECT last_prune FROM $t_contact_update  
			     WHERE last_prune < '".(time()-$ad_contact_db_empty)."'";	 
	$res = mysql_query($prun_sql);
							
		if( mysql_num_rows($res) > 0 )
		{
		
		 	$del_sql = "SELECT temp_id FROM $t_contact_temp  
			            WHERE time_sent < '".(time()-$ad_contact_db_empty)."'";
			$del = mysql_query($del_sql);
			
			//echo $del_sql; // testing
						
				while( $row = mysql_fetch_array($del) ) 
				{
					mysql_query("DELETE FROM $t_contact_temp WHERE temp_id = '".$row['temp_id']."'");
					$i++;
				}				 
			       
				    mysql_query("UPDATE $t_contact_update SET last_prune = '".time()."'");
				    mysql_query("OPTIMIZE TABLE $t_contact_temp");
								
		}


?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 TRANSITIONAL//EN">
<html>
<head>
<title>Admin panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langx['charset']; ?>">
<link rel="stylesheet" type="text/css" href="astyle.default.css">
<link rel="stylesheet" type="text/css" href="apager.css">
<?php /* START mod-paid-categories */ ?>
<link rel="stylesheet" type="text/css" href="../paid_cats/admin/paid_categories.css">
<script src="../paid_cats/admin/paid_categories.js"></script>
<?php /* END mod-paid-categories */ ?>

</head>

<body>

<table width="100%">

<tr>
<td><div id="logo" style="cursor:pointer;padding-right:25x;" onClick="location.href='home.php';">Admin Panel - <?php echo $app_fullname; ?></div></td>

<td align="left">
NEED HELP? Please submit a <a href="http://www.vivaru.com/support/" target="_blank"><b>SUPPORT TICKET</b></a><a href="http://www.zvezda.co.uk">.</a>
</td>
</tr>

<tr>
<td colspan="3">
<hr>
</td>
</tr>

</table>


<table border="0" cellspacing="0" cellpadding="0" width="100%" id="maintable">

<tr>


<td valign="top" width="150">

<div class="menus" style="width:150px;">

<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">General</td></tr>
<tr><td class="menucell"><a href="site_control.php" class="menulink">General Settings</a></td></tr>
<tr><td class="menucell"><a href="feature_control.php" class="menulink">More Settings</a></td></tr>
<tr><td class="menucell"><a href="home.php" class="menulink">Admin Home</a></td></tr>
<tr><td class="menucell"><a href="<?php echo $script_url; ?>/" class="menulink" target="_blank">Classifieds Home</a></td></tr>
<tr><td class="menucell"><a href="index.php?signout=now" class="menulink">Signout</a></td></tr>
</table>

<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">Manage Posts</td></tr>
<tr><td class="menucell"><a href="ads.php" class="menulink">Ads</a></td></tr>
<tr><td class="menucell"><a href="ads.php?subcatid=-1" class="menulink">Events</a></td></tr>
<tr><td class="menucell"><a href="images.php" class="menulink">Images</a></td></tr>
<tr><td class="menucell"><a href="postad.php" class="menulink">Post Ad/Event</a></td></tr>
</table>

<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">Categories</td></tr>
<tr><td class="menucell"><a href="cats.php" class="menulink">Categories</a></td></tr>
<tr><td class="menucell"><a href="subcats.php" class="menulink">Subcategories</a></td></tr>
</table>

<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">Locations</td></tr>
<tr><td class="menucell"><a href="regions.php" class="menulink">Regions</a></td></tr>
<tr><td class="menucell"><a href="cities.php" class="menulink">Cities</a></td></tr>
</table>

<!--CONTACT FORM & COMMENTS START-->
<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">Messages</td></tr>
<tr><td class="menucell"><a href="contactformmsgs.php" class="menulink">Contact Form</a></td></tr>
<tr><td class="menucell"><a href="comments.php" class="menulink">Comments</a></td></tr>
</table>
<!--CONTACT FORM & COMMENTS END-->

<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">User Accounts</td></tr>
<tr><td class="menucell"><a href="accounts.php" class="menulink">View Users</a></td></tr>
<tr><td class="menucell"><a href="accounts.php?action=add_user" class="menulink">Add User</a></td></tr>
</table>
<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">Paid Options</td></tr>
<tr><td class="menucell"><a href="options_featured.php" class="menulink">Featured Ad Options</a></td></tr>
<tr><td class="menucell"><a href="options_extended.php" class="menulink">Extended Ad Options</a></td></tr>
</table>

<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">Language</td></tr>
<tr><td class="menucell"><a href="language.php" class="menulink">Language Editor</a></td></tr>
<tr><td class="menucell"><a href="nwsl_email.php" class="menulink">Send Newsletter</a></td></tr>
<tr><td class="menucell"><a href="maillist.php" class="menulink">Show Maillist</a></td></tr>
<tr><td class="menucell"><a href="mailtemplates.php" class="menulink">Email Templates</a></td></tr>
</table>

<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">Tools</td></tr>
<tr><td class="menucell"><a href="import.php" class="menulink">Import Data</a></td></tr>

<tr><td class="menucell"><a href="spamfilter.php" class="menulink">Spam Filter</a></td></tr>

<tr><td class="menucell"><a href="badwords.php" class="menulink">Bad Word Filter</a></td></tr>
<tr><td class="menucell"><a href="ipblock.php" class="menulink">IP Block</a></td></tr>
<tr><td class="menucell"><a href="logo.php" class="menulink">Logo Update</a></td></tr>
</table>

<table border="0" cellspacing="1" cellpadding="2" class="menu" width="100%">
<tr><td class="menuhead">View Reports</td></tr>
<tr><td class="menucell"><a href="payments.php" class="menulink">Payment History</a></td></tr>
</table>

</div>
<br><br>

</td>

<td valign="top" id="main">



