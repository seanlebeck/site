<?php

require_once("initvars.inc.php");
require_once("config.inc.php");

require_once("paid_cats/paid_categories_helper.php");


echo "IPN received at: " . date("r",time()) . "\n";

// Testing mode
if ($sandbox_mode)
{
	$pp_domain = "localhost";
	$pp_validatorurl = "/Sandbox/validator.php";
	$req = 'verified=$_POST[verified]';
}
else
{
	$pp_domain = "www.paypal.com";
	$pp_validatorurl = "/cgi-bin/webscr";
	$req = 'cmd=_notify-validate';
}


// Read the post from PayPal system and add 'cmd'
$fullipnA = array();
foreach ($_POST as $key => $value)
{
	$fullipnA[$key] = $value;

	$encodedvalue = urlencode(stripslashes($value));
	$req .= "&$key=$encodedvalue";
}
$fullipn = Array2Str(" : ", "\n", $fullipnA);


//mail("webmister@yoursite.com", "IPN: $site_name", $fullipn);


// Post back to PayPal system to validate
$header  = "POST $pp_validatorurl HTTP/1.0\r\n";
$header .= "Host: $pp_domain\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ($pp_domain, 80, $errno, $errstr, 30);


// Assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
$txn_type = $_POST['txn_type'];
$pending_reason = $_POST['pending_reason'];
$payment_type = $_POST['payment_type'];



// Check parameters

