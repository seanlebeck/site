<?php



require_once("admin.inc.php");
require_once("aauth.inc.php");


$thispageurl = $_SERVER['REQUEST_URI'] . "?" . $_SERVER['QUERY_STRING'];


if ($_POST['del'] && $_POST['img'])
{
	$d = 0;
	$nd = 0;
	foreach ($_POST['img'] as $imgid)
	{
		$sql = "SELECT imgfilename FROM $t_imgs WHERE imgid = $imgid";
		list($filename) = mysql_fetch_array(mysql_query($sql));
		if ($filename)
		{
			if (unlink("../userimgs/$filename")) $unlinkerr = FALSE;
			else  $unlinkerr = TRUE;
		}

		$sql = "DELETE FROM $t_imgcomments WHERE imgid = '$imgid'";
		mysql_query($sql);

		$sql = "DELETE FROM $t_imgs WHERE imgid = '$imgid'";
		if (mysql_query($sql) && mysql_affected_rows()) $d++;
		else $nd++;
	}

	$msg = "$d image(s) deleted";
	if($nd) $err = "$nd image(s) could not be deleted";
}

if ($_POST['approve'] && $_POST['img'])
{
	$a = 0;
	$na = 0;
	foreach ($_POST['img'] as $imgid)
	{
		$sql = "SELECT * FROM $t_imgs WHERE imgid = $imgid";
		$thisimg = mysql_fetch_array(mysql_query($sql));
		$code = $thisimg['code'];
		$codemd5 = md5($code);

		if (!$thisimg['enabled'])
		{
			$sql = "UPDATE $t_imgs SET enabled = '1' WHERE imgid = $imgid";
			mysql_query($sql);

			if(mysql_affected_rows())
			{
				/* NOT NEEDED COZ IMGS ARE AUTO-APPROVED AND DONT NEED APPROVAL EMAIL
				$mailmsg = file_get_contents("../mailtemplates/imgapproved.txt");
				$mailmsg = str_replace("{@SITENAME}", $site_name, $mailmsg);
				$mailmsg = str_replace("{@EXPIREAFTER}", $images_delete_after, $mailmsg);
				//$mailmsg = str_replace("{@PASSWORD}", $thisimg['password'], $mailmsg);

				$mailmsg = str_replace("{@IMAGETITLE}", $thisimg['imgtitle'], $mailmsg);

				$posterenc = EncryptPoster("IMG", $thisimg['postername'], $thisimg['posteremail']);
				
				if($sef_urls) $adlink = "$script_url/$vbasedir/{$thisimg[cityid]}/images/$posterenc/$imgid.html";
				else $adlink = "$script_url/?view=showimg&posterenc=$posterenc&imgid=$imgid&cityid={$thisimg[cityid]}";
				
				$mailmsg = str_replace("{@IMAGEURL}", $adlink, $mailmsg);

				$editlink = "$script_url/?view=editimg&imgid=$imgid&codemd5=$codemd5&cityid={$thisimg[cityid]}";
				$mailmsg = str_replace("{@DELETEURL}", $editlink, $mailmsg);


				if (!@xmail($thisimg['posteremail'], $lang['MAILSUBJECT_POST_APPROVED'], $mailmsg, $site_email))
				{
					$err = "Error sending approval mail";
					$mailerr = TRUE;

				}*/

				$a++;

			}
			else
			{
				$na++;
			}
		}
	}

	$msg = "$a image(s) approved";
	if($na) $err .= "<br>$na image(s) could not be approved";
}

if ($_POST['suspend'] && $_POST['img'])
{
	$imglist = implode(" OR imgid = ", $_POST['img']);
	if ($imglist)
	{
		$imglist = "imgid = " . $imglist;
		$sql = "UPDATE $t_imgs SET enabled = '0' WHERE $imglist";
		if(mysql_query($sql)) $msg = mysql_affected_rows() . " image(s) suspended";
		else $err = "Error suspending images";
	}
}

if ($_POST['verify'] && $_POST['img'])
{
	$imglist = implode(" OR imgid = ", $_POST['img']);
	if ($imglist)
	{
		$imglist = "imgid = " . $imglist;
		$sql = "UPDATE $t_imgs SET verified = '1' WHERE $imglist";
		if(mysql_query($sql)) $msg = mysql_affected_rows() . " ad(s) marked as email verified";
		else $err = "Error verifying images";
	}
}


$whereA = array();
if ($_GET['search']) 
{
	$whereA[] = "(i.imgtitle LIKE '%$_GET[search]%' OR i.imgdesc LIKE '%$_GET[search]%')";
	$search_desc .= " containing <b>'$_GET[search]'</b>";
}
if ($_GET['postedby']) 
{
	$whereA[] = "(i.postername LIKE '%$_GET[postedby]%' OR i.posteremail LIKE '%$_GET[postedby]%')";
	$search_desc .= " posted by <b>'$_GET[postedby]'</b>";
}
if ($_GET['cityid']) 
{
	$whereA[] = "i.cityid = $_GET[cityid]";
	$sql = "SELECT countryname,cityname FROM $t_cities ct INNER JOIN $t_countries c ON ct.countryid = c.countryid WHERE cityid=$_GET[cityid]";
	list($country, $city) = mysql_fetch_array(mysql_query($sql));
	$search_desc .= " from <b>$country > $city</b>";
}

