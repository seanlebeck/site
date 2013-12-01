<?php



require_once("initvars.inc.php");
require_once("config.inc.php");


?>

<div class="imagecredits">
<b><?php echo $lang['IMAGES_CREDITS']; ?></b>&nbsp;
<select onchange="if(this.value) location.href='<?php echo $script_url; ?>/'+this.value;">
<option value="">- <?php echo $lang['SELECT']; ?> -</option>
<?php

// Poster list
$sql = "SELECT postername, posteremail, COUNT(*) AS imgcount 
		FROM $t_imgs a
			INNER JOIN $t_cities ct ON a.cityid = ct.cityid
		WHERE $visibility_condn $loc_condn_img
		GROUP BY postername, posteremail";
$res = mysql_query($sql) or die(mysql_error());

while($row = @mysql_fetch_array($res))
{
	$posterenc = EncryptPoster("IMG", $row['postername'], $row['posteremail']);

	
	$posterurl = buildURL("imgs", array($xcityid, $posterenc));
	
?>

<option value="<?php echo $posterurl; ?>" <?php if($posterenc == $xposterenc) echo "selected"; ?>><?php echo $row['postername']; ?> (<?php echo $row['imgcount']; ?>)</option>

<?php
}

?>

</select>
</div>