if (!$fp)
{
	// HTTP error
	LogTrans("HTTP Error, can't connect to Paypal");
	StopProcess();
}
else
{
	$ret = "";
	fputs ($fp, $header . $req);
	while (!feof($fp)) $ret = fgets ($fp, 1024); 
	fclose ($fp);

	if (strcmp ($ret, "VERIFIED") == 0)
	{
		// check the payment_status is Completed
		if ($payment_status != "Completed")
		{
			LogTrans("Incomplete Payment - Payment Status: $payment_status");
			StopProcess();
		}


		// Extract details from the item number and calculate price
		$items = explode("-", $item_number);
		$itemcount = count($items);

		// Get ad ID
		$uadid = $items[0];
		$adtype = substr($items[0], 0, 1);
		$adid = substr($items[0], 1);

		if ($adtype == "E")
		{
			$adtable = $t_events;
			$isevent = 1;
		}
		elseif ($adtype == "A")
		{
			$adtable = $t_ads;
			$isevent = 0;
		}
		else
		{
			LogTrans("Invalid Ad ID - AdID: $uadid");
			StopProcess();
		}

		
		// Check for existence of ad
		$sql = "SELECT adid FROM $adtable WHERE adid = $adid";
		$res = mysql_query($sql);
		if (!mysql_num_rows($res))
		{
			LogTrans("Ad does not exist - AdID: $uadid");
			StopProcess();
		}


		// check that receiver_email is your Primary PayPal email
		if ($receiver_email != $paypal_email)
		{
			LogTrans("Wrong Receiver Email - RecieverEmail: $receiver_email");
			StopProcess();
		}
		
		// check that txn_id has not been previously processed
		$sql = "SELECT txnid FROM $t_payments WHERE txnid = '$txn_id'";
		$res = mysql_query($sql) or die(mysql_error());
		if (mysql_num_rows($res) || !$txn_id)
		{
			// Entry present
			LogTrans("Invalid/Duplicate Transaction - TxnID: $txn_id");
			StopProcess();
		}


		// Recalculate price
		$total_price = 0.00;   
		for ($i=1; $i<$itemcount; $i++)
		{
			$item = $items[$i];
			preg_match("/([A-Z]+)([0-9]+)/", $item, $itemdet);
			
			switch($itemdet[1])
			{
				
				case "NEW":
				
					if ($adtype == "A") {
						$fee = $paidCategoriesHelper->getPostingFeeForAd($adid);
						$total_price += $fee;
						$selpromos['posting_fee'] = TRUE;
					}
					
				break;
				
				
				case "FEA":

					$sql = "SELECT days, price FROM $t_options_featured WHERE foptid = $itemdet[2]";
					$fdet = @mysql_fetch_array(mysql_query($sql));

					if (!$fdet)
					{
						LogTrans("Invalid Featured Ad Option - OptionID: $item");
						StopProcess();
					}

					$total_price += $fdet['price'];

					$selpromos['featured'] = TRUE;
				
				break;
				

				case "EXA":

					$sql = "SELECT days, price FROM $t_options_extended WHERE eoptid = $itemdet[2]";
					$edet = @mysql_fetch_array(mysql_query($sql));

					if (!$edet)
					{
						LogTrans("Invalid Extended Ad Option - OptionID: $item");
						StopProcess();
					}

					$total_price += $edet['price'];

					$selpromos['extended'] = TRUE;

				break;
				
				case "URG":

					$sql = "SELECT urgent_cost FROM $t_subcats";
					$urgdet = @mysql_fetch_array(mysql_query($sql));

					if (!$urgdet)
					{
						LogTrans("Invalid Urgent Ad Option - OptionID: $item");
						StopProcess();
					}

					$total_price += $urgdet['urgent_cost'];

					$selpromos['urgent'] = TRUE;
				
				break;

				default:

					LogTrans("Invalid Item - ItemNumber: $item_number; ErrorAt: $item");
					StopProcess();

			}
		}


		// check that payment_amount/payment_currency are correct
		if ((0.00+$payment_amount) < $total_price)    
		{
			LogTrans("Wrong Amount - Received: $payment_amount$payment_currency; Expected: $total_price$paypal_currency");
			StopProcess();
		}
		
		if ($payment_currency != $paypal_currency)
		{
			LogTrans("Wrong Currency - Received: $payment_amount$payment_currency; Expected: $total_price$paypal_currency");
			StopProcess();
		}


		// SUCCESS. 

		// Save payment.
		$sql = <<< EOB
		INSERT INTO $t_payments 
		SET	txnid = '$txn_id',
			adid = $adid,
			adtype = '$adtype',
			itemname = '$item_name',
			itemnumber = '$item_number',
			amount = '$payment_amount',
			currency = '$payment_currency',
			payeremail = '$payer_email',
			paymenttype = '$txn_type',
			verified = '$ret',
			status = '$payment_status',
			pendingreason = '$pending_reason',
			fullipn = '$fullipn',
			receivedat = NOW()
EOB;
		mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");

		$sql = "SELECT LAST_INSERT_ID() FROM $t_payments";
		list($paymentid) = mysql_fetch_array(mysql_query($sql));
		
		// Selected promotions
		$promodesc = array();
		$promodesc[] = "";
		
		
		// Posting fee
		if($selpromos['posting_fee']) {
			$promodesc[] = "Posting fee : {$paypal_currency_symbol}{$fee}";
			$paidCategoriesHelper->markAdPaid($adid) or failInsert(mysql_error()."\n\n$sql");	
		}
	
		

		// Featured
		if($selpromos['featured'])
		{
			$promodesc[] = "Featured ad ($fdet[days] days) : $paypal_currency_symbol$fdet[price]";
			
			$startdate = time();

			// Make featured
			$sql = "SELECT UNIX_TIMESTAMP(featuredtill) AS featuredtill
					FROM $t_featured
					WHERE adid = $adid AND adtype = '$adtype'";
			$curfeat = mysql_query($sql);

			if(mysql_num_rows($curfeat))
			{
				list($featuredtill) = mysql_fetch_array($curfeat);
				
				$newfeaturedtill = ($featuredtill>$startdate?$featuredtill:$startdate) + $fdet['days']*24*60*60;
				$newfeaturedtill_dt = date("Y-m-d H:i:s", $newfeaturedtill);

				$sql = "UPDATE $t_featured
						SET featuredtill = '$newfeaturedtill_dt'
						WHERE adid = $adid AND adtype = '$adtype'";
				mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");	
			}
			else
			{
				$newfeaturedtill = $startdate + $fdet['days']*24*60*60;
				$newfeaturedtill_dt = date("Y-m-d H:i:s", $newfeaturedtill);

				$sql = "INSERT INTO $t_featured
						SET adid = $adid,
							adtype = '$adtype',
							featuredtill = '$newfeaturedtill_dt'";
				mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");
			}
			
			// Extend the ad if its featured for longer than the ad period.
			$sql = "SELECT UNIX_TIMESTAMP(expireson) AS expiry FROM $adtable WHERE adid = $adid";
			list($expiry) = mysql_fetch_array(mysql_query($sql));
			
			if ($newfeaturedtill > $expiry) {
				$sql = "UPDATE $adtable SET expireson = '$newfeaturedtill_dt' WHERE adid = $adid";
				mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");
			}
			
			// Log promotion
			$sql = "INSERT INTO $t_promos_featured
					SET adid = $adid,
						adtype = '$adtype',
						days = $fdet[days],
						amountpaid = $fdet[price],
						paymentid = $paymentid";
			mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");
		}

		// Extended
		if($selpromos['extended'])
		{
			$promodesc[] = "Extended ad ($edet[days] days) : $paypal_currency_symbol$edet[price]";
			
			// Extend date
			$sql = "SELECT UNIX_TIMESTAMP(expireson) AS expiry FROM $adtable WHERE adid = $adid";
			list($expiry) = mysql_fetch_array(mysql_query($sql));

			if ($expiry < time()) $expiry = time();

			$newexpiry = $expiry + $edet['days']*24*60*60;
			$newexpiry_dt = date("Y-m-d H:i:s", $newexpiry);

			$sql = "UPDATE $adtable SET expireson = '$newexpiry_dt' WHERE adid = $adid";
			mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");

			// Log extension
			$sql = "INSERT INTO $t_promos_extended
					SET adid = $adid,
						adtype = '$adtype',
						days = $edet[days],
						amountpaid = $edet[price],
						paymentid = $paymentid";
			mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");

		}
		
		
		// Urgent
		
		if($selpromos['urgent'])
		{
		
		$sql = "UPDATE $t_ads
						SET urgent_paid = '1'
						WHERE adid = $adid";
				mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");
		
		}

		
		unset($promodesc[0]);
		$promodesc = Array2Str(". ", "\n", $promodesc);
		LogTrans("Success");

	}
	else // if (strcmp ($ret, "INVALID") == 0)
	{
		LogTrans("Invalid Transaction - $ret");
	}
}

