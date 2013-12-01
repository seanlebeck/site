<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");


$whereA = array();
$search_desc = "";


if ($_GET['qv']) {

    $date = time();
    $y = date("Y", $date);
    $m = date("m", $date);
    $d = date("d", $date);
    
    /* End dates are always the same: today */
    $_GET['fd'] = $_GET['td'] = $d;
    $_GET['fm'] = $_GET['tm'] = $m;
    $_GET['fy'] = $_GET['ty'] = $y;
    
    switch ($_GET['qv']) {
        case "today":
            $_GET['fd'] = $d;
            $_GET['fm'] = $m;
            $_GET['fy'] = $y;
            break;
            
        case "week":
            $w = date("w", $date);
            $sunday_zero_hours = mktime(0, 0, 0, $m, $d-$w, $y);
            $wy = date("Y", $sunday_zero_hours);
            $wm = date("m", $sunday_zero_hours);
            $wd = date("d", $sunday_zero_hours);

            $_GET['fd'] = $wd;
            $_GET['fm'] = $wm;
            $_GET['fy'] = $wy;
            break;
            
        case "month":
            $_GET['fd'] = 1;
            $_GET['fm'] = $m;
            $_GET['fy'] = $y;
            break;
            
        case "year":
            $_GET['fd'] = 1;
            $_GET['fm'] = 1;
            $_GET['fy'] = $y;
            break;
    }
}


if($_GET['fd'] && $_GET['fm'] && $_GET['fy'])
{
	$_GET['fd'] = str_pad($_GET['fd'], 2, "0", STR_PAD_LEFT);
	$_GET['fm'] = str_pad($_GET['fm'], 2, "0", STR_PAD_LEFT);
	
	$start_time = mktime(0, 0, 0, $_GET['fm'], $_GET['fd'], $_GET['fy']);
	$whereA[] = "UNIX_TIMESTAMP(pay.receivedat) >= $start_time";

	
	$mname = $langx['months'][$_GET['fm']-1];
	
    $search_desc .= " after <b>$mname $_GET[fd], $_GET[fy]</b>";
	
    $glue = "and";
}
if($_GET['td'] && $_GET['tm'] && $_GET['ty'])
{
	$_GET['td'] = str_pad($_GET['td'], 2, "0", STR_PAD_LEFT);
	$_GET['tm'] = str_pad($_GET['tm'], 2, "0", STR_PAD_LEFT);
	
	$end_time = mktime(23, 59, 59, $_GET['tm'], $_GET['td'], $_GET['ty']);
	$whereA[] = "UNIX_TIMESTAMP(pay.receivedat) <= $end_time";
	
	
	$mname = $langx['months'][$_GET['tm']-1];
	
    $search_desc .= " $glue before <b>$mname $_GET[td], $_GET[ty]</b>";
    
}

$where = implode(" AND ", $whereA);
if (!$where) $where = 1; 

if($_POST['delete']) {
	$payids = array();
	$sql = "SELECT paymentid FROM $t_payments pay WHERE $where";
	$res = mysql_query($sql) or die(mysql_error());
	
	while ($row=mysql_fetch_array($res)) {
		$payids[] = $row['paymentid'];
	}
	
	if (count($payids) > 0) {
		$sql = "DELETE FROM $t_payments WHERE paymentid in (" . implode(",", $payids) . ")";
		mysql_query($sql);
		$count = mysql_affected_rows();	
		
		$sql = "DELETE FROM $t_promos_featured WHERE paymentid in (" . implode(",", $payids) . ")";
		mysql_query($sql);
		
		$sql = "DELETE FROM $t_promos_extended WHERE paymentid in (" . implode(",", $payids) . ")";
		mysql_query($sql);

		$msg = "Deleted history of $count payments $search_desc.\nShowing all the remaining payments now.";
		
	} else {
		$msg = "No payments to delete in the selected duration.\nShowing all payments now.";
	}

	header("Location: payments.php?msg=" . urlencode($msg));
	exit;
}

if ($search_desc) $search_desc_full = "Showing payments $search_desc";
else $search_desc_full = "Showing <b>all payments</b>";

$order = "pay.receivedat DESC";

$_GET['msg'] = nl2br(htmlentities($_GET['msg']));
if (!$msg) $msg = $_GET['msg'];
unset($_GET['msg']);

