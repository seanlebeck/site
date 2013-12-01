<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");


$_POST['do'] = strtolower($_POST['do']);
$_GET['do'] = strtolower($_GET['do']);
$_REQUEST['do'] = strtolower($_REQUEST['do']);


$countryid = $_REQUEST['countryid'];
if($countryid)
{
	$sql = "SELECT countryname FROM $t_countries WHERE countryid = $countryid";
	list($countryname) = @mysql_fetch_array(mysql_query($sql));
}


if ($_POST['do'] == "save")
{
	if ($_POST['cityname'])
	{
		if ($_POST['cityid'])
		{
			$sql = "UPDATE $t_cities
					SET cityname = '$_POST[cityname]',
						countryid = $_POST[countryid],
						enabled = '$_POST[enabled]'
					WHERE cityid = $_POST[cityid]";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Location saved";
			else $err = "Cannot save location";
		}
		else
		{
			$sql = "INSERT INTO $t_cities
					SET cityname = '$_POST[cityname]',
						countryid = $_POST[countryid],
						enabled = '$_POST[enabled]'";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Location saved";
			else $err = "Cannot save location";

			$sql = "SELECT cityid FROM $t_cities WHERE cityid = LAST_INSERT_ID()";
			list($newid) = mysql_fetch_array(mysql_query($sql));

			$sql = "UPDATE $t_cities SET pos = $newid WHERE cityid = $newid";
			mysql_query($sql);

		}
	}


}
elseif ($_GET['do'] == "delete")
{
	if ($_GET['cityid'])
	{
		// Delete ads
		$adsdeleted = 0;

		$sql = "DELETE FROM $t_ads WHERE cityid = $_GET[cityid]";
		mysql_query($sql) or die(mysql_error().$sql);
		$adsdeleted += mysql_affected_rows();

		$sql = "DELETE FROM $t_events WHERE cityid = $_GET[cityid]";
		mysql_query($sql) or die(mysql_error().$sql);
		$adsdeleted += mysql_affected_rows();


		// Delete city
		$sql = "DELETE FROM $t_cities WHERE cityid = '$_GET[cityid]'";

		mysql_query($sql) or die(mysql_error().$sql);
		if (mysql_affected_rows()) $msg = "City deleted";
		else $err = "Cannot delete city";
		
	
		$paidCategoriesHelper->deleteFeeInfo(null, null, $_GET['cityid'], 2);
	
	}

}

