<div class="post_note">

<?php


require_once("initvars.inc.php");
require_once("config.inc.php");
require_once("userauth.inc.php");
if (!$auth) exit;

/*
$adid = $_REQUEST['adid'];
$adtype = $_REQUEST['adtype'];
$adtable = ($adtype=="A" ? $t_ads : $t_events);
$adview = ($adtype=="A" ? "showad" : "showevent");

*/
$data = $ad;

$showoptions = TRUE;

?>


<h2><?php echo $lang['AD_PROMOTIONS']; ?></h2>

<?php


if (count($_POST['promote']))
{
	$selpromos = array();
	if ($enable_featured_ads && $_POST['promote']['featured']) $selpromos['featured'] = $_POST['promote']['featured'];
	if ($enable_extended_ads && $_POST['promote']['extended']) $selpromos['extended'] = $_POST['promote']['extended'];


	if (count($selpromos))
	{
		$showoptions = FALSE;

		$total_price = 0;
		$item_number = ($data['isevent'] ? "E" : "A") . $adid;

	?>

		<p><?php echo $lang['SELECTED_PROMOTIONS']; ?></p>

		<table class="invoice" cellspacing="0" cellpadding="0">

		<?php 
		if($selpromos['featured']) 
		{ 
			$sql = "SELECT days, price FROM $t_options_featured WHERE foptid = {$selpromos[featured]}";
			$foption = @mysql_fetch_array(mysql_query($sql));

			if ($foption)
			{
				$item_number .= "-FEA" . $selpromos['featured'];
				$total_price += $foption['price'];

		?>

				<tr>
					<td class="firstcell"><?php echo $lang['MAKE_FEATURED']; ?></td>
					<td align="center"><?php echo $foption['days']; ?> <?php echo $lang['DAYS']; ?></td>
					<td class="maincell">
					<?php echo $paypal_currency_symbol; ?><?php echo $foption['price']; ?></td>
				</tr>

		<?php
			}
		} 
		?>

		<?php 
		if($selpromos['extended'])
		{ 
			$sql = "SELECT days, price FROM $t_options_extended WHERE eoptid = {$selpromos[extended]}";
			$eoption = @mysql_fetch_array(mysql_query($sql));

			if ($eoption)
			{
				$item_number .= "-EXA" . $selpromos['extended'];
				$total_price += $eoption['price'];
			
			?>

				<tr class="gridcell">
					<td class="firstcell"><?php echo $lang['MAKE_EXTENDED']; ?></td>
					<td align="center"><?php echo $eoption['days']; ?> <?php echo $lang['DAYS']; ?></td>
					<td class="maincell">
					<?php echo $paypal_currency_symbol; ?><?php echo $eoption['price']; ?></td>
				</tr>

		<?php
			}
		} 
		?>

		<tr class="totalrow">
			<td class="firstcell" width="150"><?php echo $lang['TOTAL_PRICE']; ?></td>
			<td align="center" width="75">&nbsp;</td>
			<td class="totalcell" width="75">
			<?php echo $paypal_currency_symbol; ?><?php echo number_format($total_price,2); ?></td>
		</tr>

		</table>
		
		<table>
		<tr>
		<?php //Vivaru.com multi payments Addon
					if ( $pay_enable_paypal == 1 ) {
					?>
	<td style="padding:5px;">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="item_name" value="Payment for ad promotions">
			<input type="hidden" name="item_number" value="<?php echo $item_number; ?>">
			<input type="hidden" name="amount" value="<?php echo $total_price; ?>">
			<input type="hidden" name="currency_code" value="<?php echo $paypal_currency; ?>">
			<input type="hidden" name="return" value="<?php echo $script_url; ?>/afterpay.php?adid=<?php echo $adid; ?>&adtype=<?php echo $adtype; ?>&cityid=<?php echo $xcityid; ?>">
			<input type="hidden" name="cancel_return" value="<?php echo $script_url; ?>/cancelpay.php?cityid=<?php echo $xcityid; ?>">
			<input type="hidden" name="business" value="<?php echo $paypal_email; ?>">
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="no_note" value="0">
			<input type="hidden" name="notify_url" value="<?php echo $script_url; ?>/ipn.php">
			<?php /* ?><input type="image" name="submit" src="images/pay.gif" border="0" alt="Make payments with PayPal, it's fast, free, and secure!"><?php */ ?>
			<button type="submit" name="submit">Pay with Paypal</button>
		</form>
		</td>
		
		<?php } //Vivaru.com multi payments Addon
					include_once("multi_payments.inc.php");
					?>
					
					</tr>
					</table>

<?php
	}
}