$thisurl = "payments.php?";
foreach ($_GET as $k=>$v) $thisurl .= "k=".urlencode($v)."&";


?>
<?php include_once("aheader.inc.php"); ?>

<script language="javascript">
function AutoSelectDates(elt, frm, fldprefix)
{
	if(elt.value)
	{
		if(frm.elements[fldprefix+'d'].value=='') frm.elements[fldprefix+'d'].selectedIndex=1;
		if(frm.elements[fldprefix+'m'].value=='') frm.elements[fldprefix+'m'].selectedIndex=1;
		if(frm.elements[fldprefix+'y'].value=='') frm.elements[fldprefix+'y'].selectedIndex=1;
	}
}
</script>

<h2>Payment History</h2>

<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<form class="box" action="" method="get" name="frmSearch">

<table cellspacing="5" width="100%">

<tr>
<td>
Payments between: 

<?php
$dateoptions_from = GetDateSelectOptions($_GET['fd'], $_GET['fm'], $_GET['fy']);
$dateoptions_to = GetDateSelectOptions($_GET['td'], $_GET['tm'], $_GET['ty']);
?>

<select name="fm" onchange="AutoSelectDates(this, this.form, 'f');">
<option value=""></option>
<?php echo $dateoptions_from['M']; ?>
</select>

<select name="fd" onchange="AutoSelectDates(this, this.form, 'f');">
<option value=""></option>
<?php echo $dateoptions_from['D']; ?>
</select> , 

<select name="fy" onchange="AutoSelectDates(this, this.form, 'f');">
<option value=""></option>
<?php echo $dateoptions_from['Y']; ?>
</select>

&nbsp;and&nbsp;

<select name="tm" onchange="AutoSelectDates(this, this.form, 't');">
<option value=""></option>
<?php echo $dateoptions_to['M']; ?>
</select>

<select name="td" onchange="AutoSelectDates(this, this.form, 't');">
<option value=""></option>
<?php echo $dateoptions_to['D']; ?>
</select> , 

<select name="ty" onchange="AutoSelectDates(this, this.form, 't');">
<option value=""></option>
<?php echo $dateoptions_to['Y']; ?>
</select>

<button type="submit">&nbsp;Go&nbsp;</button>
<button type="button" onclick="location.href='?';">View All</button>
</td>


<td>
<div style="border-left: 1px dashed #B1C7DE; margin-left: 20px; padding-left: 25px;">
<select onchange="location.href='?' + this.value;">
<option value="">- OR select a quick view -</option>
<option value="">All payments</option>
<option value="qv=today">Today</option>
<option value="qv=week">This week</option>
<option value="qv=month">This month</option>
</select>
</div>
</td>

<td>&nbsp;</td>



</tr>

</table>

</form>


<?php

