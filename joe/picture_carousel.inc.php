<?php

require_once("initvars.inc.php");
require_once("config.inc.php");

?>

<style>
    /* tooltip styling. by default the element to be styled is .tooltip  */
  .tooltip {
    display:none;
    background:transparent url(images/black_arrow.png);
    font-size:11px;
    height:70px;
    width:160px;
    padding:25px;
    color:black;
  }

  .carousel_tooltip a:hover { background-color:white; }
  .carousel_tooltip a:link img, a:visited img { border:1px solid lightgray;padding:2px;margin:2px; }
  .carousel_tooltip a:hover img { border:1px solid orange;padding:2px;margin:2px; }
  
</style>

<table width="100%" height="100" cellpadding="0" cellspacing="0">
<tr>		
		
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
			ORDER BY RAND()
			LIMIT $latest_featured_ads_count";
	$res_latest = mysql_query($sql) or die($sql.mysql_error());

	$css_first = "_first";
	while($row = mysql_fetch_array($res_latest))
	{
		
		$url = buildURL("showad", array($xcityid, $row['catid'], $row['catname'], $row['subcatid'], $row['subcatname'], $row['adid'], $row['adtitle']));
		

?>





	
<?php 
		

		if($row['picfile']) 
		{
			$picfile = $row['picfile'];
			$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$picfile}", $tinythumb_max_width, $tinythumb_max_height);
		}
		else 
		{
		
 $picfile = "papka.png";

$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$row[picfile]}", $tinythumb_max_width, $tinythumb_max_height);
                                
	
		}
		?>


	


<?php if($picfile) { ?>

<td class="carousel_tooltip" align="center" valign="top">
<a href="<?php echo $url; ?>"><img src="<?php echo "{$datadir[adpics]}/{$picfile}"; ?>" width="75" height="60" title="<?php echo $row['adtitle']; ?><br><br><i><small><font color='green'><?php echo "$row[subcatname]"; ?></font></small></i>"></a>
</td>


<?php } ?>


<?php
		$css_first = "";
	}
?>

</tr>
</table>


<script>
  $(function() {
      $(".carousel_tooltip img[title]").tooltip();
    });
</script>