$sort_fields = array("", "i.imgid", "i.imgtitle", "i.postername", "i.posteremail", "i.ip", "countryname", "cityname", "i.verified", "i.enabled", "i.createdon", "i.expireson");
$sort_field_names = array("", "ID", "Title", "Posted by", "Email", "IP", "Region", "City", "Verified", "Enabled", "Created On", "Expires On");
$sort_key = 0 + $_GET['sortby'];
$sort = $sort_fields[$sort_key];
$sort_dir = $_GET['sortdir'] == "1" ? "ASC" : "DESC";
$order = $sort ? "$sort $sort_dir, i.enabled ASC, i.timestamp DESC" : "i.enabled ASC, i.timestamp DESC";

if (isset($_GET['verified']) && $_GET['verified'] !== ""
		|| isset($_GET['enabled']) && $_GET['enabled'] !== ""
		|| isset($_GET['status']) && $_GET['status'] !== "")
{
	$search_desc .= " that are ";

	if (isset($_GET['verified']) && $_GET['verified'] !== "")
	{
		$whereA[] = "i.verified = '$_GET[verified]'";
		$search_desc .= "<b>" . ($_GET['verified']?"":"not")." verified</b>";
		$glue = ", ";
	}
	if (isset($_GET['enabled']) && $_GET['enabled'] !== "")
	{
		$whereA[] = "i.enabled = '$_GET[enabled]'";
		$search_desc .= "$glue<b>" . ($_GET['enabled']?"":"not")." approved</b>";
		$glue = ",";
	}
	if (isset($_GET['status']) && $_GET['status'] !== "")
	{
		$whereA[] = "i.expireson " . ($_GET['status']?">= NOW()":"< NOW()");
		$search_desc .= "$glue <b>" . ($_GET['status']?"running":"expired") . "</b>";
		$glue = ",";
	}

	$search_desc .= "</b>";
}


$where = implode(" AND ", $whereA);
if(!$where) $where = "1";
else $searchmode = 1;

if ($search_desc) $search_desc = "Showing <b>images</b>" . $search_desc;
else $search_desc = "Showing <b>all images</b>";

?>
<?php include_once("aheader.inc.php"); ?>
<h2>Manage Images</h2>

<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<form action="" method="get" class="box">

<table cellspacing="3">

<tr>
<td>Search: </td>
<td><input type="text" name="search" size="20" value="<?php echo $_GET['search']; ?>"></td>

<td>Verified:</td>
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


<td rowspan="3">
<div style="border-left: 1px dashed #B1C7DE; margin-left: 20px; padding-left: 25px;">
<br><br>
<select onchange="location.href='?<?php if($_GET['subcatid'] == -1) echo "subcatid=-1&"; ?>' + this.value;">
<option value="">- OR select a quick view -</option>
<option value="">All images</option>
<!--<option value="abused=1">Spam/Abuse reported</option>-->
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

<td>Posted by: </td>
<td><input type="text" name="postedby" size="20" value="<?php echo $_GET['postedby']; ?>"></td>

<td>Approved:</td>
<td>
<select name="enabled">
<option value="">- All -</option>
<option value="1" <?php if($_GET['enabled'] === "1") echo "selected"; ?>>Yes</option>
<option value="0" <?php if($_GET['enabled'] === "0") echo "selected"; ?>>No</option>
</select>
</td>

<td>City: </td>
<td>
<select name="cityid">
<option value="0">- All -</option>
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

<td>Sort order:</td>
<td>
<select name="sortdir">
<option value="0" <?php if($_GET['sortdir'] == "0") echo "selected"; ?>>Descending</option>
<option value="1" <?php if($_GET['sortdir'] == "1") echo "selected"; ?>>Ascending</option>
</select>
</td>


</tr>

<tr>

<td>&nbsp;</td>
<td colspan="3">
<button type="submit">&nbsp;Go&nbsp;</button>
<button type="button" onclick="location.href='?';">View All</button>
</td>

</tr>
</table>

</form>

<?php

$page = $_REQUEST['page'] ? $_REQUEST['page'] : 1;
$offset = ($page-1) * $admin_images_per_page;

$sql = "SELECT COUNT(*)
		FROM $t_imgs i
		WHERE $where";

list($total) = mysql_fetch_row(mysql_query($sql));

?>

<div class="search_desc">
<?php echo $search_desc; ?>
<div><span class="rescount"><?php echo $total; ?></span> images in this view</div>
</div><br>

<table cellpadding="0" cellspacing="0"><tr><td>Page:&nbsp;</td>
<td>
<?php

$qsA = $_GET; unset($qsA['page'],$qsA['del']); $qs = "";
foreach ($qsA as $k=>$v) $qs .= "&$k=$v";

$url = "?page={@PAGE}&$qs";
$pager = new pager($url, $total, $admin_images_per_page, $page);

$pager->outputlinks();

?>
</td></tr></table>

<br>