if ($where)
{

	$page = $_REQUEST['page'] ? $_REQUEST['page'] : 1;
	$offset = ($page-1) * $admin_ads_per_page;

	$sql = "SELECT COUNT(*)
			FROM $t_payments pay
			WHERE $where";

	list($total) = mysql_fetch_row(mysql_query($sql));


?>


<div style="float:right; width:120px; text-align:right" class="pay-stat">
<br>
<span class="stat-title">Total</span><br>
<span class="stat-num">
<?php
$sql = "SELECT SUM(amount) AS total FROM $t_payments pay WHERE $where";
list($total_earned) = mysql_fetch_row(mysql_query($sql));
?>
<?php echo $paypal_currency_symbol; ?><?php echo number_format($total_earned, 2); ?>
</span><br>
</div>


<div class="search_desc">
<?php echo $search_desc_full; ?>
<div><span class="rescount"><?php echo $total; ?></span> transactions in this view</div>
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

<form method="post" action="" name="frmTransactions">
<table width="100%" border="0" cellpadding="6" cellspacing="1" class="grid">
	<tr>
		<td class="gridhead" width="18%" align="center">Txn ID</td>
		<td class="gridhead" width="6%" align="center">Ad ID</td>
		<td class="gridhead" width="33%">Title</td>
		<td class="gridhead" width="4%" align="center">&nbsp;</td>
		<td class="gridhead" width="20%" align="center">Payer Email</td>
		<td class="gridhead" width="12%" align="center">Date</td>
		<td class="gridhead" width="5%" align="right">Amount</td>
	</tr>

<?php

	$sql = "SELECT pay.*, UNIX_TIMESTAMP(pay.receivedat) AS ts,
				a.adtitle AS adtitleA, a.cityid AS cityidA, 
				e.adtitle AS adtitleE, e.cityid AS cityidE,
				pf.days AS fdays, pf.amountpaid AS famount,
				pe.days AS edays, pe.amountpaid AS eamount
			FROM $t_payments pay
				LEFT OUTER JOIN $t_ads a ON pay.adid = a.adid AND pay.adtype = 'A'
				LEFT OUTER JOIN $t_events e ON pay.adid = e.adid AND pay.adtype = 'E'
				LEFT OUTER JOIN $t_promos_featured pf ON pay.paymentid = pf.paymentid
				LEFT OUTER JOIN $t_promos_extended pe ON pay.paymentid = pe.paymentid
			WHERE $where
			ORDER BY $order
			LIMIT $offset, $admin_ads_per_page";
	$res = mysql_query($sql) or die($sql.mysql_error());

	$i = 0;
	while ($row = @mysql_fetch_array($res))
	{
		if($row['adtype'] == "E") 
		{
			$adlink = "../index.php?view=showevent&adid=$row[adid]&cityid=$row[cityidE]";
			$adtitle = $row['adtitleE'];
			$editlink = "editad.php?adid=$row[adid]&isevent=1&returl=".rawurlencode($thisurl);
		}
		else
		{
			$adlink = "../index.php?view=showad&adid=$row[adid]&cityid=$row[cityidA]";
			$adtitle = $row['adtitleA'];
			$editlink = "editad.php?adid=$row[adid]&isevent=0&returl=".rawurlencode($thisurl);
		}

		$i++;
		$cssalt = ($i%2 ? "" : "alt"); 

?>

		<tr class="gridcell<?php echo $cssalt; ?>">

		<td align="center" rowspan="2">
		<?php echo $row['txnid']; ?>
		</td>

		<td align="center" rowspan="2">
		<?php echo $row['adtype'].$row['adid']; ?>
		</td>
		
		<td>
		<b><a class="adlink" href="<?php echo $adlink; ?>" target="_blank"><?php echo $adtitle; ?></a></b>
		</td>

		<td align="center">
		<a href="<?php echo $editlink; ?>">
		<img src="images/edit.gif" title="Edit Ad" border="0"></a>
		</td>

		<td align="center" rowspan="2">
		<a href="mailto:<?php echo $row['payeremail']; ?>" rowspan="2"><?php echo str_replace("@","@", $row['payeremail']); ?></a>
		</td>

		<td align="center" rowspan="2"><?php echo date("M d, y", $row['ts']); ?><br>
		<?php echo date("H:i:s", $row['ts']); ?>
		</td>

		<td align="right" rowspan="2" class="gridcellmain">
		<?php echo "$paypal_currency_symbol$row[amount]"; ?>
		</td>

		</tr>


		<tr class="gridcell<?php echo $cssalt; ?>">

		<td colspan="2">
		<?php 
		$opti = 0;
		
		if (strpos($row['itemnumber'], "-NEW") !== FALSE) {
			$opti++;
		?>
		Posting Fee<br>
		<?php
		}
		
		
		if ($row['fdays']) 
		{
			$opti++;
		?>

		Featured Ad (<?php echo $row['fdays']; ?> days): <?php echo $paypal_currency_symbol; ?><?php echo $row['famount']; ?><br>
		
		<?php
		}
		if ($row['edays']) 
		{
			$opti++;
		?>
		Extended Ad (<?php echo $row['edays']; ?> days): <?php echo $paypal_currency_symbol; ?><?php echo $row['eamount']; ?><br>
		<?php 
		}
		?>
		</td>

		</tr>


<?php
	}
?>

<tr>
<td colspan="7" align="right">
<?php 
$count = mysql_num_rows($res);
if ($count > 0) {
?>

<input type="submit" name="delete" value="Delete all <?php echo $total; ?> records from the selected duration" class="cautionbutton" onclick="return(confirm('This will delete all <?php echo $total; ?> records of payments <?php echo strip_tags($search_desc); ?>. Continue?'));">

<?php
}
?>
</td>
</tr>

</table>
</form>

<?php

}

?>
<?php include_once("afooter.inc.php"); ?>