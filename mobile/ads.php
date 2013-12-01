<?php



require_once("initvars.inc.php");
require_once("config.inc.php");
require_once("pager.cls.php");


// Pager
$page = $_GET['page'] ? $_GET['page'] : 1;
$offset = ($page-1) * $ads_per_page;

if ($sef_urls && !$xsearchmode)
{
	if ($xview == "events")
	{
       
        $urlformat = buildURL('events', array($xcityid, $xdate, "{@PAGE}"));
      
	}
	else
	{
	   
	    $urlformat = buildURL('ads', array($xcityid, $xcatid, $xcatname, $xsubcatid, $xsubcatname, "{@PAGE}"));
	   
	}
}
else
{
	
	$excludes = array('page','msg');
	$urlformat = regenerateURL($excludes) . "page={@PAGE}";
	
}


if ($xview == "events")
{
	$where = "";

	if ($xsearch)
	{
		$searchsql = mysql_escape_string($xsearch);
        
       
        if ($use_regex_search) {
            $where = "(a.adtitle RLIKE '[[:<:]]{$searchsql}[[:>:]]' OR a.addesc RLIKE '[[:<:]]{$searchsql}[[:>:]]')";
        } else {
            $where = "(a.adtitle LIKE '$searchsql' OR a.addesc LIKE '$searchsql')";
        }
        
        $where .= " AND a.endon >= NOW()";
        
	}
	else if ($xdate)
	{
		$where = "(starton <= '$xdate' AND endon >= '$xdate')";
	}
	else
	{
		$where = "endon >= NOW()";		
	}

	if($_GET['area']) $where .= "AND a.area = '$_GET[area]'";

	
	if ($xsearchmode)
	{
		$sort = "a.starton ASC";
	}
	else
	{
		$sort = "a.starton DESC";
	}


	// Get count
	$sql = "SELECT COUNT(*) AS adcount
			FROM $t_events a
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				LEFT OUTER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'E'
			WHERE $where
				AND $visibility_condn
				AND (feat.adid IS NULL OR feat.featuredtill < NOW())
				$loc_condn";
	$tmp = mysql_query($sql) or die($sql.mysql_error());
	list($adcount) = mysql_fetch_array($tmp);

	// Get results
	$sql = "SELECT a.*, COUNT(*) AS piccount, p.picfile,
				UNIX_TIMESTAMP(a.createdon) AS timestamp, ct.cityname,
				UNIX_TIMESTAMP(a.starton) AS starton, UNIX_TIMESTAMP(a.endon) AS endon			
			FROM $t_events a
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '1'
				LEFT OUTER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'E'
			WHERE $where
				AND $visibility_condn
				AND (feat.adid IS NULL OR feat.featuredtill < NOW())
				$loc_condn
			GROUP BY a.adid
			ORDER BY $sort
			LIMIT $offset, $ads_per_page";
	$res = mysql_query($sql) or die($sql.mysql_error());

	// Get featured events
	$sql = "SELECT a.*, COUNT(*) AS piccount, p.picfile,
				UNIX_TIMESTAMP(a.createdon) AS timestamp, ct.cityname,
				UNIX_TIMESTAMP(a.starton) AS starton, UNIX_TIMESTAMP(a.endon) AS endon
			FROM $t_events a
				INNER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'E' AND feat.featuredtill >= NOW()
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '1'
			WHERE $where
				AND $visibility_condn
				$loc_condn
			GROUP BY a.adid
			ORDER BY $sort";
	$featres = mysql_query($sql) or die(mysql_error().$sql);
	
	// Vars
	$adtable = $t_events;
	$adtype = "E";
	$target_view = "showevent";
	$target_view_sef = "events";
	//$page_title = "Events";
	if ($_GET['date']) $link_extra = "&amp;date=$xdate";
	else $find_date = TRUE;

}
else
{
	// Make up the sql query
	$whereA = array();

	if ($xsearch)
	{
			   
	    $search_terms = separeteSearchTerms($xsearch);
	    $or_conditions = array();
	    
	    foreach($search_terms as $term)
	    {
    		$searchsql = mysql_escape_string($term);
            
    		if ($use_regex_search) {
                $or_conditions[] .= "a.adtitle RLIKE '[[:<:]]{$searchsql}[[:>:]]' OR a.addesc RLIKE '[[:<:]]{$searchsql}[[:>:]]'";
            } else {
                $or_conditions[] = "a.adtitle LIKE '%$searchsql%' OR a.addesc LIKE '%$searchsql%'";
            }
           
        }
        
        $combined_clause = "(" . implode(" OR ", $or_conditions) . ")";
        $whereA[] = $combined_clause;
      
	}

	if($_GET['area']) $whereA[] = "a.area = '$_GET[area]'";

	if ($xsubcathasprice && $_GET['pricemin'])
	{
		$whereA[] = "a.price >= $_GET[pricemin]";
	}

	if ($xsubcathasprice && $_GET['pricemax'])
	{
		$whereA[] = "a.price <= $_GET[pricemax]";
	}

	if ($xsubcatid)		$whereA[] = "a.subcatid = $xsubcatid";
	else if ($xcatid)	$whereA[] = "scat.catid = $xcatid";

	if (count($_GET['x']))
	{
		foreach ($_GET['x'] as $fldnum=>$val)
		{
			// Ensure numbers
			$fldnum += 0;
			
			if ($val === "" || !$fldnum) continue;
			

			if($xsubcatfields[$fldnum]['TYPE'] == "N" && is_array($val))
			{
				numerize($val['min']); numerize($val['max']);	// Sanitize
				if($val['min']) $whereA[] = "axf.f{$fldnum} >= $val[min]";
				if($val['max']) $whereA[] = "axf.f{$fldnum} <= $val[max]";
			}
			elseif($xsubcatfields[$fldnum]['TYPE'] == "D") 
			{
				$whereA[] = "axf.f{$fldnum} = '$val'";
			}
			else
			{
				$whereA[] = "axf.f{$fldnum} LIKE '%$val%'";
			}
		}
	}

	$where = implode(" AND ", $whereA);
	if (!$where) $where = "1";

	// Get count
	$sql = "SELECT COUNT(*) AS adcount
			FROM $t_ads a
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				INNER JOIN $t_subcats scat ON a.subcatid = scat.subcatid
				INNER JOIN $t_cats cat ON scat.catid = cat.catid
				LEFT OUTER JOIN $t_adxfields axf ON a.adid = axf.adid
				LEFT OUTER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'A'
			WHERE $where
				AND $visibility_condn
				AND (feat.adid IS NULL OR feat.featuredtill < NOW())
				$loc_condn";
			
	$tmp = mysql_query($sql) or die(mysql_error());
	list($adcount) = mysql_fetch_array($tmp);

	// List of extra fields
	$xfieldsql = "";
	if(count($xsubcatfields)) 
	{
		for($i=1; $i<=$xfields_count; $i++)	$xfieldsql .= ", axf.f$i";
	}

	// Get results
	$sql = "SELECT a.*, UNIX_TIMESTAMP(a.createdon) AS timestamp, ct.cityname,
				COUNT(*) AS piccount, p.picfile,
				scat.subcatname, cat.catid, cat.catname $xfieldsql
			FROM $t_ads a
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				INNER JOIN $t_subcats scat ON a.subcatid = scat.subcatid
				INNER JOIN $t_cats cat ON scat.catid = cat.catid
				LEFT OUTER JOIN $t_adxfields axf ON a.adid = axf.adid
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '0'
				LEFT OUTER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'A'
			WHERE $where
				AND $visibility_condn
				AND (feat.adid IS NULL OR feat.featuredtill < NOW())
				$loc_condn
			GROUP BY a.adid
			ORDER BY a.createdon DESC
			LIMIT $offset, $ads_per_page";
	$res = mysql_query($sql) or die($sql.mysql_error());

	// Get featured ads
	$sql = "SELECT a.*, UNIX_TIMESTAMP(a.createdon) AS timestamp, ct.cityname,
				COUNT(*) AS piccount, p.picfile,
				scat.subcatname, cat.catid, cat.catname $xfieldsql
			FROM $t_ads a
				INNER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'A' AND feat.featuredtill >= NOW()
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				INNER JOIN $t_subcats scat ON a.subcatid = scat.subcatid
				INNER JOIN $t_cats cat ON scat.catid = cat.catid
				LEFT OUTER JOIN $t_adxfields axf ON a.adid = axf.adid
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '0'
			WHERE $where
				AND $visibility_condn
				$loc_condn
			GROUP BY a.adid
			ORDER BY feat.timestamp DESC";
	$featres = mysql_query($sql) or die(mysql_error().$sql);
	$featadcount = mysql_num_rows($featres);

	// Vars
	$adtable = $t_ads;
	$adtype = "A";
	$target_view = "showad"; 
	$target_view_sef = "posts"; 
	//$page_title = ($xsubcatname ? $xsubcatname : $xcatname);

}

