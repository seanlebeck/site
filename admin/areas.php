<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");


$_POST['do'] = strtolower($_POST['do']);
$_GET['do'] = strtolower($_GET['do']);
$_REQUEST['do'] = strtolower($_REQUEST['do']);


$cityid = $_REQUEST['cityid'];
if($cityid)
{
	$sql = "SELECT ct.cityname, ct.countryid, c.countryname FROM $t_cities ct INNER JOIN $t_countries c ON c.countryid = ct.countryid WHERE ct.cityid = $cityid";
	list($cityname, $countryid, $countryname) = mysql_fetch_array(mysql_query($sql));
}


if ($_POST['do'] == "save")
{
	if ($_POST['areaname'])
	{
		if ($_POST['areaid'])
		{
			$sql = "UPDATE $t_areas
					SET areaname = '$_POST[areaname]',
						cityid = $_POST[cityid],
						enabled = '$_POST[enabled]'
					WHERE areaid = $_POST[areaid]";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Location saved";
			else $err = "Cannot save location";

		}
		else
		{
			$sql = "INSERT INTO $t_areas
					SET areaname = '$_POST[areaname]',
						cityid = $_POST[cityid],
						enabled = '$_POST[enabled]'";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Location saved";
			else $err = "Cannot save location";

			$sql = "SELECT areaid FROM $t_areas WHERE areaid = LAST_INSERT_ID()";
			list($newid) = mysql_fetch_array(mysql_query($sql));

			$sql = "UPDATE $t_areas SET pos = $newid WHERE areaid = $newid";
			mysql_query($sql);

		}
	}
}
elseif ($_GET['do'] == "delete")
{

	if ($_GET['areaid'])
	{
		// Delete area
		$sql = "DELETE FROM $t_areas WHERE areaid = '$_GET[areaid]'";

		mysql_query($sql) or die(mysql_error().$sql);
		if (mysql_affected_rows()) $msg = "Area deleted";
		else $err = "Cannot delete area";
	}
}

