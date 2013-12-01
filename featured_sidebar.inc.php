

<?php




require_once("initvars.inc.php");
require_once("config.inc.php");

?>
<?php
if($latest_featured_ads_count)
{
?>






<?php
	$sql = "SELECT a.*, ct.cityname, UNIX_TIMESTAMP(a.createdon) AS timestamp, feat.adid AS isfeat,
				COUNT(*) AS piccount, p.picfile AS picfile, scat.subcatname, scat.catid, cat.catname
			FROM $t_ads a
				INNER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'A' AND feat.featuredtill >= NOW()
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				INNER JOIN $t_subcats scat ON a.subcatid = scat.subcatid
				INNER JOIN $t_cats cat ON scat.catid = cat.catid
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '0'
			WHERE $visibility_condn
				
			GROUP BY a.adid
			ORDER BY a.createdon DESC
			LIMIT $latest_featured_ads_count";
	$res_latest = mysql_query($sql) or die($sql.mysql_error());

	$css_first = "_first";
	while($row = mysql_fetch_array($res_latest))
	{
	
		$url = buildURL("showad", array($xcityid, $row['catid'], $row['catname'], $row['subcatid'], $row['subcatname'], $row['adid'], $row['adtitle']));
		

?>
	
		<?php 
		/*if($row['isfeat']) 
		{
			//$feat_class = "class=\"featured\"";
			$feat_img = "<img src=\"images/featured.gif\" align=\"absmiddle\">";
		} 
		else 
		{ 
			//$feat_class = "";
			$feat_img = "";
		}*/

		if($row['picfile']) 
		{
			$picfile = $row['picfile'];
			$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$picfile}", $tinythumb_max_width, $tinythumb_max_height);
		}
		else

                                {
                                $picfile = "nof.png";

                                $imgsize = GetThumbnailSize("{$datadir[adpics]}/{$row[picfile]}", $tinythumb_max_width, $tinythumb_max_height);
                                
                                
                                
				}
                                ?>


                                

		
			
			

<table width="110" style="width:110px;border:1px solid lightblue;margin:2px;margin-bottom:5px;border-radius: 5px;-moz-border-radius: 5px;background:white;">
<tr>
<td style="font-size:10px;font-family:arial;">	
<a href="<?php echo $url; ?>" <?php echo $feat_class; ?> style="color:#35608f;"><?php echo $row['adtitle']; ?></a>
</td>

</tr>

<tr>

<td>


			
			<?php if($picfile) { ?>
			<a href="<?php echo $url; ?>"><img src="<?php echo "{$datadir[adpics]}/{$picfile}"; ?>" width="100" height="80" style="margin-left:2px;border:1px solid lightblue;"></a>


			<?php } ?>

</td>
</tr>
</table>
	


		
	



<?php
		$css_first = "";
	}
?>



<?php
}
?>