$pager = new pager($urlformat, $adcount, $ads_per_page, $page);


if ($xsubcatname) $page_head = langcheck($xsubcatname);
elseif ($xcatname) $page_head = langcheck($xcatname);
if ($xsearchmode) $page_head = $lang['SEARCH'] . ($page_head ? ": $page_head" : "");

	
?>


<?php

if ($xview == "events" && !$xsearchmode)
{
	// Calendar navigation
	$prevmonth = mktime(0, 0, 0, $xdate_m-1, $xdate_d, $xdate_y);
	$nextmonth = mktime(0, 0, 0, $xdate_m+1, $xdate_d, $xdate_y);
	$prevday = $xdatestamp - 24*60*60;
	$nextday = $xdatestamp + 24*60*60;
?>
<table width="100%" border="0"><tr>

<td valign="bottom">
<?php 

$prevday_url = buildURL("events", array($xcityid, date("Y-m-d", $prevday)));
$nextday_url = buildURL("events", array($xcityid, date("Y-m-d", $nextday)));

?>
<a href="<?php echo $prevday_url; ?>">
<?php echo $lang['EVENTS_PREVDAY']; ?></a>
</td>

<td align="center">
<b><?php echo QuickDate($xdatestamp, FALSE, FALSE); ?> </b>
</td>

<td align="right" valign="bottom">
<a href="<?php echo $nextday_url; ?>">
<?php echo $lang['EVENTS_NEXTDAY']; ?></a>
</td>

</tr></table>
<?php
}
?>




