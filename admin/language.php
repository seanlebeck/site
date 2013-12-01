<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");

$lang = array();
$langcode = $_REQUEST['langcode'];


if(!empty($langcode) && !isSafeFilename($langcode)) {
	handle_security_attack();
}


if ($langcode)
{
	include("../lang/$langcode.inc.php");
}

$advanced_mode = $_REQUEST['advanced_mode'];

if($demo) $err = "Changes to the language file cannot be saved in demo";


if ($_POST['save'])
{
	if (!$demo)
	{

		if ($advanced_mode)
		{
			$langfilecontents = $_POST['langfilecontents'];
			$langfilecontents = stripslashes($langfilecontents);	

			$fp = fopen("../lang/$langcode.inc.php", "w") or die("Cannot open the language file ($langfile.inc.php). Please ensure that the file is CHMOD to 777.");;
			fwrite($fp, $langfilecontents);
			fclose($fp);
		}
		else
		{
			$newlang = $_POST['lang'];
			$newlangx = $_POST['langx'];
			
		

			foreach ($newlang as $k=>$v)
			{
				$newlang[$k] = $v = str_replace("\'", "'", $v);
			}

			foreach ($newlangx as $k=>$v)
			{
				$newlangx[$k] = $v = str_replace("\'", "'", $v);
			}

			
				
			$fp = fopen("../lang/$langcode.inc.php", "w") or die("Cannot open the language file ($langfile.inc.php). Please ensure that the file is CHMOD to 777.");
			
			fwrite($fp, "<"."?php\n\n");

			foreach ($newlangx as $k=>$v)
			{
				if (is_array($v))
				{
					if(count($v)) fwrite($fp, "\$langx['$k'] = array(\"" . implode("\",\"", $v) . "\");\n");
					else fwrite($fp, "\$langx['$k'] = array();\n");
				}
				else 
				{
					fwrite($fp, "\$langx['$k'] = \"$v\";\n");
				}
			}

			fwrite($fp, "\n");

			fwrite($fp, "\$lang = array(\n");

			foreach ($newlang as $k=>$v)
			{
				fwrite($fp, "'$k' => \"$v\",\n");
			}

			fwrite($fp, ");\n\n");

			fwrite($fp, "?".">");
			fclose($fp);

			$lang = $newlang;
			$langx = $newlangx;
		}
	
		if(!$err) $msg = "Language file $langcode.inc.php updated";

	}

}


?>

<?php include_once("aheader.inc.php"); ?>

<h2>Edit Language</h2>
<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<table width="100%" cellspacing="0" cellpadding="0"><tr>

<td>
<b>Language:</b>&nbsp;
<select onchange="if(this.value) location.href='?langcode='+this.value+'&advanced_mode=<?php echo $advanced_mode; ?>';">
<option value="">- Languages -</option>
<?php
$files = glob("../lang/*.inc.php");
foreach($files as $filepath)
{
	$lng = substr(basename($filepath), 0, -8);
	if (filetype($filepath) == "file" && substr($lng,0,1) != "_")
	{
		echo "<option value=\"$lng\"".($langcode==$lng ? " selected" : "").">".strtoupper($lng).($lng==$language?" (selected)":"")."</option>";
	}
}
?>
</select>
</td>

<td align="right">
<?php if($advanced_mode) { ?>
<a href="?langcode=<?php echo $langcode; ?>&advanced_mode=0">Easy Mode</a>
<?php } else { ?>
<a href="?langcode=<?php echo $langcode; ?>&advanced_mode=1">Advanced Mode</a>
<?php } ?>
</td>

</tr></table>

<?php if($advanced_mode) { ?>
<br>
<div class="warnbox"><span class="head">WARNING!</span><br>Be careful while editing the language file in advanced mode. If something is done wrong, the script will stop to function. DO NOT use advanced mode unless you are familiar with PHP.</div><br>
<?php } ?>

<?php

if($langcode)
{ 
	if($advanced_mode)
	{

?>

	<form name="frmLangAdvanced" action="?" method="post" class="box">

	<table border="0">

	<tr><td>
	<textarea cols="75" rows="25" name="langfilecontents" wrap="off"><?php readfile("../lang/$langcode.inc.php"); ?></textarea>
	</td></tr>

	<tr><td>
	<input type="hidden" name="langcode" value="<?php echo $langcode; ?>">
	<input type="hidden" name="save" value="1">
	<input type="hidden" name="advanced_mode" value="1">
	<button type="submit" value="Save Language">Save Language</button>
	</td></tr>
	</table>
	</form>

<?php

	} 
	else
	{

?>

	<form name="frmLang" action="?" method="post" class="box">

	<table border="0">

	<?php
	foreach($langx as $k=>$v)
	{
	?>
	<tr>
	<td><b><?php echo $k; ?>&nbsp;</b></td>
	<td><input type="text" size="55" name="langx[<?php echo $k; ?>]" value="<?php echo htmlspecialchars(stripslashes($v)); ?>"></td>
	</tr>
	<?php
	}
	?>

	<tr><td colspan="2" height="30">&nbsp;</td></tr>

	<?php
	foreach($lang as $k=>$v)
	{
	?>
	<tr>
	<td><b><?php echo $k; ?></b></td>
	<td><input type="text" size="55" name="lang[<?php echo $k; ?>]" value="<?php echo htmlspecialchars(stripslashes($v)); ?>"></td>
	</tr>
	<?php
	}
	?>

	<tr>
	<td>&nbsp;</td>
	<td>
	<input type="hidden" name="langcode" value="<?php echo $langcode; ?>">
	<input type="hidden" name="save" value="1">
	<button type="submit" value="Save Language">Save Language</button>
	</td>
	</tr>
	</table>
	</form>

<?php

	}
}
else
{

?>

<form class="box">
<div align="center" class="info">Please select a langauge to edit</div>
</form>

<?php 

}

?>

<?php include_once("afooter.inc.php"); ?>