elseif ($_GET['do'] == "move")
{
	if ($_GET['areaid'])
	{
		$areaid = $_GET['areaid'];
		$direction = $_GET['direction'];
		
		$sql = "SELECT pos FROM $t_areas WHERE areaid = $_GET[areaid]";
		list($curpos) = mysql_fetch_array(mysql_query($sql));

		// Find new position
		if ($direction > 0)
		{
			// To be moved up
			$sql = "SELECT pos FROM $t_areas WHERE pos < $curpos AND cityid = $cityid ORDER BY pos DESC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}
		else
		{
			// To be moved down
			$sql = "SELECT pos FROM $t_areas WHERE pos > $curpos AND cityid = $cityid ORDER BY pos ASC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}

		if ($newpos)
		{
			$sql = "UPDATE $t_areas SET pos = $curpos WHERE pos = $newpos AND cityid = $cityid";
			mysql_query($sql);

			$sql = "UPDATE $t_areas SET pos = $newpos WHERE areaid = $areaid";
			mysql_query($sql);

			if (!mysql_error() && mysql_affected_rows()) $msg = "Area moved";

		}
	}
}

?>
<?php include_once("aheader.inc.php"); ?>
<?php
if ($_GET['do'] == "edit" || $_GET['do'] == "add")
{
	if ($_GET['type'] == "area")
	{
		if ($_GET['areaid'])
		{
			$sql = "SELECT * FROM $t_areas WHERE areaid = '$_GET[areaid]'";
			$thisitem = mysql_fetch_assoc(mysql_query($sql));
			
			if (!$thisitem)
			{
				echo "ERROR! Area not found";
				exit;
			}
		}
?>
<h2>Add/Edit Area</h2>
<form class="box" name="frmArea" action="?" method="post">
<table border="0">
<tr>
<td><b>Area:</b></td>
<td><input type="text" name="areaname" size="35" value="<?php echo $thisitem['areaname']; ?>"></td>
</tr>
<tr>
<td><b>City:</b></td>
<td>
<select name="cityid">
<?php
$sql = "SELECT cityid, cityname, countryname
		FROM $t_cities ct
			INNER JOIN $t_countries c ON ct.countryid = c.countryid
		ORDER BY countryname, cityname";
$res = mysql_query($sql) or die(mysql_error());

while($row=mysql_fetch_array($res))
{
	echo "<option value=\"$row[cityid]\"";
	if (($thisitem && $row['cityid'] == $thisitem['cityid']) || $row['cityid'] == $_GET['cityid']) echo " selected";
	echo ">$row[countryname] > $row[cityname]</option>";
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
<input type="hidden" name="type" value="area">
<input type="hidden" name="areaid" value="<?php echo $_GET['areaid']; ?>">

<button type="submit" value="Save"> Save </button>
&nbsp;<button type="button" onclick="location.href='?countryid=<?php echo $_GET['countryid']; ?>&cityid=<?php echo $_GET['cityid']; ?>';">Cancel</button>
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
<h2>Manage Areas</h2>

<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr>

<td valign="top">
<button name="add" type="button" onclick="javascript:location.href='?do=add&type=area&cityid=<?php echo $cityid; ?>';">Add New</button>
</td>

<td align="right" valign="top">
<b>Show areas in: </b>
<select name="countryid" onchange="if(this.value) location.href='?cityid='+this.value;">
<option value="">- Select -</option>
<?php

$sql = "SELECT cityid, cityname, countryname
		FROM $t_cities ct 
			INNER JOIN $t_countries c ON ct.countryid = c.countryid
		ORDER BY c.pos, ct.pos";
$res = mysql_query($sql);

while ($row=mysql_fetch_array($res))
{
	echo "<option value=\"$row[cityid]\"";
	if ($cityid == $row['cityid']) echo " selected"; 
	echo ">$row[countryname] > $row[cityname]</option>";
}

?>
</select>
</td>

</tr></table><br>

<?php if($cityid) { ?>


<form name="frmCities" action="?" method="get">
<h3><a href="regions.php">Regions</a> &raquo; 
<a href="cities.php?countryid=<?php echo $countryid; ?>"><?php echo $countryname; ?></a> &raquo; 
<?php echo $cityname; ?> &raquo; </h3>

<div class="legend" align="right"><b>E</b> - Enabled</div><br>

<form method="post" action="" name="frmareas">
<table class="grid" cellspacing="1" cellpadding="6" width="100%">
	<tr class="gridhead">
		<td>Area</td>
		<td width="20" align="center">E</td>
		<td colspan="4" align="center" width="40">Actions</td>
	</tr>

<?php
$sql = "SELECT a.areaid, a.areaname, a.enabled
		FROM $t_areas a
		WHERE cityid = $cityid
		ORDER BY pos";
$res = mysql_query($sql) or die(mysql_error());

$i = 0;
while ($row=mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");
?>

	<tr class="gridcell<?php echo $cssalt; ?>">
		<td><?php echo $row['areaname']; ?></td>
		<td align="center"><?php if($row['enabled']) echo "<span class=\"yes\">+</span>"; 
		else echo "<span class=\"no\">X</span>"; ?></td>
		<td width="20" align="center"><a href="?do=move&direction=1&areaid=<?php echo $row['areaid']; ?>&cityid=<?php echo $cityid; ?>&countryid=<?php echo $countryid; ?>"><img src="images/up.gif" border="0" alt="Move Up" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=move&direction=-1&areaid=<?php echo $row['areaid']; ?>&cityid=<?php echo $cityid; ?>&countryid=<?php echo $countryid; ?>"><img src="images/down.gif" border="0" alt="Move Down" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=edit&type=area&areaid=<?php echo $row['areaid']; ?>&cityid=<?php echo $cityid; ?>&countryid=<?php echo $countryid; ?>"><img src="images/edit.gif" border="0" alt="Edit" title="Edit"></a></td>
		<td width="20" align="center"><a href="javascript:if(confirm('Delete area?')) location.href = '?do=delete&type=area&areaid=<?php echo $row['areaid']; ?>&cityid=<?php echo $cityid; ?>&countryid=<?php echo $countryid; ?>';"><img src="images/del.gif" border="0" alt="Delete" title="Delete"></a></td>
	</tr>

<?php
}
?>

</table>
</form>
<br>

<?php } else { ?>

<br><div class="infobox">Please select a city</div>

<?php } ?>

<?php
}
?>
<?php include_once("afooter.inc.php"); ?>