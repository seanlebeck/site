<?php

require_once("initvars.inc.php");
require_once("{$path_escape}config.inc.php");
require_once($acc_dir . "/" . $inc_path. "/post_security.php");
require_once('recaptchalib.php');
$publickey = "6LewF9oSAAAAAGINqFfmvtyMDnqn7GR4Mf7Og53-"; // you got this from the signup page
$privatekey = "6LewF9oSAAAAAN4MVy2h4WmlW6Kar1lCFxDLcjMn";


require_once("{$path_escape}paid_cats/paid_categories_helper.php");




if($image_verification) 
{
	require_once("{$path_escape}captcha.cls.php");
	$captcha = new captcha();
}


if($dir_sort) 
{
	$sortcatsql = "ORDER BY catname";
	$sortsubcatsql = "ORDER BY subcatname";
}
else
{
	$sortcatsql = "ORDER BY pos";
	$sortsubcatsql = "ORDER BY pos";
}


$qs = "";
foreach($_GET as $k=>$v) $qs .= "$k=$v&";

$data = array();
$data['x'] = array();

if($_REQUEST['subcatid'] && $xcityid > 0)
{
	$xsubcatid = $_REQUEST['subcatid'];
	list($xsubcathasprice, $xsubcatpricelabel, $xsubcatfields) = GetCustomFields($xsubcatid);
}


$posting_fee = 0.00;
if ($xsection == "ads" && $xcatid && $xsubcatid && $xcityid && $xcountryid) {
	$posting_fee = $paidCategoriesHelper->getPostingFee($xcatid, $xsubcatid, $xcityid, $xcountryid);
}

if ($posting_fee > 0.00) {
	$paid = '0';
} else {
	$paid = '2';
}


if ($_POST['do'] == "post")
{
	$data = $_POST;
	$data['area'] = $data['area']?$data['area']:$data['arealist'];
	recurse($data, 'stripslashes');

	if(!$data['adtitle'])
	{
		$data['adtitle'] = substr($data['addesc'], 0, $generated_adtitle_length) . ((strlen($data['addesc']) > $generated_adtitle_length) ? $generated_adtitle_append : "");

		if(strpos($data['adtitle'], "\n") > 0) $data['adtitle'] = trim(substr($data['adtitle'], 0, strpos($data['adtitle'], "\n")));
	}

    
    $value_missing = FALSE;
	if(!$data['addesc'] || (!$in_admin && !$data['email'])) {
		$err .= "&bull; $lang[ERROR_POST_FILL_ALL]<br>";
		$value_missing = TRUE;
	}


	if($in_admin)
	{
		if(!$data['email'])
		{
			$data['showemail'] = EMAIL_HIDE;
		}
		$enabled = '1';
		$verified = '1';
	}
	else
	{
		
		if($image_verification) {
		
		$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
    // What happens when the CAPTCHA was entered incorrectly
    $err .= "&bull; The Security Code wasn't entered correctly.<br>";
  } 
		
		}
		
			
		if($data['email'] && !ValidateEmail($data['email']))
			$err .= "&bull; $lang[ERROR_INVALID_EMAIL]<br>";
		if (!$_POST['agree']) 
			$err .= "&bull; $lang[ERROR_POST_AGREE_TERMS]<br>";

if (!$_POST['adtitle']) 

			$err .= "&bull; $lang[ERROR_POST_EMPTY_TITLE]<br>";
			
		if(count(explode(" ", $_POST['adtitle'])) <= $post_char_title_limit && !empty($_POST['adtitle']))  

			$err .= "&bull; $lang[ERROR_POST_TOO_FEW_TITLE]<br>";
			
		if(count(explode(" ", $data['addesc'])) <= $post_char_desc_limit && !empty($data['addesc']))  

			$err .= "&bull; $lang[ERROR_POST_TOO_FEW_DESC]<br>";

		
		if ((isset($data['isevent']) && $moderate_events) || 
			(!isset($data['isevent']) && $moderate_ads)) {
				
			$enabled = '0';
			
		} else {
			$enabled = '1';
		}
		

		
		$verified = ($logged_in && $no_email_logged_in) ? '1' : '1';
		

	}

	$numerr = "";
	if($_POST['price'] && !preg_match("/^[0-9\.]*$/", $_POST['price'])) 
		$numerr .= "- $xsubcatpricelabel<br>";

	if(is_array($data['x']))
	{
		foreach ($data['x'] as $fldnum=>$val)
		{
		    
		    if (!$value_missing && $xsubcatfields[$fldnum]['REQUIRED'] && !trim($val)) 
		    {
		        $err = "&bull; $lang[ERROR_POST_FILL_ALL]<br>" . $err;
		        $value_missing = TRUE;
		    }
			else if($xsubcatfields[$fldnum]['TYPE'] == "N" && !preg_match("/^[0-9]*$/", $val))
			{
				$fldname = $xsubcatfields[$fldnum]['NAME'];
				$numerr .= " &nbsp; - {$fldname}<br>";
			}
		   
		}
	}

	if($numerr) $err .= "&bull; $lang[ERROR_POST_MUST_BE_NUMBER]<br>$numerr";

	if($err) $err = $lang['POST_ERRORS'] . "<br><br>" . $err;

}


