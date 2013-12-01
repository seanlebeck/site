<?php





require_once("admin.inc.php");
require_once("aauth.inc.php");

//Account mod var
$acc_sql = (empty($_GET['user_id'])) ? "" : "AND user_id ='".intval($_GET['user_id'])."'";


$whereA = array();
$search_desc = "";

// Default values for ads
$table = $t_ads;
$ad_view = "showad";
$ad_section = "ads";
$isevent = 0;
$adtype = 'A';
$extra_fields = ",s.subcatname, c.catname";
$extra_join = "INNER JOIN $t_subcats s ON a.subcatid = s.subcatid INNER JOIN $t_cats c ON s.catid = c.catid";
$subcatid = $_GET['subcatid'];

if ($_GET['adid'])
{
	$whereA[] = "a.adid = ".$_GET['adid'];
	$adtype = $_GET['adtype'];

	if ($adtype == "E")
	{
		$table = $t_events;
		$ad_view = "showevent";
		$ad_section = "events";
		$isevent = 1;
		$extra_fields = ",UNIX_TIMESTAMP(a.starton) AS starton, UNIX_TIMESTAMP(a.endon) AS endon";
		$extra_join = "";
		$subcatid = $_GET['subcatid'] = -1;
	}
	else
	{
		$isevent = 0;

		//$whereA[] = "a.subcatid = $_GET[subcatid]";
		//$sql = "SELECT catname,subcatname FROM $t_subcats scat INNER JOIN $t_cats cat ON scat.catid = cat.catid WHERE subcatid=$_GET[subcatid]";
		//list($cat, $subcat) = mysql_fetch_array(mysql_query($sql));
		//$search_desc .= " in <b>$cat - $subcat</b>";
		//$_GET['subcatid'] = $subcat;
	}
}
if ($_GET['search'])
{
	$whereA[] = "(a.adtitle LIKE '%$_GET[search]%' OR a.addesc LIKE '%$_GET[search]%')";
	$search_desc .= " containing <b>$_GET[search]</b>";
}
if ($_GET['email']) 
{
	$whereA[] = "a.email LIKE '$_GET[email]'";
	$search_desc .= " posted by <b>'$_GET[email]'</b>";
}
if ($_GET['cityid']) 
{
	$whereA[] = "a.cityid = $_GET[cityid]";
	$sql = "SELECT countryname,cityname FROM $t_cities ct INNER JOIN $t_countries c ON ct.countryid = c.countryid WHERE cityid=$_GET[cityid]";
	list($country, $city) = mysql_fetch_array(mysql_query($sql));
	$search_desc .= " from <b>$city, $country</b>";
}
if($_GET['subcatid'])
{
	if ($_GET['subcatid'] == -1)
	{
		$table = $t_events;
		$ad_view = "showevent";
		$ad_section = "events";
		$isevent = 1;
		$adtype = 'E';
		$extra_fields = ",UNIX_TIMESTAMP(a.starton) AS starton, UNIX_TIMESTAMP(a.endon) AS endon";
		$extra_join = "";
	}
	else
	{
		$isevent = 0;
		$whereA[] = "a.subcatid = $_GET[subcatid]";
		$sql = "SELECT catname,subcatname FROM $t_subcats scat INNER JOIN $t_cats cat ON scat.catid = cat.catid WHERE subcatid=$_GET[subcatid]";
		list($cat, $subcat) = mysql_fetch_array(mysql_query($sql));
		$search_desc .= " in <b>$cat - $subcat</b>";
	}
}


$sort_fields = array("", "a.adid", "a.adtitle", "a.email", "a.ip", "countryname", "cityname", "a.hits", "a.abused", "a.verified", "a.enabled", "a.paid", "a.createdon", "a.expireson");
$sort_field_names = array("", "ID", "Title", "Email", "IP", "Region", "City", "Hits", "Spam/Abuse Reports", "Verified", "Enabled", "Paid", "Created On", "Expires On");