elseif ($_GET['do'] == "move")
{
	if ($_GET['cityid'])
	{
		$cityid = $_GET['cityid'];
		$direction = $_GET['direction'];

		if (!$countryid)
		{
			$sql = "SELECT ct.countryid, c.countryname FROM $t_cities ct INNER JOIN $t_countries c ON c.countryid = ct.countryid WHERE ct.cityid = $cityid";
			list($countryid, $countryname) = mysql_fetch_array(mysql_query($sql));
		}

		$sql = "SELECT pos FROM $t_cities WHERE cityid = $_GET[cityid]";
		list($curpos) = mysql_fetch_array(mysql_query($sql));

		// Find new position
		if ($direction > 0)
		{
			// To be moved up
			$sql = "SELECT pos FROM $t_cities WHERE pos < $curpos AND countryid = $countryid ORDER BY pos DESC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}
		else
		{
			// To be moved down
			$sql = "SELECT pos FROM $t_cities WHERE pos > $curpos AND countryid = $countryid ORDER BY pos ASC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}

		if ($newpos)
		{
			$sql = "UPDATE $t_cities SET pos = $curpos WHERE pos = $newpos AND countryid = $countryid";
			mysql_query($sql);

			$sql = "UPDATE $t_cities SET pos = $newpos WHERE cityid = $cityid";
			mysql_query($sql);

			if (!mysql_error() && mysql_affected_rows()) $msg = "City moved";

		}
	}
}

?>
<?php include_once("aheader.inc.php"); ?>
<?php
if ($_GET['do'] == "edit" || $_GET['do'] == "add")
{
	if ($_GET['type'] == "city")
	{
		if ($_GET['cityid'])
		{
			$sql = "SELECT * FROM $t_cities WHERE cityid = '$_GET[cityid]'";
			$thisitem = mysql_fetch_assoc(mysql_query($sql));
			
			if (!$thisitem)
			{
				echo "ERROR! City not found";
				exit;
			}
		}
?>
<h2>Add/Edit City</h2>
<form class="box" name="frmCity" action="?" method="post">
<table border="0">
<tr>
<td><b>City:</b></td>
<td><input type="text" name="cityname" size="35" value="<?php echo $thisitem['cityname']; ?>"></td>
</tr>
<tr>
<td><b>Country:</b></td>
<td>
<select name="countryid">
<?php
$sql = "SELECT countryid, countryname
		FROM $t_countries
		ORDER BY countryname";
$res = mysql_query($sql) or die(mysql_error());

while($row=mysql_fetch_array($res))
{
	echo "<option value=\"$row[countryid]\"";
	if (($thisitem && $row['countryid'] == $thisitem['countryid']) || $row['countryid'] == $_GET['countryid']) echo " selected";
	echo ">$row[countryname]</option>";
}

?>
</select>
</td>
</tr>
<tr>
<td><b>Enabled:</b></td>
<td><input type="checkbox" name="enabled" value="1" <?php if($thisitem['enabled'] == 1 || !$thisitem) echo "checked"; ?>></td>
</tr>
<tr>
<td></td>
<td>
<input type="hidden" name="do" value="save">
<input type="hidden" name="type" value="city">
<input type="hidden" name="cityid" value="<?php echo $_GET['cityid']; ?>">

<button type="submit" value="Save"> Save </button>
&nbsp;<button type="button" onclick="location.href='?countryid=<?php echo $_GET['countryid']; ?>';">Cancel</button>
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


<h2>Manage Cities</h2>

<?php if($msg) { ?><div class="msg"><?php echo $msg; ?></div><?php } ?>
<?php if($err) { ?><div class="err"><?php echo $err; ?></div><?php } ?>


<p class="tip"><img src="images/tip.gif" border="0" align="absmiddle"> Click a city to edit the areas under that city</p>


<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr>

<td valign="top">
<button name="add" type="button" onclick="javascript:location.href='?do=add&type=city&countryid=<?php echo $countryid; ?>';">Add New</button>
</td>

<td align="right" valign="top">
<b>Show cities in: </b>
<select name="countryid" onchange="if(this.value) location.href='?countryid='+this.value;">
<option value="">- Select -</option>
<?php

$sql = "SELECT countryid, countryname
		FROM $t_countries
		ORDER BY pos";
$res = mysql_query($sql);

while ($row=mysql_fetch_array($res))
{
	echo "<option value=\"$row[countryid]\"";
	if ($countryid == $row['countryid']) echo " selected"; 
	echo ">$row[countryname]</option>";
}

?>
</select>
</td>

</tr></table><br>

<?php if($countryid) { ?>

<form name="frmCities" action="?" method="get">
<h3><a href="regions.php">Regions</a> &raquo; <?php echo $countryname; ?> &raquo; </h3>


<div class="legend" align="right"><b>E</b> - Enabled</div><br>
<form method="post" action="" name="frmcities">
<table class="grid" cellspacing="1" cellpadding="6" width="100%">
	<tr class="gridhead">
		<td>City</td>
		<td width="20" align="center">E</td>
		<td colspan="4" align="center" width="40">Actions</td>
	</tr>

<?php
$sql = "SELECT ct.cityid, ct.cityname, ct.enabled
		FROM $t_cities ct
		WHERE countryid = $countryid
		ORDER BY pos";
$res = mysql_query($sql) or die(mysql_error());

$i = 0;
while ($row=mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");
    
  
	$cityname = $row['cityname'];
	
?>

	<tr class="gridcell<?php echo $cssalt; ?>">
		<td><a href="areas.php?countryid=<?php echo $countryid; ?>&cityid=<?php echo $row['cityid']; ?>"><?php echo $row['cityname']; ?></a></td>
		<td align="center"><?php if($row['enabled']) echo "<span class=\"yes\">+</span>"; 
		else echo "<span class=\"no\">X</span>"; ?></td>
		<td width="20" align="center"><a href="?do=move&direction=1&cityid=<?php echo $row['cityid']; ?>"><img src="images/up.gif" border="0" alt="Move Up" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=move&direction=-1&cityid=<?php echo $row['cityid']; ?>&countryid=<?php echo $countryid; ?>"><img src="images/down.gif" border="0" alt="Move Down" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=edit&type=city&cityid=<?php echo $row['cityid']; ?>&countryid=<?php echo $countryid; ?>"><img src="images/edit.gif" border="0" alt="Edit" title="Edit"></a></td>
		<td width="20" align="center"><a href="javascript:if(confirm('Delete city?')) location.href = '?do=delete&type=city&cityid=<?php echo $row['cityid']; ?>&countryid=<?php echo $countryid; ?>';"><img src="images/del.gif" border="0" alt="Delete" title="Delete"></a></td>
	</tr>

<?php
}
?>

</table>


<?php 
if ($shortcut_regions && $i == 1 && $countryname == $cityname) {
?>
<br>
<div class="tip">
<img src="images/tip.gif" align="left">
This is a postable region; posts to this region are automatically sent to the above city.<br>
Note that this region will no longer be postable if you add more cities or make the region and city names different.
</div>
<?php
}
?>


</form>
<br>



<?php } else { ?>

<br><div class="infobox">Please select a region</div>

<?php } ?>


<?php
}
?>
<?php include_once("afooter.inc.php"); ?>