<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");


$_POST['do'] = strtolower($_POST['do']);
$_GET['do'] = strtolower($_GET['do']);
$_REQUEST['do'] = strtolower($_REQUEST['do']);


if ($_POST['do'] == "save")
{
	if ($_POST['countryname'])
	{
		if($_POST['countryid'])
		{
			$sql = "UPDATE $t_countries
					SET countryname = '$_POST[countryname]',
						enabled = '$_POST[enabled]'
					WHERE countryid = $_POST[countryid]";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Location saved";
			else $err = "Cannot save location";
		}
		else
		{
			$sql = "INSERT INTO $t_countries
					SET countryname = '$_POST[countryname]',
						enabled = '$_POST[enabled]'";

			mysql_query($sql) or die(mysql_error().$sql);
			if (mysql_affected_rows()) $msg = "Location saved";
			else $err = "Cannot save location";

			$sql = "SELECT countryid FROM $t_countries WHERE countryid = LAST_INSERT_ID()";
			list($newid) = mysql_fetch_array(mysql_query($sql));

			$sql = "UPDATE $t_countries SET pos = $newid WHERE countryid = $newid";
			mysql_query($sql);

		}
	}

}
elseif ($_GET['do'] == "delete")
{
	if ($_GET['countryid'])	
	{
		// Delete ads in each city in country
		$sql = "SELECT cityid FROM $t_cities WHERE countryid = $_GET[countryid]";
		$res = mysql_query($sql) or die(mysql_error().$sql);
		$adsdeleted = 0;

		while ($c=mysql_fetch_array($res))
		{
			$sql = "DELETE FROM $t_ads WHERE cityid = $c[cityid]";
			mysql_query($sql) or die(mysql_error().$sql);
			$adsdeleted += mysql_affected_rows();

			$sql = "DELETE FROM $t_events WHERE cityid = $c[cityid]";
			mysql_query($sql) or die(mysql_error().$sql);
			$adsdeleted += mysql_affected_rows();

			$sql = "DELETE FROM $t_areas WHERE cityid = $c[cityid]";
			mysql_query($sql) or die(mysql_error().$sql);
			
		
			$paidCategoriesHelper->deleteFeeInfo(null, null, $c['cityid'], 2);
			
		}

		// Delete cities
		$sql = "DELETE FROM $t_cities WHERE countryid = $_GET[countryid]";
		mysql_query($sql) or die(mysql_error().$sql);
		$citiesdeleted = mysql_affected_rows();

		// Delete country
		$sql = "DELETE FROM $t_countries WHERE countryid = $_GET[countryid]";
		mysql_query($sql) or die(mysql_error().$sql);

		if (mysql_affected_rows()) $msg = "Region deleted";
		else $err = "Cannot delete region";
		
		
		$paidCategoriesHelper->deleteFeeInfo(null, null, $_GET['countryid'], 1);
		

	}
}