if ($ad_section == "events") {
	$sort_fields[] = "a.starton";
	$sort_field_names[] = "Start On";
	$sort_fields[] = "a.endon";
	$sort_field_names[] = "End On";
} else {
	$sort_fields[] = "catname";
	$sort_field_names[] = "Category";
	$sort_fields[] = "subcatname";
	$sort_field_names[] = "Subcategory";
}

$sort_key = 0 + $_GET['sortby'];
$sort = $sort_fields[$sort_key];
$sort_dir = $_GET['sortdir'] == "1" ? "ASC" : "DESC";
$order = $sort ? "$sort $sort_dir, a.createdon DESC" : "a.createdon DESC";

if ((isset($_GET['verified']) && $_GET['verified'] !== "") || (isset($_GET['enabled']) && $_GET['enabled'] !== "") || (isset($_GET['abused']) && $_GET['abused'] !== "") || (isset($_GET['status']) && $_GET['status'] !== ""))
{
	$search_desc .= " that are";
	if (isset($_GET['verified']) && $_GET['verified'] !== "")
	{
		$whereA[] = "verified = '$_GET[verified]'";
		$search_desc .= " <b>" . ($_GET['verified']?"":"not ") . " verified</b>";
		$glue = ",";
	}
	if (isset($_GET['enabled']) && $_GET['enabled'] !== "")
	{
		$whereA[] = "a.enabled = '$_GET[enabled]'";
		$search_desc .= "$glue <b>" . ($_GET['enabled']?"":"not ") . "approved</b>";
		$glue = ",";
	}
	if (isset($_GET['abused']) && $_GET['abused'] !== "")
	{
		$whereA[] = "abused " . ($_GET['abused']?"> 0":"= 0");
		$search_desc .= "$glue <b> with" . ($_GET['abused']?"":"out") . " abuse reports</b>";
		$glue = ",";
	}
	if (isset($_GET['status']) && $_GET['status'] !== "")
	{
		$whereA[] = "expireson " . ($_GET['status']?">= NOW()":"< NOW()");
		$search_desc .= "$glue <b>" . ($_GET['status']?"running":"expired") . "</b>";
		$glue = ",";
	}
}



if (isset($_GET['paid']) && $_GET['paid'] !== "") {	
	if (!$glue) {
		$search_desc .= " that are";
	}
	
	switch($_GET['paid']) {
		case 0: 
			$whereA[] = "paid = '0'"; 
			$search_desc .= "$glue <b>payment pending</b>";
			break;
		case 1: 
			$whereA[] = "paid = '1'"; 
			$search_desc .= "$glue <b>payment completed</b>";
			break;
		case 2: 
			$whereA[] = "paid = '2'"; 
			$search_desc .= "$glue <b>free posts</b>";
			break;
		case 3: 
			$whereA[] = "paid <> '0'"; 
			$search_desc .= "$glue <b>payment completed or free</b>";
			break;
	}
	
	$glue = ",";
}


$where = implode(" AND ", $whereA);
if (!$where) $where = 1; 

if ($search_desc) $search_desc = "Showing <b>$ad_section</b> " . $search_desc;
else $search_desc = "Showing <b>all $ad_section</b>";



if ($_POST['approve'] && $_POST['ad'])
{
	$adlist = implode(" OR adid = ", $_POST['ad']);
	if ($adlist)
	{
		$adlist = "adid = " . $adlist;

		

		// For ads marked as spam, clear the flag.
		$sql = "UPDATE $table SET abused = 0 WHERE ($adlist) AND abused = $spam_indicator";
		mysql_query($sql);

	

		$sql = "UPDATE $table SET enabled = '1' WHERE $adlist";
		if(mysql_query($sql)) $msg = mysql_affected_rows() . " ad(s) have been approved";
		else $err = "Error approving ads";
	}
}

if ($_POST['suspend'] && $_POST['ad'])
{
	$adlist = implode(" OR adid = ", $_POST['ad']);
	if ($adlist)
	{
		$adlist = "adid = " . $adlist;
		$sql = "UPDATE $table SET enabled = '0' WHERE $adlist";
		if(mysql_query($sql)) $msg = mysql_affected_rows() . " ad(s) have been suspended";
		else $err = "Error suspending ads";
	}
}