if ($_POST['do'] == "post" && !$err)
{
	if($image_verification) $captcha->resetCookie();
	
	
	
	$abuse_reports = 0;
	
	if (!$in_admin) {
    	$spam = checkSpam($data['addesc']);
    	
    	if ($spam) {
    		$abuse_reports = $spam_indicator;
    		$enabled = '0';
    	}
    }
	
	

	$data['price'] = 0 + str_replace(",", "", $data['price']);
	$data['isevent'] = 0 + $data['isevent'];
	$data['othercontactok'] = 0 + $data['othercontactok'];

	// Generate code
	$ip = $_SERVER['REMOTE_ADDR'];
	$code = uniqid("$ip.");
	$codemd5 = md5($code);

	$data['adtitle'] = FilterBadWords($data['adtitle']);
	$data['addesc'] = FilterBadWords($data['addesc']);
	$data['area'] = FilterBadWords($data['area']);
	
	$sql_account = ($logged_in) ? "user_id = '{$logged_row['user_id']}'," : "";
	
	
	// Keep a backup of the title before saving. Will be used in verification mail.
	$unescaped_title = $data['adtitle'];

	foreach ($data as $k=>$v)
	{
		if ($k == "addesc") {
			recurse($data[$k], 'htmlspecialchars');
			recurse($data[$k], 'mysql_escape_string');
		}
		else {
			recurse($data[$k], 'htmlspecialchars');
			recurse($data[$k], 'mysql_escape_string');
		}
	}

	$sql_set = "SET adtitle = '$data[adtitle]',
					addesc = '$data[addesc]',
					area ='$data[area]',
					email = '$data[email]',
					showemail = '$data[showemail]',
					password = '$data[password]',
					code = '$code',
					cityid = $xcityid,
					urgent = '$data[urgent]',
					othercontactok = '$data[othercontactok]',
					$sql_account
					newsletter = '$data[newsletter]',
					ip = '$ip',
					verified = '$verified',
					abused = $abuse_reports,
					enabled = '$enabled',
					createdon = NOW(),
					timestamp = NOW(),";
					
	
	$sql_set .= "paid = '$paid',";


	if($_POST['isevent'])
	{
		$table = $t_events;
		$view = "showevent";
		$adtype = "event";

		$expireafter = $expire_events_after;
		$expiry = time()+($expireafter*24*60*60);
		$expiry_dt = date("Y-m-d H:i:s", $expiry);

		$starton = "$data[fy]-$data[fm]-$data[fd]";
		$endon = "$data[ty]-$data[tm]-$data[td]";

		$sql = "INSERT INTO $table 
				$sql_set
				starton = '$starton',
				endon = '$endon',
				expireson = '$expiry_dt'";
	}
	else
	{
		$table = $t_ads;
		$view = "showad";
		$adtype = "ad";

		// Get ad duration
		$expsql = "SELECT expireafter FROM $t_subcats WHERE subcatid = $data[subcatid]";
		list($expireafter) = mysql_fetch_array(mysql_query($expsql));

		// Get catid
		$sql = "SELECT catid FROM $t_subcats WHERE subcatid = $data[subcatid]";
		list($catid) = mysql_fetch_row(mysql_query($sql));

		$expiry = time()+($expireafter*24*60*60);
		$expiry_dt = date("Y-m-d H:i:s", $expiry);

		$sql = "INSERT INTO $table 
				$sql_set
				subcatid = $data[subcatid],
				price = $data[price],
				expireson = '$expiry_dt'";
	}

	mysql_query($sql) or die($sql.mysql_error());
	
	if (mysql_affected_rows())
	{
		// Get ID
		$sql = "SELECT adid FROM $table WHERE adid = LAST_INSERT_ID()";
		list($adid) = mysql_fetch_array(mysql_query($sql));

		if ($adtype == "ad") {
		
			// Save extra fields
			$sql = "INSERT INTO $t_adxfields
					SET adid = $adid";

			if(count($data['x']))
			{
				foreach ($data['x'] as $fldnum=>$val)
				{
					$fldnum += 0;
					if (!$fldnum) continue;
					if($xsubcatfields[$fldnum]['TYPE'] == "N") 
					{
						//if($val == "") $val = -1;
						//else 
						$val = 0+$val;
					}
					$sql .= ", `f{$fldnum}` = '$val'";
				}
			}

			mysql_query($sql) or print($sql);
		}
		
		if($in_admin) 
		{
			$msg = "Ad has been posted";
		}
		elseif ($logged_in && $no_email_logged_in)
		{
			echo "<h2>$lang[POST_AD_SUCCESS_ACC]</h2>";
		}
		else
		{

?>

		



<?php
		}


		// Upload pictures
		if (count($_FILES['pic']['tmp_name']))
		{
			$ipval = ipval();
			$uploaderror = 0;
			$uploadcount = 0;
			
			$errorMessages = array();
			
			foreach ($_FILES['pic']['tmp_name'] as $k=>$tmpfile)
			{
				if ($tmpfile)
				{
					$thisfile = array("name"=>$_FILES['pic']['name'][$k],
						"tmp_name"=>$_FILES['pic']['tmp_name'][$k],
						"size"=>$_FILES['pic']['size'][$k],
						"type"=>$_FILES['pic']['type'][$k],
						"error"=>$_FILES['pic']['error'][$k]);			

					// Check size
					if ($_FILES['pic']['size'][$k] > $pic_maxsize*1000)
					{
					    
					    $errorMessages[] = $thisfile['name'] . " - " . $lang['ERROR_UPLOAD_PIC_TOO_BIG'];
					    
						$uploaderror++;
					}
					elseif (!isValidImage($thisfile))
					{
					    
					    $errorMessages[] = $thisfile['name'] . " - " . $lang['ERROR_UPLOAD_PIC_BAD_FILETYPE'];
					    
						$uploaderror++;
					}
					else
					{
					    
						$newfile = SaveUploadFile($thisfile, "{$path_escape}{$datadir['adpics']}", TRUE, $images_max_width, $images_max_height);
						
						if($newfile)
						{
						
						watermark($path_escape . $datadir['adpics'] . '/' . $newfile);
						
						    $sql = "INSERT INTO $t_adpics
									SET adid = $adid,
										isevent = '$data[isevent]',
										picfile = '$newfile'";
							mysql_query($sql);

							if (mysql_error())
							{
							    
								$errorMessages[] = $thisfile['name'] . " - " . $lang['ERROR_UPLOAD_PIC_INTERNAL'];
							
								$uploaderror++;
							}
							else
							{
								$uploadcount++;
							}

						}
						else
						{
						    
    						echo "<!-- {$k} - Permission error; can not copy -->";
						    $errorMessages[] = $thisfile['name'] . " - " . $lang['ERROR_UPLOAD_PIC_INTERNAL'];
    						
							$uploaderror++;
						}
					}

				}
				elseif ($_FILES['pic']['name'][$k])
				{
				    
				    echo "<!-- {$k} - Temp file not present -->";
				    
					$uploaderror++;
				}
			}

			if (!$in_admin && $uploadcount)
			{
				echo "<p>$lang[PICTURES_UPLOADED]: $uploadcount</p>";
			}
			if($uploaderror)
			{
			    
			    $errorMessageToShow = implode("<br>", $errorMessages);
				if($in_admin) $err .= "$uploaderror pictures could not be uploaded<br>{$errorMessageToShow}";
				else echo "<p class=\"err\">$lang[PICTURES_NOT_UPLOADED]: $uploaderror<br><span style=\"font-weight:normal;\">{$errorMessageToShow}</span></p>";
				
			}
		}


		if(!$in_admin)
		{
			// Compose the msg and mail the activation link
			$msg = file_get_contents("mailtemplates/newpost.txt");
			$msg = str_replace("{@SITENAME}", $site_name, $msg);
			$msg = str_replace("{@SITEURL}", $script_url, $msg);
			$msg = str_replace("{@ADTITLE}", $unescaped_title, $msg);
			//$msg = str_replace("{@PASSWORD}", $data['password'], $msg);

			// Get expiry
			if ($data['isevent']) 
			{
				$expireafter = $expire_events_after;
			}
			else
			{
				$sql = "SELECT expireafter FROM $t_subcats WHERE subcatid = $data[subcatid]";
				list($expireafter) = mysql_fetch_array(mysql_query($sql));
			}
			$msg = str_replace("{@EXPIREAFTER}", $expireafter, $msg);
			$msg = str_replace("{@EXPIRESON}", substr($expiry_dt, 0, 10), $msg);


			$verificationlink = "$script_url/?view=activate&type=$adtype&adid=$adid&codemd5=$codemd5&cityid=$xcityid";
			$msg = str_replace("{@VERIFICATIONLINK}", $verificationlink, $msg);

			if($_POST['isevent'])
			{
				if($sef_urls) $adlink = "$script_url/{$vbasedir}$xcityid/events/$starton/$adid.html";
				else $adlink = "$script_url/?view=showevent&adid=$adid&cityid=$xcityid";
			}
			else
			{
				if($sef_urls) $adlink = "$script_url/{$vbasedir}$xcityid/posts/$catid/$data[subcatid]/$adid.html";
				else $adlink = "$script_url/?view=showad&adid=$adid&cityid=$xcityid";
			}

			$msg = str_replace("{@ADURL}", $adlink, $msg);

			$editlink = "$script_url/?view=edit&isevent=$_POST[isevent]&adid=$adid&codemd5=$codemd5&cityid=$xcityid";
			$msg = str_replace("{@EDITURL}", "$editlink", $msg);

            $subj = $lang['MAILSUBJECT_NEW_POST'];
            $subj = str_replace("{@ADTITLE}", $unescaped_title, $subj);
			
			
			if ( $logged_in && $no_email_logged_in )
			{
				$emailer = '';
				$acc_send_email = 1;
			}
			else
			{
				$emailer = @sendMail($_POST['email'], $subj, $msg, $site_email, $langx['charset']);
				$acc_send_email = '';
			}
			
			if (!$emailer && !$acc_send_email)
			{

				if($debug) echo "<p>Error sending activation mail.<br>Mail contents are displayed for testing purposes.<br>Please go to <a href='$activationlink'>$activationlink</a> activate your post. <pre>$msg</pre>";
				else die("Error sending confirmation mail");
			}
			else
			{

?>
	
				<p>
					<?php 
					

$acc_link = ($sef_urls) ? 'account.html' : 'index.php?view=userpanel';

					if ( $logged_in && $no_email_logged_in )
					{
						echo $lang['VERIFICATION_NO_MAIL_ACC'];

?>

<br><br><a href="<?= $acc_link ?>"><?php echo $lang['ACC_RETURN']; ?></a>

<?php





					}
					else
					{
?>

<div id="rnd_container">
<b class="rnd_top"><b class="rnd_b1"></b><b class="rnd_b2"></b><b class="rnd_b3"></b><b class="rnd_b4"></b></b>
<div class="rnd_content"> 


<?php

						echo $lang['VERIFICATION_MAIL_SENT']; 
   						?>



<BR>
						* <?php echo $lang['CONFIRMATION_ADTITLE']; ?> <b><?php echo $data['adtitle'] ?></b>
<BR>

    						* <?php echo $lang['CONFIRMATION_ADEMAIL']; ?>  <b><?php echo $data['email'] ?></b>

<BR><BR>
<?php echo $lang['THANKS_FOR_VISITING']; ?><br>						


</div>
<b class="rnd_bottom"><b class="rnd_b4"></b><b class="rnd_b3"></b><b class="rnd_b2"></b><b class="rnd_b1"></b></b>
</div>




						<?php



					}
				
					?>
					</p>

<?php

				$selpromos = array();
				if ($enable_featured_ads && $_POST['promote']['featured']) $selpromos['featured'] = $_POST['promote']['featured'];
				if ($enable_extended_ads && $_POST['promote']['extended']) $selpromos['extended'] = $_POST['promote']['extended'];
				
				// BEGIN Charge On Upload Addon Code
				$sql = "SELECT upload_cost, upload_fields FROM $t_subcats WHERE subcatid='$xsubcatid'";
				$uoption = @mysql_fetch_array(mysql_query($sql));
				if ( $enable_extra_uploads && $_POST['mod_uploads'] && ($uploadcount > $pic_count ) ) 
				{
					$selpromos['uploads'] = 1;
				}
				// END Charge On Upload Addon Code
				
				// BEGIN Charge for URGENT tag Addon Code
				$sql = "SELECT urgent_cost FROM $t_subcats WHERE subcatid='$xsubcatid'";
				$urgentoption = @mysql_fetch_array(mysql_query($sql));
				$total_price += $urgentoption['urgent_cost'];
				if ( $enable_urgent_tag && $_POST['urgent'] && $total_price > 0.00 ) 
				{
					$selpromos['urgent'] = 1;
				}
				
				else
				
				{
				
				
				if ($_POST['urgent']) {
				
				$sql = "UPDATE $t_ads
						SET urgent_paid = '1'
						WHERE adid = $adid";
				mysql_query($sql) or failInsert(mysql_error()."\n\n$sql");
				
				}
				
				}
				// END Charge for URGENT tag Addon Code
				
				if ($posting_fee > 0.00) $selpromos['posting_fee'] = true;
				


				if (count($selpromos))
				{
					$total_price = 0;
					$item_number = ($data['isevent'] ? "E" : "A") . $adid;

				?>

				

					<p style="padding-bottom:10px;"><b><?php echo $lang['SELECTED_PROMOTIONS']; ?></b></p>

					<table cellspacing="0" cellpadding="0" bgcolor="#DEFFCD" style="border:1px solid orange;margin-left:5px;">

					<?php
					
					
					if ($selpromos['posting_fee']) {
						$item_number .= "-NEW1";
						$total_price += $posting_fee;
					?>
					
						<tr>
							<td class="firstcell"><?php echo $lang['POSTING_FEE']; ?></td>
							<td align="center">&nbsp;</td>
							<td class="maincell">
							<?php echo $paypal_currency_symbol; ?><?php echo number_format($posting_fee, 2); ?>
							</td>
						</tr>
						
					<?php 
					}
					
					
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
								<td style="border-right:1px dotted green;padding:2px;"><?php echo $lang['MAKE_FEATURED']; ?></td>
								<td align="center" style="border-right:1px dotted green;padding:2px;"><?php echo $foption['days']; ?> <?php echo $lang['DAYS']; ?></td>
								<td style="border-right:1px dotted green;padding:2px;">
								<?php echo $paypal_currency_symbol; ?><?php echo $foption['price']; ?></td>
							</tr>

					<?php
						}
					} 
					?>
					
					
					
			<?php 
					// BEGIN Charge for URGENT tag Addon Code
					if($selpromos['urgent'])
					{ 
						$sql = "SELECT urgent_cost FROM $t_subcats WHERE subcatid='$xsubcatid'";
						$urgentoption = @mysql_fetch_array(mysql_query($sql));

						if ( $urgentoption  )
						{
							$item_number .= "-URG" . $selpromos['urgent'];
							$total_price += $urgentoption['urgent_cost'];
						
						?>

							<tr>
								<td style="border-right:1px dotted green;padding:2px;">URGENT Ad</td>
								<td align="center" style="border-right:1px dotted green;padding:2px;">-</td>
								<td style="border-right:1px dotted green;padding:2px;">
								<?php echo $paypal_currency_symbol; ?><?php echo $urgentoption['urgent_cost']; ?></td>
							</tr>

					<?php
						}
					} 
					// END Charge for URGENT tag Addon Code
					?>		
					
					

<?php 
					// BEGIN Charge On Upload Addon Code
					if($selpromos['uploads'])
					{ 
						$sql = "SELECT upload_cost, upload_fields FROM $t_subcats WHERE subcatid='$xsubcatid'";
						$uoption = @mysql_fetch_array(mysql_query($sql));

						if ( $uoption  )
						{
							$item_number .= "-UPL" . $selpromos['uploads'];
							$total_price += $uoption['upload_cost'];
						
						?>

							<tr>
								<td style="border-right:1px dotted green;padding:2px;"><?php echo $lang['MOD_UPLOAD']; ?></td>
								<td align="center" style="border-right:1px dotted green;padding:2px;"><?php echo $uploadcount; ?> <?php echo $lang['MOD_FIELD']; ?></td>
								<td style="border-right:1px dotted green;padding:2px;">
								<?php echo $paypal_currency_symbol; ?><?php echo $uoption['upload_cost']; ?></td>
							</tr>

					<?php
						}
					} 
					// END Charge On Upload Addon Code
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

							<tr>
								<td style="border-right:1px dotted green;padding:2px;"><?php echo $lang['MAKE_EXTENDED']; ?></td>
								<td align="center" style="border-right:1px dotted green;padding:2px;"><?php echo $eoption['days']; ?> <?php echo $lang['DAYS']; ?></td>
								<td style="border-right:1px dotted green;padding:2px;">
								<?php echo $paypal_currency_symbol; ?><?php echo $eoption['price']; ?></td>
							</tr>

					<?php
						}
					} 
					?>

					<tr>
						<td style="border-right:1px dotted green;padding:2px;background-color:pink;font-weight:bold;" width="200"><?php echo $lang['TOTAL_PRICE']; ?></td>
						<td align="center" width="75" style="border-right:1px dotted green;padding:2px;background-color:pink;">&nbsp;</td>
						<td class="totalcell" width="75" style="border-right:1px dotted green;padding:2px;background-color:pink;font-weight:bold;">
						<?php echo $paypal_currency_symbol; ?><?php echo number_format($total_price,2); ?></td>
					</tr>

					</table>
					
					<table cellspacing="0" cellpadding="0">
					<tr>
					
					<?php // Vivaru.com multi payments Addon
					if ( $pay_enable_paypal == 1 ) {
					?>
		<td style="padding:5px;padding-left:0px;">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_xclick">
						<input type="hidden" name="item_name" value="Payment for Ad promotions">
						<input type="hidden" name="item_number" value="<?php echo $item_number; ?>">
						<input type="hidden" name="amount" value="<?php echo $total_price; ?>">
						<input type="hidden" name="currency_code" value="<?php echo $paypal_currency; ?>">
						<input type="hidden" name="return" value="<?php echo $script_url; ?>/afterpay.php?adid=<?php echo $adid; ?>&adtype=<?php echo ($data['isevent'] ? "E" : "A"); ?>&cityid=<?php echo $xcityid; ?>">
						<input type="hidden" name="cancel_return" value="<?php echo $script_url; ?>/cancelpay.php?cityid=<?php echo $xcityid; ?>">
						<input type="hidden" name="business" value="<?php echo $paypal_email; ?>">
						<input type="hidden" name="no_shipping" value="1">
						<input type="hidden" name="no_note" value="0">
						<input type="hidden" name="notify_url" value="<?php echo $script_url; ?>/ipn.php">
						<?php /* ?><input type="image" name="submit" src="images/pay.gif" border="0" alt="Make payments with PayPal, it's fast, free, and secure!"><?php */ ?>
						<button type="submit" name="submit"><?php echo $lang['PAY_WITH_PAYPAL']; ?></button>
					</form>
					
					</td>
					
					<?php } //Vivaru.com multi payments Addon
					include_once("multi_payments.inc.php");
					?>
					
					</tr>
					</table>


				<?php

				}
				else
				{

				?>

					<a href="?view=main&cityid=<?php echo $xcityid; ?>"><?php echo $lang['BACK_TO_HOME']; ?></a>

				<?php

				}

			}
		}
	}
	else
	{

		if($in_admin) 
		{
			$err .= "Error posting ad";
		}
		else
		{

?>

			<p class="error"><?php echo $lang['POST_AD_ERROR']; ?></p>
			<a href="?view=main&cityid=<?php echo $xcityid; ?>"><?php echo $lang['BACK_TO_HOME']; ?></a>

<?php
		}
	
	}

