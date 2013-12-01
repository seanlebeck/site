<?php



require_once("initvars.inc.php");
require_once("config.inc.php");
require_once("pager.cls.php");

// Pager
$page = $_GET['page'] ? $_GET['page'] : 1;
$offset = ($page-1) * $images_per_page;

if ($sef_urls && !$xsearchmode)
{

    $urlformat = buildURL("imgs", array($xcityid, $xposterenc, "{@PAGE}"));
    
}
else
{

	$excludes = array('page','msg');
	$urlformat = regenerateURL($excludes) . "page={@PAGE}";

}


// The link to see all images
$allimgslink = buildURL("imgs", array($xcityid));


// View conditions

if($xposterenc) $whereplus = "AND MD5(UPPER(CONCAT('IMG', '$encryptposter_sep', a.postername, '$encryptposter_sep', a.posteremail))) = '$xposterenc'";


$whereplus .= " $loc_condn_img";

?>

<h2>

<?php if($xposterenc) { ?>

<?php echo $lang['IMAGES_BY']; ?> <?php echo $xpostername; ?>

<?php } else { ?>
<?php echo $lang['IMAGES']; ?>:
 
 <font size="1">(Click on image for full size and comments)</font>
 
 <span style="float:right;">
 <?php if($xposterenc) { ?>


<a style="text-decoration:underline;" href="<?php echo $allimgslink; ?>"><?php echo $lang['ALL_IMAGES']; ?></a> |

<?php } ?>


<a style="text-decoration:underline;" href="?view=postimg&cityid=<?php echo $xcityid; ?>&lang=<?php echo $xlang; ?>"><?php echo $lang['POST_IMG_LINK']; ?></a>


</span>

<?php } ?>

</h2>

<hr>



<table width="98%"><tr><td valign="top">

<div class="imglisting">

<?php

$sql = "SELECT COUNT(*)
		FROM $t_imgs a
			INNER JOIN $t_cities ct ON a.cityid = ct.cityid
		WHERE $visibility_condn
			$whereplus";
list($imgcount) = mysql_fetch_array(mysql_query($sql));

$sql = "SELECT a.*, UNIX_TIMESTAMP(a.createdon) AS createdon, 
			COUNT(*) AS commentcount, ic.imgid AS hascomments
		FROM $t_imgs a
			INNER JOIN $t_cities ct ON a.cityid = ct.cityid
			LEFT OUTER JOIN $t_imgcomments ic ON a.imgid = ic.imgid
		WHERE $visibility_condn 
			$whereplus
		GROUP BY a.imgid
		ORDER BY a.timestamp DESC
		LIMIT $offset, $images_per_page";
$res = mysql_query($sql) or die($sql.mysql_error());

while ($row=mysql_fetch_array($res))
{
	$posterenc = EncryptPoster("IMG", $row['postername'], $row['posteremail']);
	
	$imgurl = buildURL("showimg", array($xcityid, $posterenc, $row['imgid']));
	
	
	$imgsize = GetThumbnailSize("{$datadir[userimgs]}/{$row[imgfilename]}", $thumb_max_width, $thumb_max_height);

?>

<div class="imgitem" style="background:azure;border:1px solid gray;padding:10px;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-webkit-box-shadow:0 10px 6px -6px #777;-moz-box-shadow:0 10px 6px -6px #777;box-shadow:0 10px 6px -6px #777;">


<h2><?php echo $row['imgtitle']; ?></h2>

<?php if($row['imgdesc']) { ?><p><?php echo $row['imgdesc']; ?></p><?php } ?>

<div class="caption">

<?php echo $lang['POSTED_BY']; ?>

<?php if($row['showemail']) echo "<a href=\"mailto:$row[posteremail]\" class=\"poster\">$row[postername]</a>"; else echo "<span class=\"poster\">$row[postername]</span>"; ?>

<?php echo $lang['POSTED_ON']; ?>

<span class="time">
<?php echo QuickDate($row['createdon']); ?>
</span>

</div>

<a href="<?php echo $imgurl; ?>"><img class="img" id="img<?php echo $row['imgid']; ?>" border="0" src="<?php echo "{$datadir[userimgs]}/{$row[imgfilename]}"; ?>" width="<?php echo $imgsize[0]; ?>" height="<?php echo $imgsize[1]; ?>" style="-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-webkit-box-shadow:0 10px 6px -6px #777;-moz-box-shadow:0 10px 6px -6px #777;box-shadow:0 10px 6px -6px #777;"></a><br>

</div>


<?php

}

?>

</div>

<?php

if ($imgcount > $images_per_page)
{
	$pager = new pager($urlformat, $imgcount, $images_per_page, $page);

?>

<br>
<div>
<table cellspacing="0" cellpadding="0">
<tr><td><b><?php echo $lang['PAGE']; ?>: &nbsp;</b></td><td><?php echo $pager->outputlinks(); ?></td></tr>
</table>
</div>

<?php

}

?>

</td></tr></table>