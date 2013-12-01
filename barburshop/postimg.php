<?php



require_once("initvars.inc.php");
require_once("config.inc.php");

$qs = "";
foreach($_GET as $k=>$v) $qs .= "$k=$v&";


if($image_verification) 
{
	require_once("captcha.cls.php");
	$captcha = new captcha();
}


if ($_POST['do'] == "post")
{
	$data = $_POST;

	recurse($data, 'stripslashes');


	if (!$_POST['imgtitle'] || !$_POST['postername'] || !$_POST['posteremail'])
		$err .= "&bull; $lang[ERROR_POST_FILL_ALL]<br>";
	if($_POST['posteremail'] && !ValidateEmail($_POST['posteremail']))
		$err .= "&bull; $lang[ERROR_INVALID_EMAIL]<br>";
	if($image_verification && !$captcha->verify($_POST['captcha']))
		$err .= "&bull; $lang[ERROR_IMAGE_VERIFICATION_FAILED]<br>";
	if (!$_POST['agree']) 
		$err .= "&bull; $lang[ERROR_POST_AGREE_TERMS]<br>";
	if (!in_array($_FILES['img']['type'], $pic_filetypes))
		$err .= "&bull; $lang[ERROR_POST_INVALID_PICTURE]<br>";
}


if ($_POST['do'] == "post" && !$err)
{

	foreach ($data as $k=>$v)
	{
		if ($k == "imgdesc") {
			recurse($data[$k], 'htmlspecialchars');
			recurse($data[$k], 'mysql_escape_string');
		}
		else {
			recurse($data[$k], 'htmlspecialchars');
			recurse($data[$k], 'mysql_escape_string');
		}
	}


	// Generate code
	$ip = $_SERVER['REMOTE_ADDR'];
	$code = uniqid("$ip.");
	$codemd5 = md5($code);

	$data['imgtitle'] = FilterBadWords($data['imgtitle']);
	$data['imgdesc'] = FilterBadWords($data['imgdesc']);

	// Upload image
	if($_FILES['img']['tmp_name'] && $_FILES['img']['size'] <= $pic_maxsize*1000 && isValidImage($_FILES['img'])) 
	{
		$imgfilename = SaveUploadFile($_FILES['img'], $datadir['userimgs'], TRUE, $images_max_width, $images_max_height, $images_jpeg_quality);
		
		// Reset the captcha cookie to prevent spam.
		if($image_verification) $captcha->resetCookie();

		$expiry = time()+($expire_images_after*24*60*60);
		$expiry_dt = date("Y-m-d H:i:s", $expiry);
		
	
		if ($moderate_images) {
			$enabled = '0';
		} else {
			$enabled = '1';
		}
	

		$sql = "INSERT INTO $t_imgs 
				SET imgtitle = '$data[imgtitle]',
					imgfilename = '$imgfilename',
					imgdesc = '$data[imgdesc]',
					postername = '$data[postername]',
					posteremail = '$data[posteremail]',
					showemail = '$data[showemail]',
					password = '$data[password]',
					code = '$code',
					cityid = $xcityid,
					ip = '$ip',
					verified = '0',
					enabled = '$enabled',
					createdon = NOW(),
					expireson = '$expiry_dt',
					timestamp = NOW()";

		mysql_query($sql) or die($sql.mysql_error());
		
		if (mysql_affected_rows())
		{
			// Get ID
			$sql = "SELECT LAST_INSERT_ID() FROM $t_imgs";
			list($imgid) = mysql_fetch_array(mysql_query($sql));

?>

			<h2><?php echo $lang['POST_IMAGE_SUCCESS']; ?></h2>

<?php

			// Compose the msg and mail the activation link
			$msg = file_get_contents("mailtemplates/newimg.txt");
			$msg = str_replace("{@SITENAME}", $site_name, $msg);
			$msg = str_replace("{@SITEURL}", $script_url, $msg);
			$msg = str_replace("{@IMAGETITLE}", $data['imgtitle'], $msg);
			$msg = str_replace("{@EXPIREAFTER}", $expire_images_after, $msg);
			$msg = str_replace("{@EXPIRESON}", substr($expiry_dt, 0, 10), $msg);
			//$msg = str_replace("{@PASSWORD}", $data['password'], $msg);

			$posterenc = EncryptPoster("IMG", $data['postername'], $data['posteremail']);
			if($sef_urls) $adlink = "$script_url/{$vbasedir}$xcityid/images/$posterenc/$imgid.html";
			else $adlink = "$script_url/?view=showimg&posterenc=$posterenc&imgid=$imgid&cityid=$xcityid";
			$msg = str_replace("{@IMAGEURL}", $adlink, $msg);

			$editlink = "$script_url/?view=editimg&imgid=$imgid&codemd5=$codemd5&cityid=$xcityid";
			$msg = str_replace("{@DELETEURL}", $editlink, $msg);

			// Make up the verification link
			$verificationlink = "$script_url/?view=activate&type=img&imgid=$imgid&codemd5=$codemd5&cityid=$xcityid";	
			$msg = str_replace("{@VERIFICATIONLINK}", $verificationlink, $msg);

		
			if (!@sendMail($_POST['posteremail'], $lang['MAILSUBJECT_NEW_POST'], $msg, $site_email, $langx['charset']))
			
			{
				/*if($debug) echo "<p>Error sending activation mail.<br>Mail contents are displayed for testing purposes.<br>Please go to <a href='$activationlink'>$activationlink</a> activate your post. <pre>$msg</pre>";
				else*/ die("Error sending confirmation mail");
			}
			else
			{

?>
	
			<p><?php echo $lang['POST_IMG_SUC']; ?></p>

<?php
			}

		}
		else
		{
			// Entry not added to db

?>

			<p class="error"><?php echo $lang['POST_IMG_ERROR']; ?></p>

<?php
	
		}
	}
	else
	{
		// File not uploaded

?>

		<p class="error"><?php echo $lang['POST_IMG_ERROR']; ?></p>

<?php
	
	}

?>

		<a href="?view=main&cityid=<?php echo $xcityid; ?>"><?php echo $lang['BACK_TO_HOME']; ?></a>

<?php

}

