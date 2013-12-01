<?php



require_once("admin.inc.php");
require_once("aauth.inc.php");


$_POST['do'] = strtolower($_POST['do']);
$_GET['do'] = strtolower($_GET['do']);
$_REQUEST['do'] = strtolower($_REQUEST['do']);


if ($_POST['do'] == "save")
{
	if($_POST['eoptid'])
	{
		$sql = "UPDATE $t_options_extended
				SET days = '$_POST[days]',
					price = '$_POST[price]'
				WHERE eoptid = $_POST[eoptid]";
	}
	else
	{
		$sql = "INSERT INTO $t_options_extended 
				SET days = '$_POST[days]',
					price = '$_POST[price]'";
	}

	mysql_query($sql) or die(mysql_error().$sql);
	if (mysql_affected_rows()) $msg = "Option saved";
	
}
elseif ($_GET['do'] == "delete")
{
	$sql = "DELETE FROM $t_options_extended WHERE eoptid = '$_GET[eoptid]'";
	mysql_query($sql) or die(mysql_error().$sql);
	if (mysql_affected_rows()) $msg = "Option deleted";
}


?>
<?php include_once("aheader.inc.php"); ?>
<?php
if ($_GET['do'] == "edit" || $_GET['do'] == "add")
{
	if ($_GET['eoptid'])
	{
		$sql = "SELECT * FROM $t_options_extended WHERE eoptid = '$_GET[eoptid]'";
		$thisitem = mysql_fetch_assoc(mysql_query($sql));
		
		if (!$thisitem)
		{
			echo "ERROR! Option not found";
			exit;
		}
	}
?>
<h2>Add/Edit Extended Ad Option</h2>
<form name="frmCat" action="?" method="post" class="box">
<table border="0">
<tr>
<td valign="top"><b>Duration:</b><br>
<span class="hint">This will be in addition to the normal<br>
number of days the ad will be running</span></td>
<td valign="top"><input type="text" name="days" size="15" value="<?php echo $thisitem['days']; ?>"> more days </td>
</tr>
<tr>
<td valign="top"><b>Price (<?php echo $paypal_currency_symbol; ?>):</b><br></td>
<td valign="top"><input type="text" name="price" size="15" value="<?php echo $thisitem['price']; ?>"></td>
</tr>
<tr>
<td></td>
<td>
<input type="hidden" name="do" value="save">
<input type="hidden" name="eoptid" value="<?php echo $_GET['eoptid']; ?>">
<button type="submit" value="Save"> Save </button>&nbsp;
<button type="button" onclick="document.location='?';"> Cancel </button></td>
</tr>
</table>
</form>
<?php
}
else
{
?>
<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<h2>Extended Ad Options</h2>

<button name="add" type="button" onclick="javascript:location.href='?do=add';" value="">Add New</button>
<br><br>


<table class="grid" cellspacing="1" cellpadding="6" width="100%">
	<tr class="gridhead">
		<td>Duration</td>
		<td>Price</td>
		<td colspan="2" align="center" width="40">Actions</td>
	</tr>

<?php
$sql = "SELECT * FROM $t_options_extended ORDER BY days ASC";
$res = mysql_query($sql);

$i = 0;
while ($row=mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");
?>

	<tr class="gridcell<?php echo $cssalt; ?>">
		<td><?php echo $row['days']; ?> more days</td>
		<td><?php echo $paypal_currency_symbol; ?><?php echo $row['price']; ?></td>
		<td width="20" align="center"><a href="?do=edit&eoptid=<?php echo $row['eoptid']; ?>"><img src="images/edit.gif" border="0" alt="Edit" title="Edit"></a></td>
		<td width="20" align="center"><a href="javascript:if(confirm('Delete option?')) location.href = '?do=delete&eoptid=<?php echo $row['eoptid']; ?>';"><img src="images/del.gif" border="0" alt="Delete" title="Delete"></a></td>
	</tr>

<?php
}
?>

</table>

<?php
}
?>
<?php include_once("afooter.inc.php"); ?>