?>


<?php

	if($in_admin)
	{
		header("Location: ?msg=$msg&err=$err&cityid=$_REQUEST[cityid]&subcatid=".($_POST['postevent']?"-1":$_POST['subcatid']));
		exit;
	}

}

elseif (($_GET['subcatid'] || $_GET['postevent']) && $xcityid > 0)
{
	// Show the form //
	
	recurse($data, 'htmlspecialchars');

	if($_GET['subcatid'] == -1)
	{
		$_GET['postevent'] = $_REQUEST['postevent'] = 1;
	}

	// Get the expiry
	if ($_REQUEST['postevent'])
	{
		$expireafter = $expire_events_after;
	}
	else
	{
		$sql = "SELECT expireafter FROM $t_subcats WHERE subcatid = $_REQUEST[subcatid]";
		list($expireafter) = mysql_fetch_array(mysql_query($sql));
	}

	if ($_GET['subcatid'] > 0 || $_POST['subcatid'] > 0)
	{
		$subcatid = $_GET['subcatid'] ? $_GET['subcatid'] : $_POST['subcatid'];
		$sql = "SELECT cat.catid, cat.catname, scat.subcatname, 
						scat.hasprice, scat.pricelabel
				FROM $t_subcats scat 
					INNER JOIN $t_cats cat ON scat.catid = cat.catid
				WHERE subcatid = $subcatid 
					#AND scat.enabled = '1'
					#AND cat.enabled = '1'";
		list($catid, $catname, $subcatname, $hasprice, $pricelabel) = mysql_fetch_array(mysql_query($sql));
	}
	else
	{
		$subcatname = $catname = $lang['EVENTS'];
		
		// Date lists
		$dlist = "";
		for($i=1; $i<=31; $i++) $dlist .= "<option value=\"$i\">$i</option>\n";
		
		$mlist = "";
		for ($i=1; $i<=12; $i++) $mlist .= "<option value=\"$i\">".$langx['months'][$i-1]."</option>\n";
		
		$ylist = "";
		$thisy = date("Y");
		for ($i=0; $i<=1; $i++) $ylist .= "<option value=\"".($thisy+$i)."\">".($thisy+$i)."</option>";
	}

	
?>
<script language="javascript">

function insertLink(link)
{
	if (link)
	{
		var editpane = document.frmPost.addesc;
		var linkcode = "[URL]http://" + link + "[/URL]";

		editpane.focus();
		/*if (document.selection)
		{
			document.selection.createRange().text = linkcode;
		}
		else*/
		if (editpane.selectionStart || editpane.selectionStart == '0')
		{
			var selstart = editpane.selectionStart;
			var selend = editpane.selectionEnd;
			
			editpane.value = editpane.value.substring(0, selstart) + linkcode + editpane.value.substring(selend);
			editpane.selectionStart = selstart + linkcode.length;
			editpane.selectionEnd = editpane.selectionStart;
		}
		else
		{
			editpane.value = editpane.value + linkcode;
		}

		editpane.focus();
	}

}
</script>


<script language="javascript">

function insertVideo(link)
	{
	if (link)
		{
		if(link.substring(0,29)!="http://www.youtube.com/watch?"){
			alert("You did not enter a valid URL!\r\nPlease try again.");
			return false;
			}
		else{
			link = link.replace(/watch\?/,"").replace(/\=/,"/");
			}
		var editpane = document.frmPost.addesc;
		var linkcode = "[EMBED]" + link + "[/EMBED]";

		editpane.focus();
		/*if (document.selection)
		{
			document.selection.createRange().text = linkcode;
		}
		else*/
		if (editpane.selectionStart || editpane.selectionStart == '0')
			{
			var selstart = editpane.selectionStart;
			var selend = editpane.selectionEnd;
			
			editpane.value = editpane.value.substring(0, selstart) + linkcode + editpane.value.substring(selend);
			editpane.selectionStart = selstart + linkcode.length;
			editpane.selectionEnd = editpane.selectionStart;
			}
		else
			{
			editpane.value = editpane.value + linkcode;
			}

		editpane.focus();
		}
	}

</script>
	

<script language="javascript">
function checkPostFields(form) {
	
	var msg = '';
	
	var value_missing = false;


	if (form.elements['addesc'].value == ''
			|| form.elements['email'].value == ''
			<?php if ($image_verification) { ?>
			|| form.elements['captcha'].value == ''
			<?php } ?>
			) {
		msg += '<?php echo $lang['ERROR_POST_FILL_ALL']; ?>\n';
		
		value_missing = true;
		
	}
	
	if (!form.elements['agree'].checked) {
		msg += '<?php echo $lang['ERROR_POST_AGREE_TERMS']; ?>\n';
	}
	
	
	<?php 
	if(count($xsubcatfields)) {
		foreach($xsubcatfields as $fldnum=>$fld) {
		    if ($fld['REQUIRED']) {
	?>
	
	            if (!value_missing && !form.elements['x[<?php echo $fldnum; ?>]'].value) {
            		msg = '<?php echo $lang['ERROR_POST_FILL_ALL']; ?>\n' + msg;
            		value_missing = true;
	            }
	            
	<?php
	        }
	    }
	}
	?>

	
	if (msg != '') {
		alert(msg);
		return false;
	}
}
</script>


<?php if(!$in_admin) { ?>

	<h2><?php echo $lang['POST_AD']; ?></h2>

<?php } ?>


<div class="postpath"><?php echo "<b>$xcountryname</b>" . ($postable_country ? "" : " &raquo; <b>$xcityname</b>") . (($_GET['postevent'] || $_GET['shortcutcat']) ?"":" &raquo; <b>$catname</b>")." &raquo; <b>$subcatname</b>"; ?> 

<?php if(!$in_admin) { ?>
&nbsp; (<a href="?view=selectcity&targetview=post"><?php echo $lang['CHANGE']; ?></a>)
<?php } ?>
</div><br>

<?php 

if ($posting_fee > 0.00) {
?>
	<div class="post_note">
	<?php echo str_replace("{@FEE}", "{$paypal_currency_symbol}{$posting_fee}", $lang['POSTING_FEE_NOTE']); ?>
	</div>
	<br>
<?php 
}

?>




<font color="black"><?php echo str_replace("{@EXPIREAFTER}", $expireafter, $lang['POST_AD_NOTE']); ?>

</font>
<br>
<br>

<?php if($err) echo "<br><div class=\"err\">$err</div><br>"; ?>



<form action="<?php if($in_admin) echo "postad.php?cityid=$_GET[cityid]&subcatid=$_GET[subcatid]"; else echo "index.php?$qs"; ?>" method="post" name="frmPost" enctype="multipart/form-data" 
	onsubmit="return checkPostFields(this);">


<table class="postad" border="0" cellspacing="0" cellpadding="0" width="100%">

	<tr>
		<td valign="top" style="border-top:1px dotted black;">


<br>


			<b><?php echo $lang['POST_ADTITLE']; ?>:</b> <span class="marker">*</span>



 


<br>




			<input name="adtitle" type="text" id="adtitle" size="80" maxlength="300" value="<?php echo $data['adtitle']; ?>">





		</td>

	</tr>


	<tr><td>&nbsp;</td></tr>
	
	
		
	<tr>
		<td valign="top" style="border-top:1px dotted black;">


<br>


<b><?php echo $lang['POST_CONTENTS']; ?>:</b> <span class="marker">*</span><br>
		
        <?php 
		
		
        if(richTextAllowed(time())) { 
            $wmd_editor = array("name"=>"addesc", "content"=>$data['addesc']);
            include("{$path_escape}editor/wmd_editor.inc.php"); 
            
        } else {
        ?>
        
            <textarea name="addesc" cols="110" rows="15" id="addesc"><?php echo $data['addesc']; ?></textarea><br>
        
        <?php 
        } 
        
        
 
        ?>

		</td>
	</tr>



<tr><td>&nbsp;</td></tr>

	
	<tr>
		<td valign="top" style="border-top:1px dotted black;">

<br>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			
			<?php
			// Price field
			if ($hasprice)
			{
			?>
			
			<tr>
				<td><b><?php echo $pricelabel; ?>:</b></td><td><?php echo $currency; ?> <input type="text" name="price" size="10" maxlength="15" value="<?php echo $data['price']; ?>"></td>
			</tr>
			<tr><td>&nbsp;</td></tr>

			<?php
			}
			?>


			
			<?php 
			// Custom fields
			if(count($xsubcatfields))
			{
				foreach($xsubcatfields as $fldnum=>$fld)
				{
			?>
			<tr>
			    
				<td valign="top"><b><?php echo $fld['NAME']; ?>: </b>
				<?php if ($fld['REQUIRED']) { ?>
				<span class="marker">*</span>
				<?php } ?>
				</td>
			    
				<td>
				
				<?php

				switch($fld['TYPE'])
				{
					case "N":

				?>

					<input name="x[<?php echo $fldnum; ?>]" type="text" size="8" value="<?php echo $data['x'][$fldnum]; ?>">

				<?php

					break;

					case "D":

				?>

					<select name="x[<?php echo $fldnum; ?>]">
					<?php
					foreach ($fld['VALUES_A'] as $v)
					{
						echo "<option value=\"$v\"";
						if ($data['x'][$fldnum] == $v) echo " selected";
						echo ">$v</option>";
					}
					?>
					</select>

				<?php

					break;
					
					default:

				?>

					<input name="x[<?php echo $fldnum; ?>]" type="text" size="30" value="<?php echo $data['x'][$fldnum]; ?>">

				<?php

					break;

				}
				?>

				<br>
				<img src="images/spacer.gif" height="2"><br>
				</td>
			</tr>
			<?php 
				}
				echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
			}
			?>


			<?php /* ?>
			<tr>
				<td valign="top"><b><?php echo $lang['POST_PASSWORD']; ?>: </b><span class="marker">*</span> </td>
				<td><input name="password" type="password" id="password" size="30" maxlength="50" value=""><br><span class="hint"><?php echo $lang['POST_PASSWORD_HINT']; ?></span></td>
			</tr>

			<tr><td>&nbsp;</td></tr>
			<?php */ ?>
		
			

			<tr>
				<td valign="top"><b><?php echo $lang['POST_YOUREMAIL']; ?>:</b>
				<?php if(!$in_admin) { ?>&nbsp;<span class="marker">*</span><?php } ?></td>
				
				<td><input name="email" type="text" id="email" size="30" maxlength="50" value="<?php if ($logged_in) { echo $logged_row['email']; } else { echo $data['email']; } ?>">

				<table border="0" cellspacing="1" cellpadding="0">
				<tr>
					<td><input name="showemail" type="radio" value="0" <?php if(isset($data['showemail']) && $data['showemail']==EMAIL_HIDE) echo "checked"; ?>></td>
					<td><?php echo $lang['POST_EMAILOPTION_HIDE']; ?></td>
					</tr>
				<tr>
					<td><input name="showemail" type="radio" value="2" <?php if(!isset($data['showemail']) || $data['showemail']==EMAIL_USEFORM) echo "checked"; ?>></td>
					<td><?php echo $lang['POST_EMAILOPTION_USEFORM']; ?></td>
					</tr>
				<tr>
					<td><input name="showemail" type="radio" value="1" <?php if($data['showemail']==EMAIL_SHOW) echo "checked"; ?>>&nbsp;</td>
					<td><?php echo $lang['POST_EMAILOPTION_SHOW']; ?></td>
					</tr>
					</table>
				</td>
			</tr>	



	<tr><td>&nbsp;</td></tr>

	<tr>
		<td colspan="2" style="border-top:1px dotted black;">

<br>


			<b><?php echo $lang['POST_LOCATION']; ?></b>

</td>
<tr><td colspan="2">
&nbsp;
</td></tr>

<tr><td colspan="2">
<?php echo $lang['POSTCODE_ONE']; ?>
</td></tr>

<tr><td colspan="2">
&nbsp;
</td></tr>

<tr>
<td colspan="2">



			<?php
			if($location_sort) $sort = "ORDER BY areaname";
            else $sort = "ORDER BY pos";
    
			$sql = "SELECT areaname FROM $t_areas WHERE cityid = $xcityid  $sort";
			$res = mysql_query($sql);
			if (mysql_num_rows($res))
			{
			?>

			<select name="arealist" class="select" onchange="javascript:if(this.value) { this.form.area.value=this.value; this.form.area.disabled=true; } else this.form.area.disabled=false;">

			<option value="" <?php if(!$data['area']) echo "selected"; ?>>(<?php echo $lang['SELECT']; ?>)</option>
			<?php
				$other_index = 1;
				while ($row = mysql_fetch_array($res))
				{
					$other_index++;
					echo "<option value=\"$row[areaname], $xcityname, $xcountryname\"";
					if ($data['area'] == $row['areaname']) { echo " selected"; $area_inlist = TRUE; }
					echo ">$row[areaname]</option>";
				}
			?>

			
			</select>

			<?php echo $lang['OR_SPECIFY']; ?>

			<input name="area" type="text" size="30" maxlength="50" value="<?php echo $data['area']; ?>" onKeyUp="javascript:if(this.form.arealist.selectedIndex!=<?php echo $other_index; ?>) this.form.arealist.selectedIndex=<?php echo $other_index; ?>;" <?php if($area_inlist) echo "disabled"; ?>>

			<?php
			}
			else
			{
			?>


			<?php echo $lang['POSTCODE_TWO']; ?> <input name="area" type="text" size="60" maxlength="150" value="<?php echo $data['area']; ?>"> <?php echo $lang['HINT_NAME']; ?><br><?php echo $lang['HINT_EMAIL']; ?>






			<?php
			}
			?>
	

		</td>
	</tr>	

			<tr><td>&nbsp;</td></tr>


			<?php
			if($_GET['postevent'])
			{
			?>

				<tr>

					<td><b><?php echo $lang['POST_EVENT_START']; ?>:</b>  <span class="marker">*</span></td>
					<td>
					
					<select name="fm">
					<?php echo $mlist; ?>
					</select>
					
					<select name="fd">
					<?php echo $dlist; ?>
					</select> , 
					
					<select name="fy">
					<?php echo $ylist; ?>
					</select>
					
					</td>
					</tr>
				<tr>
					<td><b><?php echo $lang['POST_EVENT_END']; ?>: </b> <span class="marker">*</span></td>
					<td>
					
					<select name="tm">
					<?php echo $mlist; ?>
					</select>
					
					<select name="td">
					<?php echo $dlist; ?>
					</select> , 
					
					<select name="ty">
					<?php echo $ylist; ?>
					</select>
									
					</td>
				</tr>

				<?php
				if ($data['fm'])	
				{
				?>

					<script language="javascript">

					document.frmPost.fm.options[<?php echo $data['fm']-1; ?>].selected = true;
					document.frmPost.fd.options[<?php echo $data['fd']-1; ?>].selected = true;
					document.frmPost.fy.options[<?php echo $data['fy']-date("Y"); ?>].selected = true;
					document.frmPost.tm.options[<?php echo $data['tm']-1; ?>].selected = true;
					document.frmPost.td.options[<?php echo $data['td']-1; ?>].selected = true;
					document.frmPost.ty.options[<?php echo $data['ty']-date("Y"); ?>].selected = true;

					</script>

				<?php
				}
				?>

			<?php
			}
			?>

			
		

		</table>
		</td>
	</tr>
		
</table>


<br><br>

<?php
	// BEGIN Charge On Upload Addon Code
	
	$upload_cost = '';
	$upload_fields = '';
	
	if ( $xsubcatid && $enable_extra_uploads )
	{
		$sql = "SELECT upload_cost, upload_fields FROM $t_subcats WHERE subcatid='$xsubcatid'";
		$res_upl = mysql_query($sql);
		$num_uploads = mysql_num_rows($res_upl);
		$upl_row = mysql_fetch_array($res_upl);
		$upload_cost = $upl_row['upload_cost'];
		$upload_fields = $upl_row['upload_fields'];
		
		if ( $upload_fields )
		{
	?>
			<script type="text/javascript">
                fields = 0;
                function addInput() 
                {
                    if (fields != 1) {
                        document.getElementById('more_fields').innerHTML += "<input type='hidden' name='mod_uploads' value='1'><?php 
                        foreach (range(1, $upload_fields) as $number) 
                        {
                            echo "<input type='file' name='pic[]' size='69'><br><img src='images/spacer.gif' height='2'><br>";
                        } ?>";
                    fields = 1;
                    } else {
                        document.form.add_more.disabled=true;
                    }
                }
            </script>

	<?php
		}
		
	}

	?>


<table class="postad" cellspacing="0" cellpadding="0" border="0" width="100%">

	<tr>
		<td valign="top" style="border-top:1px dotted black;">

<br>

<b><?php echo $lang['POST_UPLOAD_PICTURES']; ?>:</b> <font size="1" color="gray"><?php echo $lang['POST_MAX_PIC_FILESIZE']; ?>:<?php echo $pic_maxsize; ?>KB</font>



<br>
<br>
		
		<?php
		for ($i=1; $i<=$pic_count; $i++)
		{	
		?>
			<input type="file" name="pic[]" size="69"><br>
			<img src="images/spacer.gif" height="2"><br>
		<?php
		}
		?>

<!-- BEGIN Charge On Upload Addon Code -->
        <?php if ( $num_uploads && $upload_fields ) { ?>
        <div id="more_fields"></div>
        <br>
        <input type="button" onClick="addInput()" name="add_more" value="<?php echo $lang['MOD_ADD'] . ' ' . $upload_fields . ' ' . $lang['MOD_MORE'] . ' ' . $paypal_currency_symbol . $upload_cost; ?>" />
        <?php } ?>
        <!-- END Charge On Upload Addon Code -->

		</td>

<td align="center" valign="middle" style="border-top:1px dotted black;">
&nbsp;
</td>
	</tr>

</table>
<br>



<br>

<?php 
if(!$in_admin && $enable_promotions) 
{ 
	$sql = "SELECT * FROM $t_options_featured ORDER BY days ASC";
	$res_feat = mysql_query($sql);
	$num_feat = mysql_num_rows($res_feat);

	$sql = "SELECT * FROM $t_options_extended ORDER BY days ASC";
	$res_ext = mysql_query($sql);
	$num_ext = mysql_num_rows($res_ext);

	if ($num_feat || $num_ext)
	{

?>

<br><br>





<?php
	}
}
?>


<?php
if($_GET['postevent'])
{
?>
	<input name="isevent" type="hidden" id="isevent" value="1">
	<input name="postevent" type="hidden" id="postevent" value="1">

<?php
}
else
{
?>
	<input name="subcatid" type="hidden" id="subcatid" value="<?php echo $subcatid; ?>">
<?php
}
?>


	<?php

	if($enable_promotions)
	{

	?>

<table width="100%" style="border:5px solid rgba(0, 128, 255, 0.498);-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;padding:10px;background:azure;">
<tr>
<td align="left">

<table cellspacing="0" cellpadding="0"  width="100%" style="" bgcolor="azure">

<tr>
<td>
<br>
<h2><?php echo $lang['AD_PROMOTIONS']; ?></h2>
<font size="1" color="green">Please select from Featured promotions below:</font>
</td>
</tr>

<tr>
<td>
&nbsp;
</td>
</tr>

	<?php

	if($enable_featured_ads && $num_feat)
	{

	?>

		<tr>
			<td>
			<b><i><?php echo $lang['MAKE_FEATURED']; ?></i></b><br>
			<?php echo $lang['MAKE_FEATURED_DETAILS']; ?><br>
			<br>
			<select name="promote[featured]">
			<option value="0"><?php echo $lang['DONT_MAKE_FEATURED']; ?></option>
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
		/*if ($data['subcatid'])
		{
			$sql = "SELECT expireafter FROM $t_subcats WHERE subcatid = $data[subcatid]";
			list ($expireafter) = mysql_fetch_array(mysql_query($sql));
		}
		else
		{
			$expireafter = $expire_events_after;
		}*/


	?>

		<tr>
			<td>
			
		
			
			<b><i><?php echo $lang['MAKE_EXTENDED']; ?></i></b><br>
			<?php echo $lang['MAKE_EXTENDED_DETAILS']; ?><br>
			<br>
			<select name="promote[extended]">
			<option value="0"><?php echo $lang['DONT_MAKE_FEATURED']; ?></option>
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
		
				<?php

} 

	?>
		
		
	<?php
	// BEGIN Charge for URGENT tag Addon Code
	
	$urgent_cost = '';
	
	if ( $xsubcatid && $enable_urgent_tag )
	{
		$sql = "SELECT urgent_cost FROM $t_subcats WHERE subcatid='$xsubcatid'";
		$res_urg = mysql_query($sql);
		$urg_row = mysql_fetch_array($res_urg);
		$urgent_cost = $urg_row['urgent_cost'];

		
	
	

	
	?>
	<tr><td>
	<br><br>
		<input type="checkbox" name="urgent" value="1" <?php if($data['urgent'] == 1) echo "checked"; ?>> <img src="images/urgent_icon.png"> Add URGENT tag to your ad - <font color="green"><b>$<?php echo $urgent_cost; ?></b> (Will display on Ads list and Ad description pages)</font>
		<br>
		
		</td></tr>

		<tr><td>&nbsp;</td></tr>
		
		<?php // END Charge for URGENT tag Addon Code 

}

		?>

</table>

</td>
<td align="right" valign="middle">
<img src="images/visamastercard.png" style="padding-right:20px;">
</td>
</tr>
</table>



<?php } ?>


<table cellspacing="0" cellpadding="0" width="100%">




	<tr><td colspan="2">&nbsp;</td></tr>

	<tr>
		<td>
		<input type="checkbox" name="othercontactok" value="1" <?php if($data['othercontactok'] == 1) echo "checked"; ?>> <?php echo $lang['POST_COMMERCIAL_CONTACT']; ?>
		</td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

<tr>
		<td>
		<input type="checkbox" name="newsletter" value="1" checked> <?php echo $lang['POST_NEWSLETTER_OPTION']; ?>
		</td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<?php if(!$in_admin) { ?>

		<tr>
			<td style="padding-bottom:5px;">
			<input type="checkbox" name="agree" value="1"> <?php echo $lang['POST_ACCEPT_TERMS']; ?>
			</td>
		</tr>

	<?php } ?>
	
	</table>


<table cellspacing="0" cellpadding="0" width="100%">
<tr>

	<?php
			if(!$in_admin && $image_verification)
			{
			?>

				
					<td valign="top" style="border-top:1px dotted black;padding-top:10px;"><b><?php echo $lang['POST_VERIFY_IMAGE']; ?>: <span class="marker">*</span></b><font size="1"> Please enter the code exactly as you see it in the image below</font><br>
					
<?php
echo recaptcha_get_html($publickey);
?>
		

		

		
					</td>
				

			<?php
			}
			?>	

			<td align="center" valign="middle" style="border-top:1px dotted black;padding-top:10px;">
			
			
<input name="do" type="hidden" id="do" value="post">
<button type="submit" style="cursor:pointer;margin:10px;border:5px solid darkorange;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;padding:5px;color:white;background:darkorange;font-size:20px;font-family:verdana;"><?php echo $lang['BUTTON_POST']; ?></button>

			
			</td>

</tr>
</table>


</form>


<?php

}