function LogTrans($ecode)
{
	global $site_email, $site_name, $t_ipns, $fullipn, $fullipnA, $promodesc;
	
	// Mail admin
	if ($ecode == "Success")
	{
		$msg = <<< EOB
Successfull Payment at $site_name

$site_name has just received a successful payment through paypal.

Payer     : $GLOBALS[payer_email]
Ad ID     : $GLOBALS[uadid]
Txn ID    : $GLOBALS[txn_id]
Item Name : $GLOBALS[item_name]
Item No   : $GLOBALS[item_number]
Amount    : $GLOBALS[payment_currency] $GLOBALS[payment_amount]

Items bought:
$promodesc

-----------------------------------------------------------------------
Availabe details from Paypal about the transaction:


EOB;
		$msg .= Array2Str(": ", "\n", $fullipnA);
	
		sendMail($site_email, "Payment Received: $site_name", $msg, $site_email, "ISO-8859-1");
	
	}
	else
	{

		$msg = <<< EOB
UNSUCCESSFULL IPN at $site_name

$site_name has just received an IPN. BUT THE PAYMENT IS NOT SUCCESSFUL. 
Please login to your paypal account and check the details.

Payer     : $GLOBALS[payer_email]
Ad ID     : $GLOBALS[uadid]
Txn ID    : $GLOBALS[txn_id]
Item Name : $GLOBALS[item_name]
Item No   : $GLOBALS[item_number]
Amount    : $GLOBALS[payment_currency] $GLOBALS[payment_amount]

Result    : $ecode

-----------------------------------------------------------------------
Availabe details from Paypal about the transaction:


EOB;

		$msg .= Array2Str(": ", "\n", $fullipnA);
	
		sendMail($site_email, "UNSUCCESSFUL IPN: $site_name", $msg, $site_email, "ISO-8859-1");
		
	}

	echo "\n".$msg;


	// Log IPN
	$sql = <<< EOB
	INSERT INTO $t_ipns 
	SET	txnid = '$GLOBALS[txn_id]',
		result = '$ecode',
		itemname = '$GLOBALS[item_name]',
		itemnumber = '$GLOBALS[item_number]',
		amount = '$GLOBALS[payment_amount]',
		currency = '$GLOBALS[payment_currency]',
		payeremail = '$GLOBALS[payer_email]',
		paymenttype = '$GLOBALS[txn_type]',
		verified = '$GLOBALS[ret]',
		status = '$GLOBALS[payment_status]',
		pendingreason = '$GLOBALS[pending_reason]',
		fullipn = '$fullipn',
		receivedat = NOW()
EOB;

	mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");

}

function StopProcess()
{
	if($fp)
	{
		fclose($fp);
		unset($fp);
	}
	exit;
}

function FailInsert($s)
{
	global $site_name, $site_email;
	global $payment_currency, $payment_amount, $payer_email, $item_name, $item_number, $uadid, $fullipnA, $fullipn;

	
	$msg = <<< EOB
IMPORTANT! UNRECORDED PAYMENT at $site_name

$site_name has just received a successfull payment. 
BUT THE PAYMENT COULD NOT BE RECORDED IN DATABASE.
Please login to your paypal account and check the details.

Payer     : $GLOBALS[payer_email]
Ad ID     : $GLOBALS[uadid]
Txn ID    : $GLOBALS[txn_id]
Item Name : $GLOBALS[item_name]
Item No   : $GLOBALS[item_number]
Amount    : $GLOBALS[payment_currency] $GLOBALS[payment_amount]

Items bought:
$promodesc

-----------------------------------------------------------------------
MySQL response:

$s

-----------------------------------------------------------------------
Availabe details from Paypal about the transaction:


EOB;

	$msg .= Array2Str(": ", "\n", $fullipnA);
	
	sendMail($site_email, "IMPORTANT! UNRECORDED PAYMENT!: $site_name", $msg, $site_email, "ISO-8859-1");
	

	echo "\n".$msg;
	StopProcess();

}

function Array2Str($kvsep, $entrysep, $a)
{
	$str = "";
	foreach ($a as $k=>$v)
	{
		$str .= "{$k}{$kvsep}{$v}{$entrysep}";
	}
	return $str;
}

?>