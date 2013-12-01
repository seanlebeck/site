

<?php


require_once("initvars.inc.php");
require_once("config.inc.php");

?>
<?php
if($upcoming_events_count)
{
?>




<table width="100%" height="100%" cellspacing="5" cellpadding="0">
<tr>

<?php
	$sql = "SELECT a.*, UNIX_TIMESTAMP(a.starton) AS starton_ts, UNIX_TIMESTAMP(a.endon) AS endon_ts, feat.adid AS isfeat,
				COUNT(*) AS piccount, p.picfile AS picfile, ct.cityname
			FROM $t_events a
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '1'
				LEFT OUTER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'E' AND feat.featuredtill >= NOW()
			WHERE $visibility_condn
				
				AND a.starton >= NOW()
			GROUP BY a.adid
			ORDER BY a.starton ASC
			LIMIT $upcoming_events_count";
	$res_latest = mysql_query($sql) or die($sql.mysql_error());

	$css_first = "_first";
	while($row = mysql_fetch_array($res_latest))
	{
		
		$event_start_date = date("Y-m-d", $row['starton_ts']);

        
        $url = buildURL("showevent", array($xcityid, $event_start_date, $row['adid'], $row['adtitle']));
        

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

?>
		
<td width="33%" height="100%" valign="middle" style="margin:2px;padding:2px;">
		
<table width="100%" cellpadding="5" style="border:1px solid lightblue;background:azure;-moz-border-radius:8px;-webkit-border-radius:8px;border-radius:8px;">
<td valign="middle">		

<table class="eventdatebox" width="30" cellpadding="0" cellspacing="0">
			<tr>
			<th align="center">
			<b><?php echo date("d", $row['starton_ts']); ?></b>
			</th>
			</tr>
			<tr>
			<td class="bottomeventbox" align="center">
			
			<b><?php echo $langx['months_short'][date("n", $row['starton_ts'])-1]; ?></b>
			
			</td>
			
			</tr>
			</table>
			
			</td>
			
			<td>
			<a href="<?php echo $url; ?>" <?php echo $feat_class; ?> target="_top"><?php echo $row['adtitle']; ?></a><br>
			
			<?php 
			$loc = $row['cityname'];
		
			if($loc) echo "<font color=\"gray\">$loc</font>";
			?>	
			</td>
			</tr>
			</table>



			</td>

			<?php
		$css_first = "";
	}
?>

</tr>
</table>



<?php
}
?>