elseif ($_GET['catid'] && $xcityid > 0)
{
 
	$catid = $_GET['catid'];
	$sql = "SELECT catname AS catname, COUNT(*) AS subcatcount, subcatid, subcatname 
	        FROM $t_cats cat 
	            INNER JOIN $t_subcats scat ON cat.catid = scat.catid 
	                AND scat.enabled = '1'
	        WHERE cat.catid = $catid AND cat.enabled = '1'
	        GROUP BY cat.catid";
	$catdetails = mysql_fetch_array(mysql_query($sql));
	$catname = $catdetails['catname'];
		
	if ($shortcut_categories && $catdetails['subcatcount'] == 1
	        && $catdetails['subcatname'] == $catname) {

	    // Redirect to the lone subcategory.
	    header("Location: index.php?view=post&cityid={$xcityid}&lang={$xlang}&catid={$catid}&subcatid={$catdetails['subcatid']}&shortcutcat=1&shortcutregion={$_GET['shortcutregion']}");
	    exit;
	}

    
    
?>


<div style="height:280px;overflow:auto;border:1px solid #35608f;">

<table width="100%" height"100%" cellpadding="0" cellspacing="0">


<div style="background:#35608f;color:white;text-align:center;padding:6px;font-size:14px;font-family:verdana;">
<?php echo $lang['POST_SELECT_SUBCATEGORY']; ?>
</div>

<?php
	// Get subcategory names
	$sql = "SELECT subcatid, subcatname AS subcatname
			FROM $t_subcats
			WHERE catid = $_GET[catid]
				AND enabled = '1'
			$sortsubcatsql";	
	$res = mysql_query($sql);

	while ($row = mysql_fetch_array($res))
	{

?>

<tr>
<td align="left" valign="middle">

<a href="?view=post&cityid=<?php echo $xcityid; ?>&lang=<?php echo $xlang; ?>&catid=<?php echo $_GET['catid']; ?>&subcatid=<?php echo $row['subcatid']; ?>&shortcutregion=<?php echo $_GET['shortcutregion']; ?>"> 

<div style="border:1px solid #D3D3D3;padding-top:5px;padding-bottom:5px;padding-left:20px;font-size:12px;background-color:#FFEFD5;" onclick="changeMe3(this);">


<b><?php echo $row['subcatname']; ?></b>
    
</div>

</a>



</td>


</tr>
   

<?php
	
	}

?>

</table>



<?php

}