if ($_POST['verify'] && $_POST['ad'])
{
	$adlist = implode(" OR adid = ", $_POST['ad']);
	if ($adlist)
	{
		$adlist = "adid = " . $adlist;
		$sql = "UPDATE $table SET verified = '1' WHERE $adlist";
		if(mysql_query($sql)) $msg = mysql_affected_rows() . " ad(s) have been verified";
		else $err = "Error verifying ads";
	}
}


if ($_POST['markpaid'] && is_array($_POST['ad'])) {
	$res = $paidCategoriesHelper->markAdPaid($_POST['ad']);
	if ($res) $msg = mysql_affected_rows() . " ad(s) have been marked paid";
	else $err = "Error marking ads as paid";
}

// BEGIN Multi Renew Addon

$post_renew = trim(htmlspecialchars($_POST['renew']));

if ($post_renew && $_POST['ad'])
{

	$renew_days = ($admin_renew_days*86400);

	$expiry = time()+$renew_days;

	$expiry_dt = date("Y-m-d H:i:s", $expiry);
	
	
	if ($admin_renew_cre_dt == 1)
	{
		$create_date = date("Y-m-d H:i:s", time());
		$update_create_dt = ", createdon = '$create_date'";
	}


	$adlist = implode(" OR adid = ", $_POST['ad']);
	if ($adlist)
	{
		$adlist = "adid = " . $adlist;
		$sql = "UPDATE $table SET expireson = '$expiry_dt' $update_create_dt WHERE $adlist";
		//echo $sql; // testing
		if(mysql_query($sql)) $msg = mysql_affected_rows() . " ad(s) have been renewed";
		else $err = "Error renewing ads";
	}
}

// END Multi Renew Addon


if ($_POST['resetabuse'] && $_POST['ad'])
{
	
	$adlist = implode(" OR adid = ", $_POST['ad']);
	if ($adlist)
	{
		$adlist = "adid = " . $adlist;
		
		
		
		// For ads marked as spam, approve as well.
		$sql = "UPDATE $table SET enabled = '1' WHERE ($adlist) AND abused = $spam_indicator";
		mysql_query($sql);
		
	
		
		$sql = "UPDATE $table SET abused = 0 WHERE $adlist";
		if(mysql_query($sql)) $msg = "Abuse reports has been reset on " . mysql_affected_rows() . " ad(s)";
		else $err = "Error resetting abuse reports";
	}

}


if ($_POST['blockip'] && $_POST['ad'])
{
    if (!$demo) {
    	$adlist = implode(" OR adid = ", $_POST['ad']);
    	if ($adlist)
    	{
        	$userip = IPVal($_SERVER['REMOTE_ADDR']);
            $blockcount = 0;
            
    		$adlist = "adid = " . $adlist;
    		$sql = "SELECT ip FROM $table WHERE $adlist";
    		$res = mysql_query($sql);
    		
    		while ($row=mysql_fetch_array($res)) {
    
    			if(!preg_match("/([0-9]{1,3}\.){3}[0-9]{1,3}/", $row['ip'])) {
        			$err .= "$row[ip]: Invalid IP address format<br>";
        			
        		} else {
        		    $ipval = IPVal($row['ip']);
        		    
        		    if ($userip == $ipval) {
    				    $err .= "$row[ip]: You can not block your own IP address<br>";
    				
        			} else {
        				$sql = "INSERT INTO $t_ipblock 
        						SET ipstart = $ipval, ipend = $ipval";
        				$ret = mysql_query($sql);
        				
        				if ($ret) $blockcount++;
        				else $err .= $sql . " - " . mysql_error() . "<br>";
        			}
        		}
    		}
    		
    		if($blockcount) $msg = "$blockcount IP(s) added to the blacklist";
    	}
    	
    } else {
        $err = "IP blocking disabled in demo.";
    }
}