<?php

if ($adcount || mysql_num_rows($featres)>0)
{

?>




<?php

if($xview == "events")
{

?>
<tr class="head">
<td><?php echo $lang['EVENTLIST_EVENTTITLE']; ?></td>
<td align="center" width="15%"><?php echo $lang['EVENTLIST_STARTSON']; ?></td>
<td align="center" width="15%"><?php echo $lang['EVENTLIST_ENDSON']; ?></td>
</tr>

<?php

// Featured events
if (mysql_num_rows($featres)>0)
{

	$css_first = "_first";

	while($row=mysql_fetch_array($featres))
	{
		if ($find_date) 
		{
			$link_extra = "&date=".date("Y-m-d", $row['starton']);
			$urldate = date("Y-m-d", $row['starton']);
		}

       
		$url = buildURL($target_view, array($xcityid, $urldate, $row['adid'], $row['adtitle']));
		

?>

		<tr class="featuredad<?php echo $css_first; ?>">
			<td>
			<a href="<?php echo $url; ?>" class="posttitle">	
			<?php 
			if($row['picfile'] && $ad_thumbnails) 
			{ 
				$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$row[picfile]}", $tinythumb_max_width, $tinythumb_max_height);
			?>
				<img src="<?php echo "$datadir[adpics]/$row[picfile]"; ?>" border="0" width="<?php echo $imgsize[0]; ?>" height="<?php echo $imgsize[1]; ?>" align="left" style="border:1px solid black;margin-right:5px;"> 
			<?php 
			}
			?>
			<img src="images/featured.gif" align="absmiddle" border="0">
			
			<?php echo $row['adtitle']; ?></a>

			<?php 
			$loc = "";
			if($row['area']) $loc = $row['area'];
			if($xcityid <= 0) $loc .= ($loc ? ", " : "") . $row['cityname'];	
			if($loc) echo "($loc)";
			?>			

						
			<?php 
			if($ad_preview_chars) 
			{ 
				echo "<span class='adpreview'>";
				

				echo generateBrief($row['addesc']);

				
				echo "</span>";
			} 
			?>


			</td>
			
			<td align="center"><?php echo $langx['months_short'][date("n", $row['starton'])-1] . " " . date("j", $row['starton']) . ", " . date("y", $row['starton']); ?></td>	
			<td align="center"><?php if($row['starton'] != $row['endon']) echo $langx['months_short'][date("n", $row['endon'])-1] . " " . date("j", $row['endon']) . ", " . date("y", $row['endon']);	?>
			

			
			</td>

		</tr>

<?php

		$css_first = "";
	}

}

?>


<?php

	$i = 0;
	while($row=mysql_fetch_array($res))
	{
			    
		$css_class = "post" . (($i%2)+1);
		
		$i++;

	?>

		<tr class="<?php echo $css_class; ?>">

			<td>
				
				<?php

				if ($find_date) 
				{
					$link_extra = "&date=".date("Y-m-d", $row['starton']);
					$urldate = date("Y-m-d", $row['starton']);
				}

               
                $url = buildURL($target_view, array($xcityid, $urldate, $row['adid'], $row['adtitle']));
             

				?>

				<a href="<?php echo $url; ?>" class="posttitle">	


				


				<?php echo $row['adtitle']; ?></a>

				<?php 
				$loc = "";
				if($row['area']) $loc = $row['area'];
				if($xcityid <= 0) $loc .= ($loc ? ", " : "") . $row['cityname'];
				if($loc) echo "($loc)";
				?>				

				
							
    			<?php 
    			if($ad_preview_chars) 
    			{ 
    				echo "<span class='adpreview'>";
    				

    				echo generateBrief($row['addesc']);

    				
    				echo "</span>";
    			} 
    			?>


			</td>
			
			<td align="center"><?php echo $langx['months_short'][date("n", $row['starton'])-1] . " " . date("j", $row['starton']) . ", " . date("y", $row['starton']); ?></td>
			<td align="center"><?php if($row['starton'] != $row['endon']) echo $langx['months_short'][date("n", $row['endon'])-1] . " " . date("j", $row['endon']) . ", " . date("y", $row['endon']);	?>
			</td>

		</tr>

	<?php

	}
}
else
{

?>



<ul data-role="listview" style="margin-top:0px;">

<?php

// Featured ads
if (mysql_num_rows($featres)>0)
{
	
	echo "";
	$css_first = "_first";



	while($row=mysql_fetch_array($featres))
	{
		
		
		$url = buildURL($target_view, array($xcityid, $row['catid'], $row['catname'], 
		    $row['subcatid'], $row['subcatname'], $row['adid'], $row['adtitle']));
		

?>

<li data-theme="e" style="height:82px;">

<a href="<?php echo "$script_url/$url"; ?>">

<?php 
				if($row['picfile'] && $ad_thumbnails) 
				{ 
					$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$row[picfile]}", $tinythumb_max_width, $tinythumb_max_height);
				?>

					<img src="<?php echo "$datadir[adpics]/$row[picfile]"; ?>"  title="" width="80" height="80"> 
				
<?php 
				}
                                 
                                 else

                                {
                             
                                
                                ?>

                                <img src="images/no_image.png" title="" width="80" height="80"> 
                      
                                <?php 
				}
                                ?>

<?php 
if($row['urgent'] == 1  && $row['urgent_paid'] == 1) { ?><span class="ui-li-count" style="background:darkorange;color:white;font-weight:normal;font-family:verdana;">URGENT</span><?php }
?>		
	
		
<h3>
			
<?php echo $row['adtitle']; ?>

</h3>

<?php 
				if($ad_preview_chars) 
				{ 
					echo "<p>";
                 

                    echo generateBrief($row['addesc']);
                    
					echo "</p>";
				} 
				?>

</a>

</li>
			
<?php

		$css_first = "";
	
	}

?>



<?php

}

