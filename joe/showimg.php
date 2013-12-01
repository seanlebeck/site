<?php




require_once("initvars.inc.php");
require_once("config.inc.php");
require_once("pager.cls.php");

if (!$ximgid)
{
	header("Location: $script_url/?view=imgs&cityid=$xcityid&lang=$xlang");
	exit;
}

$sql = "SELECT *, UNIX_TIMESTAMP(createdon) AS createdon
		FROM $t_imgs a
		WHERE $visibility_condn_admin AND imgid = $ximgid";
$img = @mysql_fetch_array(mysql_query($sql));

if (!$img)
{
	header("Location: $script_url/?view=imgs&cityid=$xcityid&lang=$xlang");
	exit;
}

$qs = "";
foreach($_GET as $k=>$v) $qs .= "$k=$v&";



// The link to see all images
$allimgslink = buildURL("imgs", array($xcityid));
$userimgslink = buildURL("imgs", array($xcityid, $xposterenc));


if ($_POST['do']=="postcomment" && $_POST['comment'])
{

	$data = $_POST;

	foreach ($data as $k=>$v)
	{
		if ($k == "comment") {
			recurse($data[$k], 'stripslashes');
			recurse($data[$k], 'htmlspecialchars');
			recurse($data[$k], 'mysql_escape_string');
		}
		else {
			recurse($data[$k], 'stripslashes');
			recurse($data[$k], 'htmlspecialchars');
			recurse($data[$k], 'mysql_escape_string');
		}
	}
	
	$data['postername'] = FilterBadWords($data['postername']);
	$data['comment'] = FilterBadWords($data['comment']);
	
	$sql = "INSERT INTO $t_imgcomments
			SET imgid = $ximgid,
			postername = '$data[postername]',
			comment = '$data[comment]'";
	mysql_query($sql) or die(mysql_error());
	if(mysql_affected_rows()) $msg = $lang['MESSAGE_COMMENT_POSTED'];
		
}


?>

<h2><?php echo "$lang[IMAGES_BY] $img[postername]" . " : " . $img['imgtitle']; ?> <span style="float:right;"><?php if($xposterenc) { ?>
<a href="<?php echo $allimgslink; ?>"><?php echo $lang['ALL_IMAGES']; ?></a> | <a href="?view=postimg&cityid=<?php echo $xcityid; ?>&lang=<?php echo $xlang; ?>"><?php echo $lang['POST_IMG_LINK']; ?></a>
<?php } ?></span></h2>
<hr>

<?php if($msg) { ?><div class="msg"><?php echo $msg ?></div><?php } ?>
<?php if($err) { ?><div class="err"><?php echo $err ?></div><?php } ?>



<table width="98%"><tr><td valign="top">

<?php

if($img)
{
	$imgsize = GetThumbnailSize("{$datadir[userimgs]}/{$img[imgfilename]}", $images_max_width, $images_max_height);

?>

<div class="imgitem">
<?php echo $lang['POST_ID']; ?> M<?php echo $img['imgid']; ?>
<div class="head"><?php echo $img['imgtitle']; ?></div><br>

<div class="caption">

<?php echo $lang['POSTED_BY']; ?>

<?php if($img['showemail']) echo "<a href=\"mailto:$img[posteremail]\" class=\"poster\">$img[postername]</a>"; else echo "<span class=\"poster\">$img[postername]</span>"; ?>

<?php echo $lang['POSTED_ON']; ?>

<span class="time">
<?php echo QuickDate($img['createdon']); ?>
</span>

</div>
<img class="img" id="img<?php echo $img['imgid']; ?>" src="<?php echo "{$datadir[userimgs]}/{$img[imgfilename]}"; ?>" width="<?php echo $imgsize[0]; ?>" height="<?php echo $imgsize[1]; ?>" style="-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-webkit-box-shadow:0 10px 6px -6px #777;-moz-box-shadow:0 10px 6px -6px #777;box-shadow:0 10px 6px -6px #777;">
<?php if($img['imgdesc']) { ?><div class="desc"><?php echo $img['imgdesc']; ?></div><?php } ?>
</div>

<?php

}

?>

<?php if ($ad_comments) { ?>
<!-- COMMENTS PLACEHOLDER START  -->
<tr><td colspan="2">
<fieldset style="border:1px solid lightblue;margin-top:0px;padding:5px;">
<legend style="color:black;font-size:12px;">&nbsp;Comments&nbsp;</legend>
<?php
    $object_id = $img['imgid']; //identify the object which is being commented
    include('comments/php/loadComments.php'); //load the comments and display    
?>

</fieldset>
</td></tr>
<!-- COMMENTS PLACEHOLDER END  -->
<?php } ?>

</td></tr></table>