if ($_POST['del'] && $_POST['ad'])
{
	$d = 0;
	$nd = 0;
	foreach ($_POST['ad'] as $adid)
	{
		$sql = "DELETE FROM $table WHERE adid = '$adid'";
		if (mysql_query($sql) && mysql_affected_rows()) $d++;
		else $nd++;

		// Delete pics, extra fields and payment details
		$sql = "SELECT picfile FROM $t_adpics WHERE adid = '$adid' AND isevent = '$isevent'";
		$res = mysql_query($sql) or die(mysql_error().$sql);

		while($row = mysql_fetch_array($res))
		{
			if(is_file("../{$datadir[adpics]}/$row[picfile]")) unlink("../{$datadir[adpics]}/$row[picfile]");
		}

		$sql = "DELETE FROM $t_adpics WHERE adid = '$adid' AND isevent = '$isevent'";
		mysql_query($sql) or die(mysql_error().$sql);

		$sql = "DELETE FROM $t_promos_featured WHERE adid = '$adid' AND adtype = '$adtype'";
		mysql_query($sql) or die(mysql_error().$sql);

		$sql = "DELETE FROM $t_promos_extended WHERE adid = '$adid' AND adtype = '$adtype'";
		mysql_query($sql) or die(mysql_error().$sql);

		if ($adtype == 'A')
		{
			$sql = "DELETE FROM $t_adxfields WHERE adid = '$adid'";
			mysql_query($sql) or die(mysql_error().$sql);
		}	
	}

	$msg = "$d ad(s) have been deleted";
	if($nd) $err = "$nd ad(s) could not be deleted";
} 

if ($_POST['resend_links'] && $_POST['ad']) {

    if ($demo) {
        $err = "Feature disabled in demo";
    } 
    else {
    
		$idlist = implode(",", $_POST['ad']);
		$sql = "select * from $table where adid in ($idlist)";
		$res = mysql_query($sql);
		
		$mail_done = "";
		$mail_error = "";
		
		while ($ad = mysql_fetch_array($res)) {

			$data = $ad;        
			$adid = $ad["adid"];
			$xcityid = $ad["cityid"];
			$codemd5 = md5($ad['code']);
			
			$sql = "select catid from $t_subcats where subcatid = $ad[subcatid]";
			list($catid) = mysql_fetch_row(mysql_query($sql));
			
			// Compose the msg and mail the activation link
			$mail = file_get_contents("{$path_escape}mailtemplates/newpost.txt");
			$mail = str_replace("{@SITENAME}", $site_name, $mail);
			$mail = str_replace("{@SITEURL}", $script_url, $mail);
			$mail = str_replace("{@ADTITLE}", $ad['adtitle'], $mail);

			// Get expiry
			if ($isevent) 
			{
				$expireafter = $expire_events_after;
			}
			else
			{
				$sql = "SELECT expireafter FROM $t_subcats WHERE subcatid = $ad[subcatid]";
				list($expireafter) = mysql_fetch_array(mysql_query($sql));
			}
			$mail = str_replace("{@EXPIREAFTER}", $expireafter, $mail);
			$mail = str_replace("{@EXPIRESON}", substr($ad["expireson"], 0, 10), $mail);

            
            $adtype_long = ($adtype == "E" ? "event" : "ad");
			$verificationlink = "$script_url/?view=activate&type=$adtype_long&adid=$adid&codemd5=$codemd5&cityid=$xcityid";
		
			$mail = str_replace("{@VERIFICATIONLINK}", $verificationlink, $mail);

			if($isevent)
			{
				if($sef_urls) $adlink = "$script_url/{$vbasedir}$xcityid/events/$starton/$adid.html";
				else $adlink = "$script_url/?view=showevent&adid=$adid&cityid=$xcityid";
			}
			else
			{
				if($sef_urls) $adlink = "$script_url/{$vbasedir}$xcityid/posts/$catid/$ad[subcatid]/$adid.html";
				else $adlink = "$script_url/?view=showad&adid=$adid&cityid=$xcityid";
			}

			$mail = str_replace("{@ADURL}", $adlink, $mail);

			$editlink = "$script_url/?view=edit&isevent=$isevent&adid=$adid&codemd5=$codemd5&cityid=$xcityid";
			$mail = str_replace("{@EDITURL}", "$editlink", $mail);

			$subj = $lang['MAILSUBJECT_NEW_POST'];
			$subj = str_replace("{@ADTITLE}", $data['adtitle'], $subj);
		
			if (@sendMail($ad['email'], $subj, $mail, $site_email, $langx['charset']))
			
			{
			    
				$mail_done .= "- {$adtype}{$adid} by " . $ad['email'] . " - <a href=\"{$verificationlink}\" target=\"blank\">Verification Link</a> - <a href=\"{$editlink}\" target=\"blank\">Edit Link</a><br>";
				
			} else {
			    
				$mail_error .= "- {$adtype}{$adid} by " . $ad['email'] . " - <a href=\"{$verificationlink}\" target=\"blank\">Verification Link</a> - <a href=\"{$editlink}\" target=\"blank\">Edit Link</a><br>";
			
			}
		}
    }

    if ($mail_done) $msg = "Mail has been sent for the following posts:<br>" . $mail_done;
    if ($mail_error) $err = "Could not send mail for the following posts:<br>" . $mail_error;
}