if($showoptions)
{
	$sql = "SELECT * FROM $t_options_featured ORDER BY days ASC";
	$res_feat = mysql_query($sql);
	$num_feat = mysql_num_rows($res_feat);

	$sql = "SELECT * FROM $t_options_extended ORDER BY days ASC";
	$res_ext = mysql_query($sql);
	$num_ext = mysql_num_rows($res_ext);
?>



<div style="border:1px dotted silver; padding:10px;background-color:#FAFAFA;">
<h3><?php echo $data['adtitle']; ?></h3>
<?php echo substr($data['addesc'], 0, 255); ?>...<br><br>
<b>Expires On:</b> <?php echo QuickDate($data['expireson_ts']); ?>
</div><br><br>




<form action="index.php?view=edit&target=promote&cityid=<?php echo $xcityid; ?>" method="post">
<table class="postad" cellspacing="0" cellpadding="0" border="0" width="100%">

<?php

	if($enable_featured_ads && $num_feat)
	{

	?>

		<tr>
			<td>
			<b><?php echo $lang['MAKE_FEATURED']; ?></b><br>
			<?php echo $lang['MAKE_FEATURED_DETAILS']; ?><br>
			<select name="promote[featured]">
			<option value="0">(<?php echo $lang['DONT_MAKE_FEATURED']; ?>)</option>
			<?php
			while ($row = mysql_fetch_array($res_feat))
			{
				echo "<option value=\"$row[foptid]\"";
				if($data['promote']['featured'] == $row['foptid']) echo " selected";
				echo ">$row[days] $lang[DAYS] ({$paypal_currency_symbol}{$row[price]})</option>\n";
			}
			?>
			</select>
			</td>
		</tr>

		<tr><td>&nbsp;</td></tr>

	<?php
		
	}

	?>


	<?php 

	if($enable_extended_ads && $num_ext) 
	{
		if ($data['subcatid'])
		{
			$sql = "SELECT expireafter FROM $t_subcats WHERE subcatid = $data[subcatid]";
			list ($expireafter) = mysql_fetch_array(mysql_query($sql));
		}
		else
		{
			$expireafter = $expire_events_after;
		}


	?>

		<tr>
			<td>
			<b><?php echo $lang['MAKE_EXTENDED']; ?></b><br>
			<?php echo $lang['MAKE_EXTENDED_DETAILS']; ?><br>
			<select name="promote[extended]">
			<option value="0">(<?php echo $lang['DONT_MAKE_FEATURED']; ?>)</option>
			<?php
			while ($row = mysql_fetch_array($res_ext))
			{
				$totaldays = $row['days'];
				echo "<option value=\"$row[eoptid]\"";
				if($data['promote']['extended'] == $row['eoptid']) echo " selected";
				echo ">+ $totaldays $lang[DAYS] ({$paypal_currency_symbol}{$row[price]})</option>\n";
			}
			?>
			</select>
			</td>
		</tr>

		<tr><td>&nbsp;</td></tr>

	<?php
	
	} 

	?>

</table>

<button type="submit"><?php echo $lang['BUTTON_UPDATE_AD']; ?></button>
<?php 

	$cancel_link = (!empty($_COOKIE[$ck_userid])) ? 'userpanel' : $adview . "&adid={$adid}&cityid={$xcityid}";

?>
<button type="button" onclick="location.href='index.php?view=<?= $cancel_link ?>';">Cancel</button><br><br>

</form>

<?php
}
?>

</div>