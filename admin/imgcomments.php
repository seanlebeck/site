<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");


if (!$_GET['imgid'])
{
	header("Location: home.php");
	exit;
}

if ($_POST['del'] && $_POST['comment'])
{
	$d = 0;
	$nd = 0;
	foreach ($_POST['comment'] as $commentid)
	{
		$sql = "DELETE FROM $t_imgcomments WHERE commentid = '$commentid'";
		if (mysql_query($sql) && mysql_affected_rows()) $d++;
		else $nd++;
	}

	$msg = "$d comments deleted";
	if($nd) $err = "$nd comments could not be deleted";
}

$sql = "SELECT * FROM $t_imgs WHERE imgid = $_GET[imgid] ORDER BY timestamp DESC";
$img = mysql_fetch_array(mysql_query($sql));

?>
<?php include_once("aheader.inc.php"); ?>
<h2>Manage Images</h2>

<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<h3>Edit comments for '<?php echo $img['imgtitle']; ?>'</h3>
<img src="../userimgs/<?php echo $img['imgfilename']; ?>" width="100" height="75" border="1"><br><br>
<b><a href="<?php echo $_GET['returl']; ?>">Back to Images</a></b><br><br>
<br>

<script language="javascript">
function checkall(state)
{
	var n = frmComments.elements.length;
	for (i=0; i<n; i++)
	{
		if (frmComments.elements[i].name == "comment[]") frmComments.elements[i].checked = state;
	}
}
</script>
<form method="post" action="" name="frmComments">
<table width="100%" border="0" cellpadding="4" cellspacing="1" class="grid">
	<tr>
		<td class="gridhead" align="center" width="5%">#</td>
		<td class="gridhead" width="30%">Posted By/On</td>
		<td class="gridhead" width="60%">Comment</td>
		<td class="gridhead" width="5%" align="center">
		<input type="checkbox" onclick="javascript:checkall(this.checked);"></td>
	</tr>

<?php

$sql = "SELECT *, UNIX_TIMESTAMP(timestamp) AS timestamp FROM $t_imgcomments WHERE imgid = $_GET[imgid]";
$res = mysql_query($sql) or die($sql.mysql_error());

while ($row = @mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");
?>

	<tr class="gridcell<?php echo $cssalt; ?>">

	<td align="center" valign="top">
	<?php echo $i; ?>
	</td>

	<td valign="top">
	<b><?php echo $row['postername']; ?></b><br>
	<span class="date"><?php echo date("F d, Y H:i", $row['timestamp']); ?></span>
	</td>

	<td>
	<?php echo $row['comment']; ?>
	</td>

	<td valign="top" align="center">
	<input type="checkbox" name="comment[]" value="<?php echo $row['commentid']; ?>">
	</td>

	</tr>


<?php
}
?>

<tr>
<td colspan="4" align="right">With selected: &nbsp;
<input type="submit" name="del" value="Delete" class="cautionbutton"></td>
</tr>

</table>
</form>

<?php include_once("afooter.inc.php"); ?>