elseif($xcityid > 0)
{

?>



<div style="height:280px;overflow:auto;border:1px solid #35608f;">

<table width="100%" height"100%" cellpadding="0" cellspacing="0">

<div style="background:#35608f;color:white;text-align:center;padding:6px;font-size:14px;font-family:verdana;">
<?php echo $lang['POST_SELECT_CATEGORY']; ?>
</div>



<?php
	// Get category names
	$sql = "SELECT catid, catname AS catname
			FROM $t_cats
			WHERE enabled = '1'
			$sortcatsql";		
	$res = mysql_query($sql);

	while ($row = mysql_fetch_array($res))
	{

?>
       




<tr>
<td align="left" valign="middle">

<a href="javascript:ajaxpage('post.php?postevent=&cityid=<?php echo $xcityid; ?>&lang=<?php echo $xlang; ?>&catid=<?php echo $row['catid']; ?>&shortcutregion=<?php echo $_GET['shortcutregion']; ?>', 'contentarea2');">

<div style="border:1px solid #D3D3D3;padding-top:5px;padding-bottom:5px;padding-left:20px;font-size:12px;background-color:#FFEFD5;" onclick="changeMe2(this);">

<b><?php echo $row['catname']; ?></b>

</div>

</a>

</td>
</tr>


<?php
	
	}

?>



<?php if($enable_calendar) { ?>
<tr>
<td align="left" valign="middle">

<a href="?view=post&cityid=<?php echo $xcityid; ?>&lang=<?php echo $xlang; ?>&postevent=1">

<div style="border:1px solid #D3D3D3;padding-top:5px;padding-bottom:5px;padding-left:20px;font-size:12px;background-color:#FFEFD5;" onclick="changeMe2(this);">
<b><font color="green"><?php echo $lang['EVENTS']; ?></font></b>
</div>
</a>
</td>
</tr>

<?php } ?>

<?php if($enable_images) { ?>
<tr>
<td align="left" valign="middle">
<div style="border:1px solid #D3D3D3;padding-top:5px;padding-bottom:5px;padding-left:20px;font-size:12px;background-color:#FFEFD5;" onclick="changeMe2(this);">
<a href="?view=postimg&cityid=<?php echo $xcityid; ?>&lang=<?php echo $xlang; ?>">

<b><font color="green"><?php echo $lang['IMAGES']; ?></font></b>

</div>

</a>
</td>
</tr>
<?php } ?>







</table>



<?php

}
else
{
    
	$sql = "SELECT countryname, COUNT(*) AS citycount, cityid, cityname 
	        FROM $t_countries c 
	            INNER JOIN $t_cities ct ON c.countryid = ct.countryid 
	                AND ct.enabled = '1'
	        WHERE c.countryid = $xcountryid AND c.enabled = '1'
	        GROUP BY c.countryid";
	$countrydetails = mysql_fetch_array(mysql_query($sql));
print_r($countrydetails);

	if ($shortcut_regions && $countrydetails['citycount'] == 1
	        && $countrydetails['cityname'] == $countrydetails['countryname']) {

	    // Redirect to the lone city.
	    header("Location: index.php?view={$_GET['view']}&cityid={$countrydetails['cityid']}&lang={$xlang}&catid={$_GET['catid']}&subcatid={$_GET['subcatid']}&postevent={$_GET['postevent']}&shortcutregion=1");
	    exit;
	
    } else {
            
        $qsplus = "";
        foreach($_GET as $k=>$v) if($k != "view") $qsplus .= "&$k=$v";

        header("Location: $script_url/?view=selectcity&targetview=$_GET[view]{$qsplus}");
        exit;
    }

   
}

?>




