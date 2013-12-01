<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");

$_POST['do'] = strtolower($_POST['do']);
$_GET['do'] = strtolower($_GET['do']);
$_REQUEST['do'] = strtolower($_REQUEST['do']);



if ($_POST['do'] == "save" && $_POST['ips'])
{
	$ips = explode("\n", $_POST['ips']);
	$err = "";
	$userip = IPVal($_SERVER['REMOTE_ADDR']);
	
	foreach ($ips as $iprange) {
		$iprange = trim($iprange);
		$parts = explode("-", $iprange);
		
		$ipstartStr = trim($parts[0]);
		$ipendStr = trim($parts[1]);
		if (!$ipendStr) $ipendStr = $ipstartStr;
		
		if(!(preg_match("/([0-9]{1,3}\.){3}[0-9]{1,3}/", $ipstartStr)
			&& preg_match("/([0-9]{1,3}\.){3}[0-9]{1,3}/", $ipendStr)))
		{
			$err .= "$iprange: Invalid IP address format<br>";
		}
		else
		{
			$ipstart = IPVal($ipstartStr);
			$ipend = IPVal($ipendStr);
			
			if ($userip >= $ipstart && $userip <= $ipend) {
				$err .= "$iprange: You can not block your own IP address<br>";
				
			} else {
				$sql = "INSERT INTO $t_ipblock 
						SET ipstart = $ipstart, ipend = $ipend";
				mysql_query($sql);
				if (!$msg && mysql_affected_rows()>0) $msg = "IP(s) added to the list";
			}
		}
	}
	
	if ($msg && $err) $msg = "Some of the $msg. Errors below:";
}
elseif ($_REQUEST['delete'] && $_REQUEST['ipid']) 
{
	$ips = is_array($_REQUEST['ipid']) 
			? $_REQUEST['ipid'] 
			: array($_REQUEST['ipid']);
	$iplist = implode(" OR ipid = ", $ips);

	if ($iplist)
	{
		$iplist = "ipid = " . $iplist;
		
		$sql = "DELETE FROM $t_ipblock WHERE $iplist";
		mysql_query($sql) or die(mysql_error().$sql);
		if (mysql_affected_rows()>0) $msg = "IP address(es) deleted";
	}
}

elseif ($_POST['do'] == "delall")
{
	$sql = "TRUNCATE TABLE $t_ipblock";
	mysql_query($sql) or die(mysql_error().$sql);
	$msg = "All IP addresses unblocked";
}

?>

<?php include_once("aheader.inc.php"); ?>


<script type="text/javascript" language="javascript">
function checkall(state)
{
	var n = frmIPs.elements.length;
	for (i=0; i<n; i++)
	{
		if (frmIPs.elements[i].name == "ipid[]") frmIPs.elements[i].checked = state;
	}
}
</script>


<?php if($demo) { ?><div class="err">IP blocking is disabled in demo</div><?php } ?>

<h2>Blocked IP Addresses</h2>

<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<table width="100%"><tr>
<td>
<form name="frmAddIP" method="post" action="?">

<div class="tip">
<table><tr><td valign="top">
<img src="images/tip.gif" style="float:left;">
</td><td class="tip">
Enter the IPs or IP ranges to block, one per line.<br>
An IP range may be specified using the format <b>A.B.C.D - W.X.Y.Z</b>
</td></tr></table>
</div><br>

<table><tr><td>
<textarea cols="40" rows="2" name="ips"></textarea>
<input type="hidden" name="do" value="save">
<br>
<button type="submit">Add IPs</button>
</td><td valign="top" class="hint">
<b>Eg:</b><br>
192.168.0.1<br>
192.168.10.100 - 192.168.10.255
</td></tr></table>
</form>
</td>
<td align="right">&nbsp;</td>
</tr></table>

<form method="post" action="?" name="frmIPs">
<table class="grid" cellspacing="1" cellpadding="6" width="100%">
	<tr class="gridhead">
		<td>IP Range Start</td>
		<td>IP Range End</td>
		<td width="150" align="center">Times Blocked</td>	
		<td width="60" align="center" width="40">Actions</td>
		<td width="40" align="center">
		<input type="checkbox" onclick="javascript:checkall(this.checked);"></td>
	</tr>


<?php
$sql = "SELECT * FROM $t_ipblock ORDER BY ip";
$res = mysql_query($sql);

$i = 0;
while ($row=mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");
?>

	<tr class="gridcell<?php echo $cssalt; ?>">
	
		<td><?php echo decodeIP($row['ipstart']); ?></td>
		<td>
		<?php echo ($row['ipend'] != $row['ipstart'] ? decodeIP($row['ipend']) : "-"); ?>
		</td>
		<td align="center"><?php echo $row['blocks']; ?></td>
		<td align="center"><a href="javascript:if(confirm('Delete entry?')) location.href = '?delete=1&ipid=<?php echo $row['ipid']; ?>';"><img src="images/del.gif" border="0" alt="Delete" title="Delete"></a></td>
		<td align="center">
			<input type="checkbox" name="ipid[]" value="<?php echo $row['ipid']; ?>">
		</td>
			
	</tr>

<?php
}
?>


<tr>
<td colspan="5" align="right">With selected: 
<input type="submit" name="delete" value="Delete" class="cautionbutton" onclick="return(confirm('Clear selected IPs?'));">
</td>
</tr>


</table>
</form>

<?php include_once("afooter.inc.php"); ?>