<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");

if($demo) $err = "Changes to the filter cannot be saved in demo";

if (!$demo && $_POST['update'])
{
	$cont = stripslashes($_POST['cont']);

	$fp = fopen("../{$datafile[spamfilter]}", "w") or die("Cannot open file {$datafile[spamfilter]} to write");
	fwrite($fp, $cont);
	fclose($fp);

	$msg = "Filter updated";
}


?>
<?php include_once("aheader.inc.php"); ?>

<h2>Spam Filter</h2>

<?php if($err) { ?><div class="err"><?php echo $err; ?></div><?php } ?>
<?php if($msg) { ?><div class="msg"><?php echo $msg; ?></div><?php } ?>

<table><tr><td valign="top"><img src="images/tip.gif" align="absmiddle">&nbsp;</td><td valign="top" class="tip">
Enter keywords expected in typical spam posts, one per line.<br>
If a post contains <b>more than <?php echo $spam_word_limit; ?> instances</b> of any of these words, it will be treated as spam and would require your approval to show up.<br>
Note: You may adjust the limit by editing the setting <b>$spam_word_limit</b> in <b>config.inc.php</b>.
</td></tr></table>

<br>

<form action="?" method="post" name="frmTemplate1" class="box">
	<table border="0"><tr>
		<td valign="top"> 
			<textarea name="cont" cols="60" rows="20"><?php readfile("../{$datafile[spamfilter]}"); ?></textarea>
			<br><br>
			<input type="hidden" name="update" value="1">
			<button type="submit">Update</button><br><br>
		</td>
	</tr></table>
</form>

<?php include_once("afooter.inc.php"); ?>