<script language="javascript">
function checkall(state)
{
	var n = frmImages.elements.length;
	for (i=0; i<n; i++)
	{
		if (frmImages.elements[i].name == "img[]") frmImages.elements[i].checked = state;
	}
}
</script>
<div class="legend" align="right"><b>V</b> - Verified &nbsp; <b>A</b> - Approved</div>
<form method="post" action="" name="frmImages">
<table width="100%" border="0" cellpadding="4" cellspacing="1" class="grid">
	<tr>
		<td class="gridhead" align="center" width="5%">ID</td>
		<td class="gridhead" width="13%" align="center">Image</td>
		<td class="gridhead">Title / Posted by / IP</td>
		<td class="gridhead" align="center" width="13%">City</td>
		<td class="gridhead" align="center" width="12%">Comments</td>
		<td class="gridhead" align="center" width="10%">Date</td>
		<td class="gridhead" align="center" width="10%">Expiry</td>
		<td class="gridhead" align="center" width="3%">V</td>
		<td class="gridhead" align="center" width="3%">A</td>
		<td class="gridhead" width="3%">
		<input type="checkbox" onclick="javascript:checkall(this.checked);"></td>
	</tr>

<?php

$sql = "SELECT i.*, UNIX_TIMESTAMP(i.createdon) AS createdon, UNIX_TIMESTAMP(i.expireson) AS expireson,
			ct.cityid, ct.cityname, c.countryid, c.countryname,
			COUNT(*) AS commentcount, ic.imgid AS hascomments
		FROM $t_imgs i
			LEFT OUTER JOIN $t_cities ct ON ct.cityid = i.cityid
			LEFT OUTER JOIN $t_countries c ON c.countryid = ct.countryid
			LEFT OUTER JOIN $t_imgcomments ic ON i.imgid = ic.imgid
		WHERE $where
		GROUP BY i.imgid
		ORDER BY $order
		LIMIT $offset, $admin_images_per_page";
$res = mysql_query($sql) or die($sql.mysql_error());

while ($row = @mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");

?>

	<tr class="gridcell<?php if($row['expireson']<time()) echo "expired"; ?><?php echo $cssalt; ?>" height="80">

	<td align="center">
	M<?php echo $row['imgid']; ?>
	</td>

	<td align="center">

	<a href="../index.php?view=showimg&imgid=<?php echo $row['imgid']; ?>&cityid=<?php echo $row['cityid']; ?>" target="_blank">
	<img src="../userimgs/<?php echo $row['imgfilename']; ?>" border="0" width="80" height="60" style="border:1px solid black;"></a>

	</td>

	<td>

	
	<a href="../index.php?view=showimg&imgid=<?php echo $row['imgid']; ?>" class="postlink" target="_blank">
	<?php echo $row['imgtitle']; ?></a>
	

	<?php /*if($admin_adpreview_chars) { ?>
	<span class="adpreview"><?php echo substr($row['imgdesc'], 0, $admin_adpreview_chars); ?>
	<?php if(strlen($row['imgdesc'])>$admin_adpreview_chars) echo "..."; ?></span>
	<?php }*/ ?>

	<br><br><?php echo $row['postername']; ?><br><a href="mailto:<?php echo $row['posteremail']; ?>"><?php echo $row['posteremail']; ?></a><br><?php echo $row['ip']; ?>
	</td>

	<td align="center">
	<?php echo $row['cityname']; ?>, <?php echo $row['countryname']; ?>
	</td>

	<td align="center">
	<?php if($row['hascomments']) { ?>
	<a href="imgcomments.php?imgid=<?php echo $row['imgid']; ?>&returl=<?php echo rawurlencode($thispageurl); ?>"><b><?php echo $row['commentcount']; ?></b> Comments</a>
	<?php } else { ?>
	-
	<?php } ?>
	</td>

	<td align="center"><?php echo date("M d, Y", $row['createdon']); ?><br><?php echo date("h:i a", $row['createdon']); ?></td>

	<td align="center"><?php echo date("M d, Y", $row['expireson']); ?><br><br><?php //echo date("H:i:s", $row['expireson']); ?></td>

	<td align="center"><?php if($row['verified']) echo "<span class=\"yes\">+</span>"; else echo "<span class=\"no\">X</span>"; ?></td>

	<td align="center"><?php if($row['enabled']) echo "<span class=\"yes\">+</span>"; else echo "<span class=\"no\">X</span>"; ?></td>

	<td align="center">
	<input type="checkbox" name="img[]" value="<?php echo $row['imgid']; ?>">
	</td>

	</tr>

<?php
}
?>

<tr>
<td colspan="10" align="right">With selected:&nbsp;
<input type="submit" name="verify" value="Verify" class="button">
<input type="submit" name="approve" value="Approve" class="button">
<input type="submit" name="suspend" value="Suspend" class="cautionbutton" onclick="return(confirm('Suspend selected posts?'));">
<input type="submit" name="del" value="Delete" class="cautionbutton" onclick="return(confirm('Delete selected posts?'));">
</td>
</tr>

</table>
</form>

<?php include_once("afooter.inc.php"); ?>