elseif ($_GET['do'] == "move")
{
	if ($_GET['countryid'])
	{
		$countryid = $_GET['countryid'];
		$direction = $_GET['direction'];
		
		$sql = "SELECT pos FROM $t_countries WHERE countryid = $_GET[countryid]";
		list($curpos) = mysql_fetch_array(mysql_query($sql));

		// Find new position
		if ($direction > 0)
		{
			// To be moved up
			$sql = "SELECT pos FROM $t_countries WHERE pos < $curpos ORDER BY pos DESC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}
		else
		{
			// To be moved down
			$sql = "SELECT pos FROM $t_countries WHERE pos > $curpos ORDER BY pos ASC LIMIT 1";
			list($newpos) = @mysql_fetch_array(mysql_query($sql));
		}

		if ($newpos)
		{
			$sql = "UPDATE $t_countries SET pos = $curpos WHERE pos = $newpos";
			mysql_query($sql);

			$sql = "UPDATE $t_countries SET pos = $newpos WHERE countryid = $countryid";
			mysql_query($sql);

			if (!mysql_error() && mysql_affected_rows()) $msg = "Region moved";

		}
	}
}


?>
<?php include_once("aheader.inc.php"); ?>
<?php
if ($_GET['do'] == "edit" || $_GET['do'] == "add")
{
	if ($_GET['type'] == "country")
	{
		if ($_GET['countryid'])
		{
			$sql = "SELECT * FROM $t_countries WHERE countryid = '$_GET[countryid]'";
			$thisitem = mysql_fetch_assoc(mysql_query($sql));
			
			if (!$thisitem)
			{
				echo "ERROR! Country not found";
				exit;
			}
		}
?>
<h2>Add/Edit Region</h2>
<form class="box" name="frmCountry" action="?" method="post">
<table border="0">
<tr>
<td><b>Region:</b></td>
<td><input type="text" name="countryname" size="35" value="<?php echo $thisitem['countryname']; ?>"></td>
</tr>
<tr>
<td><b>Enabled:</b></td>
<td><input type="checkbox" name="enabled" value="1" <?php if($thisitem['enabled'] == 1 || !$thisitem) echo "checked"; ?>></td>
</tr>
<tr>
<td></td>
<td>
<input type="hidden" name="do" value="save">
<input type="hidden" name="type" value="country">
<input type="hidden" name="countryid" value="<?php echo $_GET['countryid']; ?>">

<button type="submit" value="Save"> Save </button>
&nbsp;<button type="button" onclick="location.href='?';">Cancel</button>
</td>

</tr>
</table>
</form>

<img src="images/tip.gif" align="top">
<a href="javascript:showHelp('postable_region');">
How to create a "postable" region?</a>

<div id="help_postable_region" style="display:none;width:500px;">
<ol>
    <li>Make sure $shortcut_regions is set to TRUE in the config file.</li>
    <li>Create your region.</li>
    <li>Create a single city under this with the exact same name as the region.</li>
</ol>

<div style="margin-left:22px;">
This city will not be visible to users and the region will act like it is postable. Every time a post is made to the region, it will be automatically routed to its lone city.<br><br>
Note that you should not add more than one city and the region and city names should exactly be the same.
</div>
</div>

<?php
	}
}
else
{
?>
<h2>Manage Regions</h2>

<?php if($msg) { ?><div class="msg"><?php echo $msg; ?></div><?php } ?>
<?php if($err) { ?><div class="err"><?php echo $err; ?></div><?php } ?>

<p class="tip"><img src="images/tip.gif" border="0" align="left"> Click a region to edit the cities under that region.<br> Only those regions with atleast one city will show up on the site.</p>

<button name="add" type="button" onclick="javascript:location.href='?do=add&type=country';">Add New</button>

<div class="legend" align="right"><b>E</b> - Enabled</div>
<form method="post" action="" name="frmCountries">
<table class="grid" cellspacing="1" cellpadding="6" width="100%">
	<tr class="gridhead">
		<td>Region</td>
		<td width="20" align="center">E</td>
		<td colspan="4" align="center" width="40">Actions</td>
	</tr>

<?php
$sql = "SELECT c.countryid, c.countryname, c.enabled
		FROM $t_countries c
		ORDER BY pos";
$res = mysql_query($sql) or die(mysql_error());

$i = 0;
while ($row=mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");
?>

	<tr class="gridcell<?php echo $cssalt; ?>">
		<td><a href="cities.php?countryid=<?php echo $row['countryid']; ?>"><?php echo $row['countryname']; ?></a></td>
		<td align="center"><?php if($row['enabled']) echo "<span class=\"yes\">+</span>"; 
		else echo "<span class=\"no\">X</span>"; ?></td>
		<td width="20" align="center"><a href="?do=move&direction=1&countryid=<?php echo $row['countryid']; ?>"><img src="images/up.gif" border="0" alt="Move Up" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=move&direction=-1&countryid=<?php echo $row['countryid']; ?>"><img src="images/down.gif" border="0" alt="Move Down" title="Move Up"></a></td>
		<td width="20" align="center"><a href="?do=edit&type=country&countryid=<?php echo $row['countryid']; ?>"><img src="images/edit.gif" border="0" alt="Edit" title="Edit"></a></td>
		<td width="20" align="center"><a href="javascript:if(confirm('Delete region?')) location.href = '?do=delete&type=country&countryid=<?php echo $row['countryid']; ?>';"><img src="images/del.gif" border="0" alt="Delete" title="Delete"></a></td>
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