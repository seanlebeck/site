<?php


if ( ($pay_enable_paypal + $pay_enable_google + $pay_enable_2co + $pay_enable_moneyb) <= 0 )
{
	echo $lang['PAY_EMPTY_ERROR'];
}
	
if ( $enable_featured_ads && $_POST['promote']['featured'] ) 
{
	$sql = "SELECT days FROM $t_options_featured WHERE foptid = " .intval($_POST['promote']['featured']);
	$pay_row = @mysql_fetch_array(mysql_query($sql));
	$plan_type = $lang['MAKE_FEATURED'];
}

if ( $enable_extended_ads && $_POST['promote']['extended'] ) 
{
	$sql = "SELECT days FROM $t_options_extended WHERE eoptid = " .intval($_POST['promote']['extended']);
	$pay_row = @mysql_fetch_array(mysql_query($sql));
	$plan_type = $lang['MAKE_EXTENDED'];
}

$inc_ip = ($pay_inc_ip == 1) ? " - IP: {$_SERVER['REMOTE_ADDR']}" : "";

$pay_desc = "AD ID: " . ($data['isevent'] ? "E" : "A") . $adid . ", ($plan_type {$pay_row['days']} {$lang['DAYS']})" . $inc_ip;
$idforextra = ($data['isevent'] ? "E" : "A") . $adid;
				
if ( $pay_enable_google == 1 && $account_google != '12345' ) { ?>

			
			<td style="padding:5px;">
			<form action="https://checkout.google.com/cws/v2/Merchant/<?php echo $account_google ?>/checkoutForm" id="BB_BuyButtonForm" method="post" name="BB_BuyButtonForm">
			  <input type="hidden" name="item_name_1" value="<?php echo $site_name . " {$lang['PAY_TEXT']}" ?>"/>
			  <input type="hidden" name="item_description_1" value="<?php echo $pay_desc ?>"/>
			  <input type="hidden" name="item_quantity_1" value="1"/>
			  <input type="hidden" name="item_price_1" value="<?php echo $total_price; ?>"/>
			  <input type="hidden" name="item_currency_1" value="<?php echo $google_currency ?>"/>
			  <input type="hidden" name="_charset_"/>
			  <button type="submit" name="Google Checkout"><?php echo $lang['PAY_GOOGLE'] ?></button>
			</form> 
			</td>
			
			
<?php }	if ( $pay_enable_2co == 1 && $account_2co != '12345' ) { ?>


			<td style="padding:5px;">
		    <form action="https://www.2checkout.com/2co/buyer/purchase" method="post" name="c0">
			  <input type="hidden" name="sid" value="<?php echo $account_2co ?>">
			  <input type="hidden" name="cart_order_id" value="<?php echo $adid ?>">
			  <input type="hidden" name="c_name" value="<?php echo $site_name . " {$lang['PAY_TEXT']}" ?>">
			  <input type="hidden" name="c_description" value="<?php echo $pay_desc ?>">
			  <input type="hidden" name="c_prod" value="4,1">
			  <input type="hidden" name="id_type" value="1">
			  <input type="hidden" name="c_price" value="<?php echo $total_price; ?>"> 
			  <!-- <input type="hidden" name="demo" value="Y"> -->
			  <input type="hidden" name="total" value="<?php echo $total_price; ?>">
			  <button type="submit" name="submit"><?php echo $lang['PAY_2CO'] ?></button>
			</form>
			</td>
				
					
<?php } if ( $pay_enable_moneyb == 1 && $account_mb != 'NO_ONE@moneybookers.com' ) { ?>


			<td style="padding:5px;">
			<form action="https://www.moneybookers.com/app/payment.pl" method="post">
			  <input type="hidden" name="pay_to_email" value="<?php echo $account_mb ?>">
			  <input type="hidden" name="status_url" value="<?php echo $account_mb ?>"> 
			  <input type="hidden" name="language" value="EN">
			  <input type="hidden" name="amount" value="<?php echo $total_price; ?>">
			  <input type="hidden" name="currency" value="<?php echo $skrill_currency ?>">
			  <input type="hidden" name="detail1_description" value="<?php echo $pay_desc ?>">
			  <input type="hidden" name="detail1_text" value="<?php echo $site_name . " {$lang['PAY_TEXT']}" ?>">
			  <button type="submit" name="submit"><?php echo $lang['PAY_MONEYB'] ?></button>
			</form>
			</td>
			
			
<?php } if ( $pay_enable_bank == 1 ) { ?>


			<td style="padding:5px;">
			<button class="modalInput" rel="#prompt"><?php echo $lang['PAY_BANK'] ?></button>
			</td>
			
<style>
 .contact_form_modal {
    background-color:#fff;
	width:600px;
	padding:10px;
	padding-left:0px;
    display:none;
    text-align:left;
    border:2px solid #333;

    opacity:0.8;
	border-radius: 6px;
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    -moz-box-shadow: 0 0 50px #ccc;
    -webkit-box-shadow: 0 0 50px #ccc;
  }

  .contact_form_modal h2 {
    margin:0px;
    padding:5px;
	padding-left:0px;
    font-size:14px;
  }
</style>

<!-- Bank payment dialog starts here-->
<div class="contact_form_modal" id="prompt">

<div style="padding:10px;padding-top:0px;color:black;">
<h2 style="color:blue;padding-bottom:10px;"><?php echo $data['adtitle'] ?></h2>
Please send the payment of <b><?php echo $currency . $total_price; ?></b> to the following bank account.
<div style="padding:10px;margin:10px;border:1px dashed gray;background:azure;font-weight:bold;">
<?php echo $bank_details ?>
</div>

Make sure to include Ad ID as reference when making payment: <font color="crimson"><b><?php echo $idforextra; ?></b></font>
<br>
<br>
<button type="button" class="close"> Cancel </button>
</div>

</div>
<!-- Bank payment dialog ends here-->

<script>
$(document).ready(function() {
    var triggers = $(".modalInput").overlay({
      // some mask tweaks suitable for modal dialogs
      mask: {
        color: '#ebecff',
        loadSpeed: 200,
        opacity: 0.9
      },
      closeOnClick: true
  });
  });
</script>
			
			
<?php }  ?>