?>

<?php

	$i = $j = 0;
	$lastdate = "";
	while($row=mysql_fetch_array($res))
	{
		$date_formatted = date("Ymd", $row['timestamp']);
		if($date_formatted != $lastdate)
		{
			if ($lastdate) 
			{
				//echo "";
				$j = 0;
			}

			

			$lastdate = $date_formatted;
		}

		    
		$css_class = "post" . (($j%2)+1);
		
		$i++; $j++;		
		$url = buildURL($target_view, array($xcityid, $row['catid'], $row['catname'], 
		    $row['subcatid'], $row['subcatname'], $row['adid'], $row['adtitle']));
		

      
        $title_extra = "";
        
      
		if(!$xsubcatid && !$postable_category)
		
		
		{
			
    		$subcatlink = buildURL("ads", array($xcityid, $row['catid'], $row['catname'], 
    		    $row['subcatid'], $row['subcatname']));
    		

			$title_extra = "&nbsp;- <a href=\"$subcatlink\" class=\"adcat\">".langcheck($row['catname'])." $path_sep ".langcheck($row['subcatname'])."</a>";
		}


	?>

		


<li style="height:82px;">

<a href="<?php echo "$script_url/$url"; ?>">

			


<?php 
				if($row['picfile'] && $ad_thumbnails) 
				{ 
					$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$row[picfile]}", $tinythumb_max_width, $tinythumb_max_height);
				?>

					<img src="<?php echo "$datadir[adpics]/$row[picfile]"; ?>"  title="" width="80" height="80" align="left"> 
				
<?php 
				}
                                 
                                 else

                                {
                             
                                
                                ?>

                                <img src="../images/no_image.png" title="" width="80" height="80"> 
                          
                                <?php 
				}
                                ?>

