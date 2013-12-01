<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");


function printStat($sql) {
	list($stat) = mysql_fetch_row(mysql_query($sql));
	$stat += 0;
	echo number_format($stat);
}


?>
<?php include_once("aheader.inc.php"); ?>

<h1>Dashboard</h1>

<p class="tip"><img src="images/tip.gif" border="0" align="absmiddle">Use the links on the left to add categories, cities, ban users, edit settings and more...</p>



<table class="stats">
<tr>

<td class="column pay-stat-column">
<?php
$date = time();
$y = date("Y", $date);
$m = date("m", $date);
$d = date("d", $date);
$today_zero_hours = mktime(0, 0, 0, $m, $d, $y);
?>
<div class="stat pay-stat">
<a href="payments.php?fm=<?php echo $m; ?>&fd=<?php echo $d; ?>&fy=<?php echo $y; ?>&tm=<?php echo $m; ?>&td=<?php echo $d; ?>&ty=<?php echo $y; ?>">
<div class="stat-num">
<?php echo $paypal_currency_symbol; ?>
<?php printStat("SELECT SUM(amount) FROM $t_payments WHERE UNIX_TIMESTAMP(receivedat) >= $today_zero_hours"); ?>
</div>
<div class="stat-title">earned<br>today</div>
</a>
</div>


<?php
$w = date("w", $date);
$sunday_zero_hours = mktime(0, 0, 0, $m, $d-$w, $y);
$wy = date("Y", $sunday_zero_hours);
$wm = date("m", $sunday_zero_hours);
$wd = date("d", $sunday_zero_hours);
?>
<div class="stat pay-stat">
<a href="payments.php?fm=<?php echo $wm; ?>&fd=<?php echo $wd; ?>&fy=<?php echo $wy; ?>&tm=<?php echo $m; ?>&td=<?php echo $d; ?>&ty=<?php echo $y; ?>">
<div class="stat-num">
<?php echo $paypal_currency_symbol; ?>
<?php printStat("SELECT SUM(amount) FROM $t_payments WHERE UNIX_TIMESTAMP(receivedat) >= $sunday_zero_hours"); ?>
</div>
<div class="stat-title">earned<br>this week</div>
</a>
</div>

<?php
$dayone_zero_hours = mktime(0, 0, 0, $m, 1, $y);
?>
<div class="stat pay-stat">
<a href="payments.php?fm=<?php echo $m; ?>&fd=1&fy=<?php echo $y; ?>&tm=<?php echo $m; ?>&td=<?php echo $d; ?>&ty=<?php echo $y; ?>">
<div class="stat-num">
<?php echo $paypal_currency_symbol; ?>
<?php printStat("SELECT SUM(amount) FROM $t_payments WHERE UNIX_TIMESTAMP(receivedat) >= $dayone_zero_hours"); ?>
</div>
<div class="stat-title">earned<br>this month</div>
</a>
</div>
</td>


<td class="column warn-stat-column">
<div class="stat warn-stat">
<a href="ads.php?enabled=0">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_ads WHERE enabled = '0'"); ?>
</div>
<div class="stat-title">ads pending <br>approval</div>
</a>
</div>

<div class="stat warn-stat">
<a href="ads.php?subcatid=-1&enabled=0">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_events WHERE enabled = '0'"); ?>
</div>
<div class="stat-title">events pending<br>approval</div>
</a>
</div>

<div class="stat warn-stat">
<a href="images.php?enabled=0">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_imgs WHERE enabled = '0'"); ?>
</div>
<div class="stat-title">images pending<br>approval</div>
</a>
</div>
</td>

<td class="alt-column warn-stat-column">
<div class="stat warn-stat">
<a href="ads.php?verified=0">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_ads WHERE verified = '0'"); ?>
</div>
<div class="stat-title">ads pending <br>email verification</div>
</a>
</div>

<div class="stat warn-stat">
<a href="ads.php?subcatid=-1&verified=0">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_events WHERE verified = '0'"); ?>
</div>
<div class="stat-title">events pending<br>email verification</div>
</a>
</div>

<div class="stat warn-stat">
<a href="images.php?verified=0">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_imgs WHERE verified = '0'"); ?>
</div>
<div class="stat-title">images pending<br>email verification</div>
</a>
</div>
</td>

<td class="column cool-stat-column">
<div class="stat cool-stat">
<a href="ads.php?sortby=11">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_ads WHERE UNIX_TIMESTAMP(createdon) >= $today_zero_hours"); ?>
</div>
<div class="stat-title">ads<br>posted today</div>
</a>
</div>

<div class="stat cool-stat">
<a href="ads.php?subcatid=-1&sortby=11">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_events WHERE UNIX_TIMESTAMP(createdon) >= $today_zero_hours"); ?>
</div>
<div class="stat-title">events<br>posted today</div>
</a>
</div>

<div class="stat cool-stat">
<a href="images.php?sortby=10">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_imgs WHERE UNIX_TIMESTAMP(createdon) >= $today_zero_hours"); ?>
</div>
<div class="stat-title">images<br>posted today</div>
</a>
</div>
</td>

<td class="alt-column cool-stat-column">
<div class="stat cool-stat">
<a href="ads.php?verified=1&enabled=1&status=1">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_ads a WHERE $visibility_condn"); ?>
</div>
<div class="stat-title">ads<br>running</div>
</a>
</div>

<div class="stat cool-stat">
<a href="ads.php?subcatid=-1&verified=1&enabled=1&status=1">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_events a WHERE $visibility_condn"); ?>
</div>
<div class="stat-title">events<br>running</div>
</a>
</div>

<div class="stat cool-stat">
<a href="images.php?verified=1&enabled=1&status=1">
<div class="stat-num">
<?php printStat("SELECT COUNT(*) FROM $t_imgs a WHERE $visibility_condn"); ?>
</div>
<div class="stat-title">images<br>running</div>
</a>
</div>
</td>

</tr>
</table>

<p>&nbsp;</p>


	
<?php include_once("afooter.inc.php"); ?>