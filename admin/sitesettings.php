<?php


require_once("admin.inc.php");
require_once("aauth.inc.php");


if ($demo) $err = "Changes to templates cannot be saved in demo";


if(isset($_POST['filename']) && !isSafeFilename($_POST['filename'])) {
	handle_security_attack();
}



if (!$demo && $_POST['update'])
# Update settings
{
	$tpl = stripslashes($_POST['tpl']);

	$fp = fopen("../$_POST[filename]", "w") or die("Cannot open template file to write");
	fwrite($fp, $tpl);
	fclose($fp);

	$msg = "Settings successfuly updated";
}


?>
<?php include_once("aheader.inc.php"); ?>

<h2>Edit Email Templates</h2>

<br>
<div class="warnbox" style="background:pink;"><span class="head">WARNING!</span><br>Be careful while editing the settings in this file. If you make spelling or other mistake here, your website might stop working. You have to manually replace the file config.inc.php if something goes wrong. (There is a copy of this file called config.inc.php.bak)<BR></div><br>



<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<form action="?" method="post" name="frmTemplate1" class="box">
	<table border="0"><tr>
		<td valign="top"> 

			<textarea name="tpl" cols="100" rows="40"><?php readfile("../config.inc.php"); ?></textarea>
			
			<input type="hidden" name="filename" value="config.inc.php">

			<input type="hidden" name="update" value="1">

<br>

			<button type="submit">Update</button><br><br>
		</td>
		<td width="10"></td>
		
	</tr></table>
</form>