$_GET['msg'] = stripslashes($_GET['msg']);
if (!$msg) {
    $msg = $_GET['msg'];
    $msg = stripslashes($msg);
}
unset($_GET['msg']);

$thisurl = "ads.php?";
foreach ($_GET as $k=>$v) $thisurl .= "$k=".urlencode($v)."&";


?>
<?php include_once("aheader.inc.php"); ?>
<h2>Manage <?php echo $_GET['subcatid']==-1?"Events":"Ads"; ?></h2>

<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<form class="box" action="" method="get" style="width: 200px;">	
<b>Get ad by ID:</b> <?php echo $adtype; ?> <input type="text" size="5" name="adid">
<input type="hidden" name="adtype" value="<?php echo $adtype; ?>">
<button type="submit">Get</button>
</form>

<form class="box" action="" method="get">

<table cellspacing="5">

<tr>
<td>
Search: 
</td>

<td>
<input type="text" name="search" size="20" value="<?php echo $_GET['search']; ?>">
</td>


<td>
Verified:
</td>

<td>
<select name="verified">
<option value="">- All -</option>
<option value="1" <?php if($_GET['verified'] === "1") echo "selected"; ?>>Yes</option>
<option value="0" <?php if($_GET['verified'] === "0") echo "selected"; ?>>No</option>
</select>
</td>

<td>Status:</td>
<td>
<select name="status">
<option value="">- All -</option>
<option value="1" <?php if($_GET['status'] === "1") echo "selected"; ?>>Running</option>
<option value="0" <?php if($_GET['status'] === "0") echo "selected"; ?>>Expired</option>
</select>
</td>

<td>Sort by:</td>
<td>
<select name="sortby">
<?php
foreach ($sort_field_names as $k=>$v) {
	if ($k == 0) continue;
	
	if ($k == $_GET['sortby']) $selected = "selected";
	else $selected = "";
?>
<option value="<?php echo $k; ?>" <?php echo $selected; ?>><?php echo $v; ?></option>
<?php
}
?>
</select>
</td>


<td rowspan="4">
<div style="border-left: 1px dashed #B1C7DE; margin-left: 20px; padding-left: 25px;">
<br><br>
<select onchange="location.href='?<?php if($_GET['subcatid'] == -1) echo "subcatid=-1&"; ?>' + this.value;">
<option value="">- OR select a quick view -</option>
<option value="">All <?php echo $ad_section; ?></option>
<option value="abused=1">Spam/Abuse reported</option>
<option value="enabled=0">Pending admin approval</option>
<option value="verified=0">Pending email verification</option>
<option value="enabled=1&verified=1&status=1">Currently running</option>
<option value="status=0">Expired</option>
</select>
<br><br><br>
</div>
</td>


</tr>

<tr>

<td>
Posted by:
</td>
<td>
<input type="text" name="email" size="20" value="<?php echo $_GET['email']; ?>">
</td>

<td>Approved:</td>
<td>
<select name="enabled">
<option value="">- All -</option>
<option value="1" <?php if($_GET['enabled'] === "1") echo "selected"; ?>>Yes</option>
<option value="0" <?php if($_GET['enabled'] === "0") echo "selected"; ?>>No</option>
</select>
</td>