elseif ($xcityid > 0)
{

	
?>
	

<script language="javascript">
function checkPostFields(form) {
	
	var msg = '';

	if (form.elements['imgtitle'].value == ''
			|| form.elements['img'].value == ''
			|| form.elements['postername'].value == ''
			|| form.elements['posteremail'].value == ''
			<?php if ($image_verification) { ?>
			|| form.elements['captcha'].value == ''
			<?php } ?>
			) {
		msg += '<?php echo $lang['ERROR_POST_FILL_ALL']; ?>\n';
	}
	
	if (!form.elements['agree'].checked) {
		msg += '<?php echo $lang['ERROR_POST_AGREE_TERMS']; ?>\n';
	}
	
	if (msg != '') {
		alert(msg);
		return false;
	}
}
</script>


<h2><?php echo $lang['POST_IMG']; ?></h2>

<div><?php echo $lang['POST_IMG_WELCOME']; ?></div><br>

<?php if($err) echo "<div class=\"err\">$err</div><br>"; ?>


<table border="0" cellspacing="0" cellpadding="0"><tr><td>

<form action="index.php?<?php echo $qs; ?>" method="post" name="frmPostImage" enctype="multipart/form-data"
	onsubmit="return checkPostFields(this);">


<table class="postad" border="0" cellspacing="0" cellpadding="0">

	<tr>
	<td><b><?php echo $lang['POSTIMG_IMAGE_TITLE']; ?>:</b><span class="marker">*</span></td>
	<td><input name="imgtitle" type="text" id="imgtitle" size="55" maxlength="100" value="<?php echo $data['imgtitle']; ?>"><br><img src="images/spacer.gif"></td>
	</tr>

	<tr>
	<td><b><?php echo $lang['POSTIMG_IMAGE_FILE']; ?>:</b><span class="marker">*</span></td>
	<td><input name="img" type="file" size="45"><br><img src="images/spacer.gif"></td>
	</tr>

	<tr>
	<td><b><?php echo $lang['POSTIMG_IMAGE_DESCRIPTION']; ?>:</b></td>
	<td><textarea name="imgdesc" type="text" rows="5" cols="54"><?php echo $data['imgdesc']; ?></textarea><br><img src="images/spacer.gif"></td>
	</tr>

	<?php /* ?>
	<tr>
	<td valign="top"><b><?php echo $lang['POST_PASSWORD']; ?>:&nbsp;</b><span class="marker">*</span>&nbsp;</td>
	<td><input name="password" type="password" id="password" size="30" maxlength="50" value=""><br><span class="hint"><?php echo $lang['POST_PASSWORD_HINT']; ?></span><br><img src="images/spacer.gif"></td>
	</tr>
	<?php */ ?>

	<tr>
	<td valign="top"><b><?php echo $lang['POST_YOURNAME']; ?>:&nbsp;</b><span class="marker">*</span>&nbsp;</td>
	<td><input name="postername" type="text" id="postername" size="30" maxlength="50" value="<?php echo $data['postername']; ?>"><br><img src="images/spacer.gif"></td>
	</tr>
	
	<tr>
		<td valign="top"><b><?php echo $lang['POST_YOUREMAIL']; ?>:</b>&nbsp;<span class="marker">*</span> </td><td><input name="posteremail" type="text" id="posteremail" size="30" maxlength="50" value="<?php echo $data['posteremail']; ?>">

		<table border="0" cellspacing="1" cellpadding="0">
		<tr>
		<tr>
			<td><input name="showemail" type="radio" value="0" <?php if(!is_string($data['showemail']) || $data['showemail']==EMAIL_HIDE) echo "checked"; ?>></td>
			<td><?php echo $lang['POST_EMAILOPTION_HIDE']; ?></td>
		</tr>
			<td><input name="showemail" type="radio" value="1" <?php if($data['showemail']==EMAIL_SHOW) echo "checked"; ?>>&nbsp;</td>
			<td><?php echo $lang['POST_EMAILOPTION_SHOW']; ?></td>
		</tr>
		</table>
		</td>
	</tr>

	<?php
	if($image_verification)
	{
	?>

		<tr>
			<td valign="top"><b><?php echo $lang['POST_VERIFY_IMAGE']; ?>: <span class="marker">*</span></b></td>
			<td>
			<img src="captcha.png.php?<?php echo rand(0,999); ?>"><br>
			<span class="hint"><?php echo $lang['POST_VERIFY_IMAGE_HINT']; ?></span><br>
			<input type="text" name="captcha" value="">
			</td>
		</tr>

	<?php
	}
	?>

</table>
<br>

<input name="do" type="hidden" id="do" value="post">
<input type="checkbox" name="agree" value="1"> <?php echo $lang['POST_ACCEPT_TERMS']; ?><br><br>
<button type="submit"><?php echo $lang['BUTTON_POST']; ?></button></td>

</form>

</td></tr></table>


<?php

}
else
{
	header("Location: $script_url/?view=selectcity&targetview=postimg&cityid=$xcityid&lang=$xlang");
	exit;
}

?>