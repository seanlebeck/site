<?php

require_once("admin.inc.php");
require_once("aauth.inc.php");
/* START mod-paid-categories */
require_once("../paid_cats/paid_categories_helper.php");
/* END mod-paid-categories */


$_POST['do'] = strtolower($_POST['do']);
$_GET['do'] = strtolower($_GET['do']);
$_REQUEST['do'] = strtolower($_REQUEST['do']);


if ($_POST['do'] == "save")
{
	if ($_POST['catname'])
	{
		$_POST['enabled'] = 0+$_POST['enabled'];
		$_POST['alert'] = 0+$_POST['alert'];

		recurse($_POST['alertdesc'], 'htmlspecialchars');
		recurse($_POST['alertdesc'], 'mysql_escape_string');

		if($_POST['catid'])
		{
			$sql = "UPDATE $t_cats
					SET catname = '$_POST[catname]',
						enabled = '$_POST[enabled]',
						alert = '$_POST[alert]',
						alerttitle = '$_POST[alerttitle]',
						alertdesc = '$_POST[alertdesc]'
					WHERE catid = $_POST[catid]";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Category saved";
		}
		else
		{
			$sql = "INSERT INTO $t_cats
					SET catname = '$_POST[catname]',
						enabled = '$_POST[enabled]',
						alert = '$_POST[alert]',
						alerttitle = '$_POST[alerttitle]',
						alertdesc = '$_POST[alertdesc]'";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Category saved";

			$sql = "SELECT catid FROM $t_cats WHERE catid = LAST_INSERT_ID()";
			list($newcatid) = mysql_fetch_array(mysql_query($sql));

			$sql = "UPDATE $t_cats SET pos = $newcatid WHERE catid = $newcatid";
			mysql_query($sql);

		}
		
	
		$fee_catid = $_POST['catid'] ? $_POST['catid'] : $newcatid;
		$paidCategoriesHelper->saveFeeInfo($fee_catid, 1, $_POST);

		
	}
	/*elseif ($_POST['subcatname'])
	{
		$pricelabel = $_POST['hasprice'] ? ($_POST['pricelabel'] ? $_POST['pricelabel'] : "Price") : "";

		if ($_POST['subcatid'])
		{
			$sql = "DELETE FROM $t_subcatxfields WHERE subcatid = $_POST[subcatid]";
			mysql_query($sql);

			$sql = "UPDATE $t_subcats
					SET subcatname = '$_POST[subcatname]',
						catid = $_POST[catid],
						hasprice = '$_POST[hasprice]',
						pricelabel = '$pricelabel',
						expireafter = $_POST[expireafter],
						enabled = '$_POST[enabled]'
					WHERE subcatid = $_POST[subcatid]";
		}
		else
		{
			$sql = "INSERT INTO $t_subcats
					SET subcatname = '$_POST[subcatname]',
						catid = $_POST[catid],
						hasprice = '$_POST[hasprice]',
						pricelabel = '$pricelabel',
						expireafter = $_POST[expireafter],
						enabled = '$_POST[enabled]'";
		}

		mysql_query($sql) or die(mysql_error().$sql);
		if (mysql_affected_rows()) $msg = "Subcategory saved";

		if ($_POST['subcatid'])
		{
			$subcatid = $_POST['subcatid'];
		}
		else
		{
			$sql = "SELECT LAST_INSERT_ID() FROM $t_subcats";
			list($subcatid) = mysql_fetch_array(mysql_query($sql));
		}

		// Custom fields
		if (count($_POST['xfields']))
		{
			foreach ($_POST['xfields'] as $fldnum=>$fld)
			{
				if(!$fld['NAME']) continue;

				$fld['SHOWINLIST'] = 0+$fld['SHOWINLIST'];
				$fld['SEARCHABLE'] = 0+$fld['SEARCHABLE'];
				$fld['VALUES'] = xStripSlashes($fld['VALUES']);

				$sql = "INSERT INTO $t_subcatxfields
						SET subcatid = $subcatid,
							fieldnum = $fldnum,
							name = '$fld[NAME]',
							type = '$fld[TYPE]',
							vals = '$fld[VALUES]',
							showinlist = '$fld[SHOWINLIST]',
							searchable = '$fld[SEARCHABLE]'";
				mysql_query($sql) or die($sql.mysql_error());
				if (mysql_affected_rows()) $msg = "Subcategory saved";
			}
		}
	}*/
}
elseif ($_GET['do'] == "delete")
{
	if ($_GET['catid'])	
	{
		// Delete ads in each city in country
		$sql = "SELECT subcatid FROM $t_subcats WHERE catid = $_GET[catid]";
		$res = mysql_query($sql) or die(mysql_error().$sql);
		$adsdeleted = 0;
		
	
		$subcatlist = "";
		$adlist = "";
	

		while ($c=mysql_fetch_array($res))
		{
			
			
			// Add the subcatid as well as the child ad ids to a list so that
			// we can delete the extra field information later in one go. 
			// Doing it the hard way for compatibility with MySQL 3.23.
			
			// Subcat id
			$subcatlist .= "$c[subcatid],";
			
			// Ad ids
			$sql = "SELECT adid FROM $t_ads WHERE subcatid = $c[subcatid]";
			$ad_res = mysql_query($sql) or die(mysql_error().$sql);
			
			while($ad = mysql_fetch_array($ad_res)) {
				$adlist .= "$ad[adid],";
			}
			
		
			
			$sql = "DELETE FROM $t_ads WHERE subcatid = $c[subcatid]";
			mysql_query($sql) or die(mysql_error().$sql);
			$adsdeleted += mysql_affected_rows();
			
			
			$paidCategoriesHelper->deleteFeeInfo($c['subcatid'], 2);
		
			
		}
		
	
		
		// Delete subcat extra field definitions
		if ($subcatlist) {
			$subcatlist = substr($subcatlist, 0, -1);
			$sql = "DELETE FROM $t_subcatxfields WHERE subcatid IN ($subcatlist)";
			mysql_query($sql) or die(mysql_error().$sql);
		}
		
		// Delete extra field values for ads.
		if ($adlist) {
			$adlist = substr($adlist, 0, -1);
			$sql = "DELETE FROM $t_adxfields WHERE adid IN ($adlist)";
			mysql_query($sql) or die(mysql_error().$sql);
		}

	
		// Delete subcats
		$sql = "DELETE FROM $t_subcats WHERE catid = $_GET[catid]";
		mysql_query($sql) or die(mysql_error().$sql);
		$subcatsdeleted = mysql_affected_rows();
		
		// Delete cat
		$sql = "DELETE FROM $t_cats WHERE catid = $_GET[catid]";
		mysql_query($sql) or die(mysql_error().$sql);

		if (mysql_affected_rows()) $msg = "Category deleted";
		//else $err = "Cannot delete category";
		
	
		$paidCategoriesHelper->deleteFeeInfo($_GET['catid'], 1);
	

	}

	/*elseif ($_GET['subcatid'])
	{
		// Delete ads
		$sql = "DELETE FROM $t_ads WHERE subcatid = $_GET[subcatid]";
		mysql_query($sql) or die(mysql_error().$sql);
		$adsdeleted = mysql_affected_rows();

		// Delete subcat
		$sql = "DELETE FROM $t_subcats WHERE subcatid = '$_GET[subcatid]'";

		mysql_query($sql) or die(mysql_error().$sql);
		if (mysql_affected_rows()) $msg = "Subcategory deleted";
		//else $err = "Cannot delete subcategory";
	}*/
}
elseif ($_GET['do'] == "move")
{
	if ($_GET['catid'])
	{

		$catid = $_GET['catid'];
		$direction = $_GET['direction'];
		
		$sql = "SELECT pos FROM $t_cats WHERE catid = $_GET[catid]";
		list($curpos) = mysql_fetch_array(mysql_query($sql));

		// Find new position
		if ($direction > 0)
		{
			// To be moved up
			$sql = "SELECT pos FROM $t_cats WHERE pos < $curpos ORDER BY pos DESC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}
		else
		{
			// To be moved down
			$sql = "SELECT pos FROM $t_cats WHERE pos > $curpos ORDER BY pos ASC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}

		// Exchange now
		if ($newpos)
		{
			$sql = "UPDATE $t_cats SET pos = $curpos WHERE pos = $newpos";
			mysql_query($sql);

			$sql = "UPDATE $t_cats SET pos = $newpos WHERE catid = $catid";
			mysql_query($sql);

			if (!mysql_error() && mysql_affected_rows()) $msg = "Category moved";

		}
	}
}

?>
<?php include_once("aheader.inc.php"); ?>
<?php
if ($_GET['do'] == "edit" || $_GET['do'] == "add")
{
	if ($_GET['type'] == "cat")
	{
		if ($_GET['catid'])
		{
			$sql = "SELECT * FROM $t_cats WHERE catid = '$_GET[catid]'";
			$thisitem = mysql_fetch_assoc(mysql_query($sql));
			
			if (!$thisitem)
			{
				echo "ERROR! Category not found";
				exit;
			}
		}
?>
<h2>Add/Edit Category</h2>
<form class="box" name="frmCat" action="?" method="post" class="box">
<table border="0">
<tr>
<td><b>Category:</b></td>
<td><input type="text" name="catname" size="35" value="<?php echo $thisitem['catname']; ?>"></td>
</tr>
<tr>
<td><b>Enabled:</b></td>
<td><input type="checkbox" name="enabled" value="1" <?php if($thisitem['enabled'] == 1 || !$thisitem) echo "checked"; ?>></td>
</tr>

<!-- BEGIN Vivaru Adult Warning Mod -->
<tr>
<td><b>Alert Enabled:</b></td>
<td><input type="checkbox" name="alert" value="1" <?php if($thisitem['alert'] == 1 || !$thisitem) echo "checked"; ?>></td>
</tr>
<tr>
<td><b>Alert Title:</b></td>
<td><input type="text" name="alerttitle" size="35" value="<?php echo $thisitem['alerttitle']; ?>"></td>
</tr>
<tr>
<td colspan="2">
<table border="0" width="70%" cellpadding="0">
<td><b>Alert Notification:</b></td>
<tr><td>
	<?php
	

        if(richTextAllowed(time())) 
		{
            $wmd_editor = array("name"=>"alertdesc", "content"=>$thisitem['alertdesc']);
            include("{$path_escape}editor/wmd_editor.inc.php");

        } 
	else 
	{
        ?>

         <textarea name="alertdesc" cols="90" rows="10" id="alertdesc"><?php echo $thisitem['alertdesc']; ?></textarea><br>

        <?php
        }

     
        ?>

</td></tr>
</table>
</td>
</tr>
<!-- END Vivaru Adult Warning Mod -->

<?php /* START mod-paid-categories */ ?>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td style="vertical-align:top"><b>Posting fee:</b><br>
</td>
<td style="vertical-align:top">
<?php 
$feeSectionLevel = 1;
$feeSectionId = $_GET['catid'];
include("../paid_cats/admin/cat_fees.inc.php"); 
?>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php /* END mod-paid-categories */ ?>
<tr>
<td></td>
<td>
<input type="hidden" name="do" value="save">
<input type="hidden" name="type" value="cat">
<input type="hidden" name="catid" value="<?php echo $_GET['catid']; ?>">

<button type="submit" value="Save"> Save </button>
&nbsp;<button type="button" onclick="location.href='?';">Cancel</button>
</td>

</tr>
</table>
</form>


<img src="images/tip.gif" align="top">
<a href="javascript:showHelp('postable_category');">
How to create a "postable" category?</a>

<div id="help_postable_category" style="display:none;width:500px;">
<ol>
    <li>Make sure $shortcut_categories is set to TRUE in the config file.</li>
    <li>Create your category.</li>
    <li>Create a single subcategory under this with the exact same name as the category.</li>
</ol>

<div style="margin-left:22px;">
This subcategory will not be visible to users and the category will act like it is postable. Every time a post is made to the category, it will be automatically routed to its lone subcategory.<br><br>
Note that you should not add more than one subcategory and the category and subcategory names should exactly be the same.
</div>
</div>


<?php
	}
}
else
{
?>
<h2>Manage Categories</h2>

<?php if($msg) { ?><div class="msg"><?php echo $msg; ?></div><?php } ?>
<?php if($err) { ?><div class="err"><?php echo $err; ?></div><?php } ?>

<p class="tip"><img src="images/tip.gif" border="0" align="absmiddle"> Click on a category name to edit the subcategories under that category</p>

<button name="add" type="button" onclick="javascript:location.href='?do=add&type=cat';" value="">Add New</button><br>
<div class="legend" align="right"><b>E</b> - Enabled</div>
<form method="post" action="" name="frmCats">
<table class="grid" cellspacing="1" cellpadding="6" width="100%">
	<tr class="gridhead">
		<td>Category</td>
		<td width="20" align="center">E</td>
		<td colspan="4" align="center" width="40">Actions</td>
	</tr>

<?php
$sql = "SELECT cat.catid, cat.catname, cat.enabled
		FROM $t_cats cat
		ORDER BY pos";
$res = mysql_query($sql) or die(mysql_error());

$i = 0;
while ($row=mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");
?>

	<tr class="gridcell<?php echo $cssalt; ?>">
		<td><a href="subcats.php?catid=<?php echo $row['catid']; ?>"><?php echo $row['catname']; ?></a></td>
		<td align="center"><?php if($row['enabled']) echo "<span class=\"yes\">+</span>"; 
		else echo "<span class=\"no\">X</span>"; ?></td>
		<td width="20" align="center"><a href="?do=move&direction=1&catid=<?php echo $row['catid']; ?>"><img src="images/up.gif" border="0" alt="Move Up" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=move&direction=-1&catid=<?php echo $row['catid']; ?>"><img src="images/down.gif" border="0" alt="Move Down" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=edit&type=cat&catid=<?php echo $row['catid']; ?>"><img src="images/edit.gif" border="0" alt="Edit" title="Edit"></a></td>
		<td width="20" align="center"><a href="javascript:if(confirm('Delete category?')) location.href = '?do=delete&type=cat&catid=<?php echo $row['catid']; ?>';"><img src="images/del.gif" border="0" alt="Delete" title="Delete"></a></td>
	</tr>

<?php
}
?>

</table>
</form>
<br>

<?php
}
?>
<?php include_once("afooter.inc.php"); ?>