<td>Spam/Abuse Reports:</td>
<td>
<select name="abused">
<option value="">- All -</option>
<option value="1" <?php if($_GET['abused'] === "1") echo "selected"; ?>>Yes</option>
<option value="0" <?php if($_GET['abused'] === "0") echo "selected"; ?>>No</option>
</select>
</td>

<td>Sort order:</td>
<td>
<select name="sortdir">
<option value="0" <?php if($_GET['sortdir'] == "0") echo "selected"; ?>>Descending</option>
<option value="1" <?php if($_GET['sortdir'] == "1") echo "selected"; ?>>Ascending</option>
</select>
</td>

</tr>


<tr>

<td>
City: 
</td>

<td>

<select name="cityid">
<option value="0">- All Cities -</option>
<?php

$sql = "SELECT countryid, countryname
		FROM $t_countries
		ORDER BY countryname";
$res = mysql_query($sql);

while ($row=mysql_fetch_array($res))
{
	$sql = "SELECT cityid, cityname
			FROM $t_cities ct
			WHERE countryid = $row[countryid]
			ORDER BY cityname";
	$resct = mysql_query($sql);

	if (mysql_num_rows($resct))
	{
		echo "<optgroup label=\"$row[countryname]\">\r\n";

		while ($ct = mysql_fetch_array($resct))
		{
			echo "<option value=\"$ct[cityid]\"";
			if ($_GET['cityid'] == $ct['cityid'])
			{
				$country = $row['countryname'];
				$city = $ct['cityname'];
				echo " selected"; 
			}
			echo ">$ct[cityname]</option>\r\n";
		}

		echo "</optgroup>\r\n";
	}
}

?>
</select>
</td>


<?php
if($_GET['subcatid'] == -1)
{
?>


<td colspan="6">
<input type="hidden" name="subcatid" value="-1">
</td>


<?php
}
else
{
?>
<td>
Category: 
</td>

<td>
<select name="subcatid">
<option value="0" <?php if(!$_GET['subcatid']) echo "selected"; ?>>- All Categories -</option>
<?php

$sql = "SELECT catid, catname
		FROM $t_cats
		ORDER BY catname";
$res = mysql_query($sql);

while ($row=mysql_fetch_array($res))
{
	$sql = "SELECT subcatid, subcatname
			FROM $t_subcats
			WHERE catid = $row[catid]
			ORDER BY subcatname";

	$ress = mysql_query($sql);

	if (mysql_num_rows($ress))
	{
		echo "<optgroup label=\"$row[catname]\">";
		while ($s = mysql_fetch_array($ress))
		{
			echo "<option value=\"$s[subcatid]\"";
			if ($_GET['subcatid'] == $s['subcatid'])
			{
				echo " selected"; 
				$cat = $row['catname']; 
				$subcat = $s['subcatname'];
			}
			echo ">$s[subcatname]</option>";
		}

		echo "</optgroup>";
	}
}

?>
</select>
</td>





<?php
}
?>

<?php 

if ($ad_section == "ads") {
?>
<td>Paid:</td>
<td>
<select name="paid">
<option value="">- All -</option>
<option value="1" <?php if($_GET['paid'] === "1") echo "selected"; ?>>Payment completed</option>
<option value="0" <?php if($_GET['paid'] === "0") echo "selected"; ?>>Payment pending</option>
<option value="2" <?php if($_GET['paid'] === "2") echo "selected"; ?>>Free Posts</option>
<option value="3" <?php if($_GET['paid'] === "3") echo "selected"; ?>>Paid or Free</option>
</select>
</td>
<?php 
}

?>

</tr>


<tr>
<td>&nbsp;</td>
<td colspan="7">	
<button type="submit">&nbsp;Go&nbsp;</button>
<button type="button" onclick="location.href='?<?php if ($ad_section == "events") echo "subcatid=-1"; ?>';">View All</button>
</td>
</tr>

</table>

</form>


<?php