<?php 
if($row['urgent'] == 1  && $row['urgent_paid'] == 1) { ?><span class="ui-li-count" style="background:darkorange;color:white;font-weight:normal;font-family:verdana;">URGENT</span><?php }
?>		

<h3>			
				
<?php echo $row['adtitle']; ?>

</h3>

<?php 
				if($ad_preview_chars) 
				{ 
					echo "<p>";
                 

                    echo generateBrief($row['addesc']);
                    
					echo "</p>";
				} 
				?>

</a>	




</li>


		

	<?php

	}
}
?>

</ul>



<?php

if ($adcount > $ads_per_page)
{

?>


<div align="right">
<table>
<tr><td><b><?php echo $lang['PAGE']; ?>: </b></td><td><?php echo $pager->outputlinks(); ?></td></tr>
</table>
</div>

<?php

}

?>


<?php

}
else
{

?>

<div class="noresults"><?php echo $lang['NO_RESULTS']; ?><br>
<a href="?view=main&cityid=<?php echo $xcityid; ?>"><?php echo $lang['BACK_TO_HOME']; ?></a>
<hr>
<center>

<script type="text/javascript"><!--
  // XHTML should not attempt to parse these strings, declare them CDATA.
  /* <![CDATA[ */
  window.googleAfmcRequest = {
    client: 'ca-mb-pub-8593185966940873',
    format: '300x250_as',
    output: 'html',
    slotname: '2881786943',
  };
  /* ]]> */
//--></script>
<script type="text/javascript"    src="http://pagead2.googlesyndication.com/pagead/show_afmc_ads.js"></script>


</center>
</div>

<?php

}

?>
