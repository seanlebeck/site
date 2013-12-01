<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");
/* START mod-paid-categories */
require_once("../paid_cats/paid_categories_helper.php");
/* END mod-paid-categories */


$_POST['do'] = strtolower($_POST['do']);
$_GET['do'] = strtolower($_GET['do']);
$_REQUEST['do'] = strtolower($_REQUEST['do']);

$catid = $_REQUEST['catid'];
if($catid)
{
	$sql = "SELECT catname FROM $t_cats WHERE catid = $catid";
	list($catname) = mysql_fetch_array(mysql_query($sql));
}



if ($_POST['do'] == "save")
{
	if ($_POST['subcatname'])
	{
		$_POST['hasprice'] = 0+$_POST['hasprice'];
		$pricelabel = $_POST['hasprice'] ? ($_POST['pricelabel'] ? $_POST['pricelabel'] : "Price") : "";
		$expireafter = $_POST['expireafter'] ? (0+$_POST['expireafter']) : $expire_ads_after_default;
		
		$_POST['enabled'] = 0+$_POST['enabled'];
		$_POST['alert'] = 0+$_POST['alert'];

		recurse($_POST['alertdesc'], 'htmlspecialchars');
		recurse($_POST['alertdesc'], 'mysql_escape_string');

		if ($_POST['subcatid'])
		{
			$sql = "DELETE FROM $t_subcatxfields WHERE subcatid = $_POST[subcatid]";
			mysql_query($sql);

			$sql = "UPDATE $t_subcats
					SET subcatname = '$_POST[subcatname]',
						upload_cost= '".number_format($_POST['upload_cost'], 2)."',
						upload_fields= '".intval($_POST['upload_fields'])."',
						urgent_cost= '".number_format($_POST['urgent_cost'], 2)."',
						catid = $_POST[catid],
						hasprice = '$_POST[hasprice]',
						pricelabel = '$pricelabel',
						expireafter = $expireafter,
						enabled = '$_POST[enabled]',
						alert = '$_POST[alert]',
						alerttitle = '$_POST[alerttitle]',
						alertdesc = '$_POST[alertdesc]'
					WHERE subcatid = $_POST[subcatid]";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Subcategory saved";
		}
		else
		{
			$sql = "INSERT INTO $t_subcats
					SET subcatname = '$_POST[subcatname]',
						upload_cost= '".number_format($_POST['upload_cost'], 2)."',
						upload_fields= '".intval($_POST['upload_fields'])."',
						urgent_cost= '".number_format($_POST['urgent_cost'], 2)."',
						catid = $_POST[catid],
						hasprice = '$_POST[hasprice]',
						pricelabel = '$pricelabel',
						expireafter = $expireafter,
						enabled = '$_POST[enabled]',
						alert = '$_POST[alert]',
						alerttitle = '$_POST[alerttitle]',
						alertdesc = '$_POST[alertdesc]'";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Subcategory saved";

			$sql = "SELECT subcatid FROM $t_subcats WHERE subcatid = LAST_INSERT_ID()";
			list($newsubcatid) = mysql_fetch_array(mysql_query($sql));

			$sql = "UPDATE $t_subcats SET pos = $newsubcatid WHERE subcatid = $newsubcatid";
			mysql_query($sql);
		}

		if ($_POST['subcatid'])
		{
			$subcatid = $_POST['subcatid'];
		}
		else
		{
			$sql = "SELECT LAST_INSERT_ID() FROM $t_subcats";
			list($subcatid) = mysql_fetch_array(mysql_query($sql));
		}
		
		
		if ($_POST['inherit']) {
			$_POST['fee'] = array();
			$_POST['fee_loc'] = array();
		}
		$paidCategoriesHelper->saveFeeInfo($subcatid, 2, $_POST);
	

	
		// Custom fields
		if ($_POST['xfieldmode'] == "copy") {
			
			$src = $_POST['xfieldsrc'];
			$sql = "INSERT INTO $t_subcatxfields
					SELECT {$subcatid}, `fieldnum`, `name`, `type`, `vals`, 
							`required`, `showinlist`, `searchable`, NOW()
						FROM $t_subcatxfields
						WHERE subcatid = $src";
			mysql_query($sql) or die($sql.mysql_error());
			$msg = "Subcategory saved";
			
		} else {
		
			if (count($_POST['xfields']))
			{
				foreach ($_POST['xfields'] as $fldnum=>$fld)
				{
					if(!$fld['NAME']) continue;
	
                
                	$fld['REQUIRED'] = 0+$fld['REQUIRED'];
			        
					$fld['SHOWINLIST'] = 0+$fld['SHOWINLIST'];
					$fld['SEARCHABLE'] = 0+$fld['SEARCHABLE'];
					$fld['VALUES'] = xStripSlashes($fld['VALUES']);
	
	              
					$sql = "INSERT INTO $t_subcatxfields
							SET subcatid = $subcatid,
								fieldnum = $fldnum,
								name = '$fld[NAME]',
								type = '$fld[TYPE]',
								vals = '$fld[VALUES]',
								required = '$fld[REQUIRED]',
								showinlist = '$fld[SHOWINLIST]',
								searchable = '$fld[SEARCHABLE]'";
	                
					mysql_query($sql) or die($sql.mysql_error());
					if (mysql_affected_rows()) $msg = "Subcategory saved";
				}
			}
		}
		
	}
}
elseif ($_GET['do'] == "delete")
{
	if ($_GET['subcatid'])
	{
		
			
		// Delete extra fields for the subcategory.
		$sql = "DELETE FROM $t_subcatxfields WHERE subcatid = $_GET[subcatid]";
		mysql_query($sql) or die(mysql_error().$sql);
		
		// Delete the extra fields for ads before deleting ads. 
		$sql = "SELECT adid FROM $t_ads WHERE subcatid = $_GET[subcatid]";
		$ad_res = mysql_query($sql) or die(mysql_error().$sql);
		$adlist = "";
		
		while($ad = mysql_fetch_array($ad_res)) {
			$adlist .= "$ad[adid],";
		}
		
		if ($adlist) {
			$adlist = substr($adlist, 0, -1);
			$sql = "DELETE FROM $t_adxfields WHERE adid IN ($adlist)";
			mysql_query($sql) or die(mysql_error().$sql);
		}
		
	

		// Delete ads
		$sql = "DELETE FROM $t_ads WHERE subcatid = $_GET[subcatid]";
		mysql_query($sql) or die(mysql_error().$sql);
		$adsdeleted = mysql_affected_rows();

		// Delete subcat
		$sql = "DELETE FROM $t_subcats WHERE subcatid = '$_GET[subcatid]'";

		mysql_query($sql) or die(mysql_error().$sql);
		if (mysql_affected_rows()) $msg = "Subcategory deleted";
		//else $err = "Cannot delete subcategory";
		
		
		$paidCategoriesHelper->deleteFeeInfo($_GET['subcatid'], 2);
		

	}
}
elseif ($_GET['do'] == "move")
{
	if ($_GET['subcatid'])
	{
		$subcatid = $_GET['subcatid'];
		$catid = $_GET['catid'];
		$direction = $_GET['direction'];
		
		$sql = "SELECT pos FROM $t_subcats WHERE subcatid = $_GET[subcatid]";
		list($curpos) = mysql_fetch_array(mysql_query($sql));

		// Find new position
		if ($direction > 0)
		{
			// To be moved up
			$sql = "SELECT pos FROM $t_subcats WHERE pos < $curpos AND catid = $catid ORDER BY pos DESC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}
		else
		{
			// To be moved down
			$sql = "SELECT pos FROM $t_subcats WHERE pos > $curpos AND catid = $catid ORDER BY pos ASC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}

		if ($newpos)
		{
			$sql = "UPDATE $t_subcats SET pos = $curpos WHERE pos = $newpos AND catid = $catid";
			mysql_query($sql);

			$sql = "UPDATE $t_subcats SET pos = $newpos WHERE subcatid = $subcatid";
			mysql_query($sql);

			if (!mysql_error() && mysql_affected_rows()) $msg = "Subcategory moved";

		}
	}
}


?>
<?php include_once("aheader.inc.php"); ?>
<?php
if ($_GET['do'] == "edit" || $_GET['do'] == "add")
{
	if ($_GET['type'] == "subcat")
	{
		if ($_GET['subcatid'])
		{
			$sql = "SELECT * FROM $t_subcats WHERE subcatid = '$_GET[subcatid]'";
			$thisitem = mysql_fetch_assoc(mysql_query($sql));
			
			if (!$thisitem)
			{
				echo "ERROR! Subcategory not found";
				exit;
			}

			list($xhasprice, $xpricelabel, $xsubcatfields) = GetCustomFields($_GET['subcatid']);
		}
?>

<script language="javascript">
function toggleValuesField(num, state)
{
	disstate = !state;
	document.frmSubcat.elements['xfields['+num+'][VALUES]'].disabled = disstate;
}


function changeFieldMode(mode) {
	var radios = document.forms['frmSubcat'].elements['xfieldmode'];
	
	if (mode == "copy") {
		document.getElementById('xfieldinfo_copy').style.display = '';
		document.getElementById('xfieldinfo_manual').style.display = 'none';
	} else {
		document.getElementById('xfieldinfo_manual').style.display = '';
		document.getElementById('xfieldinfo_copy').style.display = 'none';
	}
}

</script>

<h2>Add/Edit Subcategory</h2>
<form class="box" name="frmSubcat" action="?" method="post" class="box">
<table border="0">
<tr>
<td width="20%"><b>Subcategory name:</b></td>
<td><input type="text" name="subcatname" size="35" value="<?php echo $thisitem['subcatname']; ?>"></td>
</tr>
<tr>
<td><b>Category:</b></td>
<td>
<select name="catid">
<?php
$sql = "SELECT catid, catname
		FROM $t_cats
		ORDER BY catname";
$res = mysql_query($sql) or die(mysql_error());

while($row=mysql_fetch_array($res))
{
	echo "<option value=\"$row[catid]\"";
	if ($row['catid'] == $thisitem['catid'] || (!$thisitem && $row['catid'] == $_REQUEST['catid'])) echo " selected";
	echo ">$row[catname]</option>";
}

?>
</select>
</td>
</tr>

<tr>
<td><b>Ads expire after:</b><br>
</td>
<td><input type="text" size="3" maxlength="5" name="expireafter" value="<?php echo $thisitem['expireafter']?$thisitem['expireafter']:$expire_ads_after_default; ?>">  days</td>
</tr>

<tr>
<td><b>Add a price field for this category?:</b></td>
<td><input type="checkbox" name="hasprice" value="1" <?php if($thisitem['hasprice']) echo "checked"; ?> onchange="javascript:if(this.checked) this.form.pricelabel.disabled=false; else this.form.pricelabel.disabled=true;"></td>
</tr>

<tr>
<td><b>Name of the price field:</b><br>
<span class="hint">Eg: Price, Rent etc.</span></td>
<td><input type="text" size="20" name="pricelabel" value="<?php echo $thisitem['pricelabel']; ?>" <?php if(!$thisitem['hasprice']) echo "disabled"; ?>></td>
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


<!-- BEGIN Charge On Upload Addon Code -->
<tr>
<td><b>Upload Field Price:</b><br>
<span class="hint">Optional if you want to charge price for more upload fields. (Ex. 2.00, 5.00)</span></td>
<td><input name="upload_cost" type="text" value="<?php echo $thisitem['upload_cost']; ?>" size="10" maxlength="15"> for <select name="upload_fields">
<?php
foreach (range(0, 10) as $number) 
{
	$txt = ($number > 1) ? 's' : '';
	$selected = ($number == $thisitem['upload_fields'] ) ? 'selected="selected"' : '';
    echo "<option value=\"{$number}\"{$selected}>{$number} extra upload{$txt}</option>";
}
?>
</select></td>
</tr>
<!-- END Charge On Upload Addon Code -->


<!-- BEGIN Charge for Urgent Ad Addon Code -->
<tr>
<td>
<b>URGENT tag Price:</b><br>
<span class="hint">Optional if you want to charge price for URGENT tag to ads. (Ex. 2.00, 5.00)</span></td>
<td><input name="urgent_cost" type="text" value="<?php echo $thisitem['urgent_cost']; ?>" size="10" maxlength="15">
</td>
</tr>
<!-- END Charge for Urgent Ad Addon Code -->


<tr>
<td colspan="2">&nbsp;</td>
</tr>

<tr><td colspan="2"><b>Custom Fields:</b></td></tr>

<tr><td colspan="2">


<table>
<tr>
<td><input type="radio" name="xfieldmode" id="xfieldmode_copy" value="copy" onchange="changeFieldMode('copy')"></td>
<td><label for="xfieldmode_copy">Copy fields from another subcategory</label></td>
</tr>

<tr id="xfieldinfo_copy" style="display:none"><td>&nbsp;</td>
<td>
<div class="tip">
Note: Any changes made in the above subcategory *after* you submit this form, will *not* be reflected here.
<br><br>
</div>

<select name="xfieldsrc">
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
			echo "<option value=\"$s[subcatid]\">$s[subcatname]</option>";
		}

		echo "</optgroup>";
	}
}

?>
</select>
<br><br>

</td>
</tr>

<tr>
<td><input type="radio" name="xfieldmode" id="xfieldmode_manual" value="manual" checked onchange="changeFieldMode('manual')"></td>
<td><label for="xfieldmode_manual">Create a new set of fields</label></td>
</tr>

<tr id="xfieldinfo_manual"><td>&nbsp;</td>
<td>
<div class="tip">Enter as many or as little fields as you want.</div><br>
<fieldset id="xfielddef">


<table>

<tr>
<td align="center"><i>#</i></td>
<td><i>Field Name</i></td>
<td align="center"><i>Type</i></td>
<td align="center"><i>Values for dropdown<br>(Separated by ';' - semicolon)</i></td>

<td align="center"><i>Required</i></td>

<td align="center"><i>Show in<br>ad list</i></td>
<td align="center"><i>Searchable</i></td>
</tr>


<?php for ($i=1; $i<=$xfields_count; $i++) { ?>

<tr>

<td align="center"><?php echo $i; ?></td>

<td><input type="text" name="xfields[<?php echo $i; ?>][NAME]" size="15" value="<?php echo $xsubcatfields[$i]['NAME']; ?>"></td>

<td align="center">
<select name="xfields[<?php echo $i; ?>][TYPE]" onchange="toggleValuesField(<?php echo $i; ?>, this.value=='D')">
<option value="S" <?php if($xsubcatfields[$i]['TYPE']=="S") echo "selected"; ?>>Text</option>
<option value="N" <?php if($xsubcatfields[$i]['TYPE']=="N") echo "selected"; ?>>Number</option>
<option value="D" <?php if($xsubcatfields[$i]['TYPE']=="D") echo "selected"; ?>>Dropdown</option>
</select>
</td>

<td>
<input type="text" name="xfields[<?php echo $i; ?>][VALUES]" size="50" value="<?php echo $xsubcatfields[$i]['VALUES']; ?>" <?php if($xsubcatfields[$i]['TYPE']!="D") echo "disabled"; ?>>
</td>


<td align="center">
<input type="checkbox" name="xfields[<?php echo $i; ?>][REQUIRED]" value="1" <?php if($xsubcatfields[$i]['REQUIRED']) echo "checked"; ?>>
</td>


<td align="center">
<input type="checkbox" name="xfields[<?php echo $i; ?>][SHOWINLIST]" value="1" <?php if($xsubcatfields[$i]['SHOWINLIST']) echo "checked"; ?>>
</td>

<td align="center">
<input type="checkbox" name="xfields[<?php echo $i; ?>][SEARCHABLE]" value="1" <?php if($xsubcatfields[$i]['SEARCHABLE']) echo "checked"; ?>>
</td>

</tr>

<?php } ?>


</table>


</fieldset>
</td></tr>
</table>
<br>


</td>
</tr>

<?php /* START mod-paid-categories */ ?>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td style="vertical-align:top"><b>Posting fee:</b><br>
</td>
<td style="vertical-align:top">
<?php 
$feeSectionLevel = 2;
$feeSectionId = $_GET['subcatid'];
include("../paid_cats/admin/cat_fees.inc.php"); 
?>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php /* END mod-paid-categories */ ?>

<tr>
<td colspan="2">
<input type="hidden" name="do" value="save">
<input type="hidden" name="type" value="subcat">
<input type="hidden" name="subcatid" value="<?php echo $_GET['subcatid']; ?>">
<button type="submit" value="Save"> Save </button>
&nbsp;<button type="button" onclick="location.href='?catid=<?php echo $thisitem['catid']; ?>';">Cancel</button>
</td>
</tr>

</table>
</form>

<?php
	}
}
else
{
?>

<h2>Manage Subcategories</h2>
<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>


<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr>

<td valign="top">
<button name="add" type="button" onclick="javascript:location.href='?do=add&type=subcat&catid=<?php echo $catid; ?>';" value="">Add New</button>
<br><br>
</td>

<td align="right" valign="top">
<b>Show subcategories in: </b>
<select name="catid" onchange="if(this.value) location.href='?catid='+this.value;">
<option value="">- Select -</option>
<?php

$sql = "SELECT catid, catname
		FROM $t_cats
		ORDER BY pos";
$res = mysql_query($sql);

while ($row=mysql_fetch_array($res))
{
	echo "<option value=\"$row[catid]\"";
	if ($catid == $row['catid']) echo " selected"; 
	echo ">$row[catname]</option>";
}

?>
</select>
</td>

</tr></table>



<?php if($catid) { ?>

<br>
<h3><a href="cats.php">Categories</a> &raquo; <?php echo $catname; ?> &raquo;</h3>
<div class="legend" align="right"><b>E</b> - Enabled</div>
<form name="frmSubcats" action="?" method="get">
<table class="grid" cellspacing="1" cellpadding="6" width="100%">
	<tr class="gridhead">
		<td>Sub Category</td>
		<td width="150" align="center">URGENT ad cost</td>
		<td width="90" align="center">Uplo. Price</td>
		<td width="90" align="center">Uplo. Extra</td>
		<td width="20" align="center">E</td>
		<td colspan="4" align="center" width="40">Actions</td>
	</tr>

<?php
$sql = "SELECT scat.subcatid, scat.upload_cost, scat.upload_fields, scat.urgent_cost, scat.subcatname, scat.enabled, cat.catid, cat.catname
		FROM $t_subcats scat
			INNER JOIN $t_cats cat ON scat.catid = cat.catid
		WHERE scat.catid = $catid
		ORDER BY  scat.pos";
$res = mysql_query($sql) or die(mysql_error());

$i = 0;
$j = 0;
$thiscatname = "";
while ($row=mysql_fetch_array($res))
{
	/*if($row['catname'] != $thiscatname)
	{
		$thiscatname = $row['catname'];
		$j = 0;
?>

	<tr class="gridgrouphead">
		<td colspan="4"><?php echo $thiscatname; ?></td>
	</tr>

<?php
	}*/

	$i++;
	$j++;
	$cssalt = ($j%2 ? "" : "alt");
	
	
	$subcatname = $row['subcatname'];
	

?>

	<tr class="gridcell<?php echo $cssalt; ?>">
		<td><?php echo $row['subcatname']; ?></td>
		<td align="center"><?php echo $row['urgent_cost']; ?></td>
		<td align="center"><?php echo $row['upload_cost']; ?></td>
		<td align="center"><?php echo $row['upload_fields']; ?></td>
		<td align="center"><?php if($row['enabled']) echo "<span class=\"yes\">+</span>"; 
		else echo "<span class=\"no\">X</span>"; ?></td>
		<td width="20" align="center"><a href="?do=move&direction=1&catid=<?php echo $catid; ?>&subcatid=<?php echo $row['subcatid']; ?>"><img src="images/up.gif" border="0" alt="Move Up" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=move&direction=-1&catid=<?php echo $catid; ?>&subcatid=<?php echo $row['subcatid']; ?>"><img src="images/down.gif" border="0" alt="Move Down" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=edit&type=subcat&subcatid=<?php echo $row['subcatid']; ?>"><img src="images/edit.gif" border="0" alt="Edit" title="Edit"></a></td>
		<td width="20" align="center"><a href="javascript:if(confirm('Delete subcategory?')) location.href = '?do=delete&type=subcat&catid=<?php echo $catid; ?>&subcatid=<?php echo $row['subcatid']; ?>';"><img src="images/del.gif" border="0" alt="Delete" title="Delete"></a></td>
	</tr>

<?php
}
?>

</table>


<?php 
if ($shortcut_categories && $i == 1 && $subcatname == $catname) {
?>
<br>
<div class="tip">
<img src="images/tip.gif" align="left">
This is a postable category; posts to this category are automatically sent to the above subcategory.<br>
Note that this category will no longer be postable if you add more subcategories or make the category and subcategory names different.
</div>
<?php
}
?>


</form>
<br>

<?php } else { ?>

<br><div class="infobox">Please select a category</div>

<?php } ?>

<?php
}
?>
<?php include_once("afooter.inc.php"); ?>