if ($where)
{

	$page = $_REQUEST['page'] ? $_REQUEST['page'] : 1;
	$offset = ($page-1) * $admin_ads_per_page;

	$sql = "SELECT COUNT(*)
			FROM $table a
			WHERE $where$acc_sql";

	list($total) = mysql_fetch_row(mysql_query($sql));


?>

<div class="search_desc">
<?php echo $search_desc; ?>
<div><span class="rescount"><?php echo $total; ?></span> <?php echo $ad_section; ?> in this view</div>
</div><br>

<table><tr><td>Page:</td>
<td>
<?php

	$qsA = $_GET; unset($qsA['page'],$qsA['del']); $qs = "";
	foreach ($qsA as $k=>$v) $qs .= "&$k=$v";
	
	$url = "?page={@PAGE}&$qs";
	$pager = new pager($url, $total, $admin_ads_per_page, $page);

	$pager->outputlinks();

?>
</td></tr></table>

<br>

<script language="javascript">
function checkall(state)
{
	var n = frmAds.elements.length;
	for (i=0; i<n; i++)
	{
		if (frmAds.elements[i].name == "ad[]") frmAds.elements[i].checked = state;
	}
}
</script>
<?php /* START mod-paid-categories */ ?>
<div class="legend" align="right"><b>V</b> - Email Verified &nbsp; <b>A</b> - Approved &nbsp; <b>P</b> - Paid</div> 
<?php /* END mod-paid-categories */ ?>
<form method="post" action="" name="frmAds">


<table width="100%" cellpadding="6" cellspacing="1" class="grid">
	<tr>
		<td class="gridhead" width="5%" align="center">ID</td>
		<td class="gridhead">Title/<?php if($subcatid==-1) echo "Event Dates"; else echo "Category"; ?></td>
		<td class="gridhead" width="15%">Email / IP</td>
		<td class="gridhead" align="center" width="12%">City</td>
		<td class="gridhead" width="5%" align="center">Hits</td>
		<td class="gridhead" width="9%" align="center">Date</td>
		<td class="gridhead" width="9%" align="center">Expiry</td>
		<td class="gridhead" width="2%" align="center">V</td>
		<td class="gridhead" width="2%" align="center">A</td>
		<?php /* START mod-paid-categories */ ?>
		<td class="gridhead" width="2%" align="center">P</td>
		<?php /* END mod-paid-categories */ ?>
		<td class="gridhead" width="3%" align="center">&nbsp;</td>
		<td class="gridhead" width="3%" align="center">
		<input type="checkbox" onclick="javascript:checkall(this.checked);"></td>
	</tr>

<?php

	$sql = "SELECT a.*, UNIX_TIMESTAMP(a.createdon) AS createdon, UNIX_TIMESTAMP(a.expireson) AS expireson, COUNT(*) AS piccount, p.adid AS haspics, ct.cityname, ct.cityid, cy.countryname, cy.countryid, feat.adid AS isfeatured
				$extra_fields
			FROM $table a
				LEFT OUTER JOIN $t_cities ct ON a.cityid = ct.cityid
				LEFT OUTER JOIN $t_countries cy ON ct.countryid = cy.countryid
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '$isevent'
				LEFT OUTER JOIN $t_featured feat ON feat.adid = a.adid AND feat.adtype = '$adtype' AND feat.featuredtill > NOW()
				$extra_join
			WHERE $where$acc_sql
			GROUP BY a.adid
			ORDER BY $order
			LIMIT $offset, $admin_ads_per_page";
	$res = mysql_query($sql) or die($sql.mysql_error());

	$i = 0;
	while ($row = @mysql_fetch_array($res))
	{
		$i++;
		$cssalt = ($i%2 ? "" : "alt");

?>

		<tr height="55" class="gridcell<?php if($row['expireson']<time()) echo "expired"; ?><?php echo $cssalt; ?>">

		<td align="center">
		<?php echo ($_GET['subcatid']==-1?"E":"A"); ?><?php echo $row['adid']; ?>
		</td>

		<td>
		
		<a class="postlink" href="../index.php?view=<?php echo $ad_view; ?>&adid=<?php echo $row['adid']; ?>&cityid=<?php echo $row['cityid']; ?>" target="_blank"><?php echo $row['adtitle']; ?></a>
		
		<?php if($row['haspics']) { ?><img src="images/adwithpic.gif" align="absmiddle" title="<?php echo $row['piccount']; ?> Pictures"><?php } ?>

		<?php if($row['isfeatured']) { ?><img src="images/featured.gif" align="absmiddle" title="Featured Ad"><?php } ?>

	
		<?php if($row['abused'] == $spam_indicator) { ?>
		<span class="spam" title="Use the [Not Spam/Abuse] button to clear the flag and approve this post.">Spam?</span>
		<?php } else if($row['abused'] > 0) { ?>
		<span class="abuse_marker"><?php echo $row['abused']; ?> abuse report(s)</span>
		<?php } ?>
	
		
		<div class="adextra">

		<?php
		if($subcatid==-1)
		{
			echo date("M d, y", $row['starton']);
			if ($row['endon'] != $row['starton']) echo " - ".date("M d, y", $row['endon']);
		}
		else
		{
			echo "$row[catname] // $row[subcatname]";
		}
		?>

		</div>

		

		</td>

		<td>
		<a href="mailto:<?php echo $row['email']; ?>?subject=<?php echo $row['adtitle']; ?>"><?php echo $row['email']; ?></a><br><br>
		<?php echo $row['ip']; ?>
		</td>

		<td align="center">
		<?php echo "$row[cityname]"; ?></td>

		<td align="center">
		<?php echo "$row[hits]"; ?></td>

		<td align="center"><?php echo date("M d, Y", $row['createdon']); ?><br>
		<?php echo date("h:i a", $row['createdon']); ?>
		</td>

		<td align="center"><?php echo date("M d, Y", $row['expireson']); ?><br><br> 
		<?php //echo date("H:i:s", $row['expireson']); ?></td>

		<td align="center">
		<?php if($row['verified']) echo "<span class=\"yes\">+</span>"; 
		else echo "<span class=\"no\">X</span>"; ?></td>

		<td align="center">
		<?php if($row['enabled']) echo "<span class=\"yes\">+</span>"; 
		else echo "<span class=\"no\">X</span>"; ?></td>
		
		<?php /* START mod-paid-categories */ ?>
		<td align="center">
		<?php 
		switch($row['paid']) {
			case 0:  echo "<span class=\"no\">X</span>"; break;
			case 1:  echo "<span class=\"yes\">+</span>"; break;
			case 2:  echo "<span class=\"yes\">NA</span>"; break;
		}
		?>
		</td>
		

		<td align="center">
		<a href="editad.php?adid=<?php echo $row['adid']; ?>&isevent=<?php echo $isevent; ?>&returl=<?php echo rawurlencode($thisurl); ?>">
		<img src="images/edit.gif" border="0"></a>
		</td>
		
		<td align="center">
		<input type="checkbox" name="ad[]" value="<?php echo $row['adid']; ?>">
		</td>
		</tr>


<?php
	}
?>

<tr>

<td colspan="10" align="right">

<input type="submit" name="verify" value="Verify" class="button">
<input type="submit" name="approve" value="Approve" class="button">
<?php /* START mod-paid-categories */ ?>
<input type="submit" name="markpaid" value="Mark Paid" class="button">
<?php /* END mod-paid-categories */ ?>

<input type="submit" name="resetabuse" value="Not Spam/Abuse" class="button">
<input type="submit" name="renew" value="Renew" class="button" onclick="return(confirm('Renew selected posts?'));">
<input type="submit" name="resend_links" value="Resend Links" class="button">
<input type="submit" name="blockip" value="Block IP" class="cautionbutton">

<input type="submit" name="suspend" value="Suspend" class="cautionbutton" onclick="return(confirm('Suspend selected posts?'));">
<input type="submit" name="del" value="Delete" class="cautionbutton" onclick="return(confirm('Delete selected posts?'));">
</td>
</tr>

</table>
</form>

<?php

}
else
{

?>

<div class="info">
Please select a city and category from the list and an optional search term to view ads
</div>

<?php
}
?>
<?php include_once("afooter.inc.php"); ?>