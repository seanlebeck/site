<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");

if($demo) $err = "Changes to the filter cannot be saved in demo";

if (!$demo && $_POST['update'])
{
	$cont = stripslashes($_POST['cont']);

	$fp = fopen("../{$datafile[badwords]}", "w") or die("Cannot open file {$datafile[badwords]} to write");
	fwrite($fp, $cont);
	fclose($fp);

	$msg = "File updated";
}


?>
<?php include_once("aheader.inc.php"); ?>

<h2>Bad Word Filter</h2>

<?php if($err) { ?><div class="err"><?php echo $err; ?></div><?php } ?>
<?php if($msg) { ?><div class="msg"><?php echo $msg; ?></div><?php } ?>

<table><tr><td valign="top"><img src="images/tip.gif" align="absmiddle">&nbsp;</td><td valign="top" class="tip">Enter the words to be filtered out, one per line.<br>Bad words in the post will be replaced with <b><?php echo $badword_replacement; ?></b>.<br> Edit the variable <b>$badword_replacement</b> in  config.inc.php to change the replacement word.</td></tr></table>

<form action="?" method="post" name="frmTemplate1" class="box">
	<table border="0"><tr>
		<td valign="top"> 
			<textarea name="cont" cols="60" rows="20"><?php readfile("../{$datafile[badwords]}"); ?></textarea>
			<br><br>
			<input type="hidden" name="update" value="1">
			<button type="submit">Update</button><br><br>
		</td>
	</tr></table>
</form>

<?php include_once("afooter.inc.php"); ?>