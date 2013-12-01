<?php


require_once("initvars.inc.php");
require_once("config.inc.php");
require_once("pager.cls.php");

// BEGIN Vivaru Adult Warning
$alert_session = '';

if ( $alert_use_cookies )
{
	$alert_session = $_COOKIE["ck_adultverified"];
}
else
{
	session_start();
	$alert_session = $_SESSION["adultverified"];
}

if ( !$xsubcatid )
{
	$resa = mysql_query("SELECT alert FROM $t_cats WHERE catid='$xcatid' AND alert='1'") or die($sqla.mysql_error());
		
	if ( (@mysql_num_rows($resa) && !$alert_session) && !strstr($_SERVER['HTTP_USER_AGENT'], 'Googlebot') ) 
	{
		header("Location: $script_url/adult_warning.inc.php?catid=$xcatid&cityid=$xcityid");
		exit;
	}
}
else
{
	$resb = mysql_query("SELECT alert FROM $t_subcats WHERE subcatid='$xsubcatid' AND alert='1'") or die($sqlb.mysql_error());
	
	if ( (@mysql_num_rows($resb) && !$alert_session) && !strstr($_SERVER['HTTP_USER_AGENT'], 'Googlebot') ) 
	{
		header("Location: $script_url/adult_warning.inc.php?subcatid=$xsubcatid&cityid=$xcityid");
		exit;
	}
}
// END Vivaru Adult Warning


?>
<!--bookmark mod start-->
<script>window.onload=setCheckedSelectedBookmarksAds;</script>
<!--bookmark mod end-->
<?php


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
<table width="100%" class="eventnav" border="0"><tr>

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
if(!$show_sidebar_always)
{
?>






<table id="search_top" width="100%" cellpaddingg="0" cellspacing="0">

<tr>
<td align="left" valign="middle">

<h2><?php if ($rssurl && !$xsearchmode) { ?>
<a href="<?php echo $rssurl; ?>"><img src="images/rss.gif" border="0" align="absmiddle"></a>
<?php } ?> <?php echo $page_head; ?></h2>	




</td>

<td align="left" valign="middle">

	
	
	<?php include("search.inc.php"); ?>
	

</td>



</tr>

</table>

<?php
}
?>




<?php

if ($adcount || mysql_num_rows($featres)>0)
{

?>


<table border="0" cellspacing="0" cellpadding="0" width="100%" class="postlisting"> 

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
<font color="darkred">
			<?php 
			$loc = "";
			if($row['area']) $loc = $row['area'];
			if($xcityid <= 0) $loc .= ($loc ? ", " : "") . $row['cityname'];	
			if($loc) echo ":$loc";
			?>
</font>
			

						
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


				<?php 
				if($row['picfile'] && $ad_thumbnails) 
				{ 
					$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$row[picfile]}", $tinythumb_max_width, $tinythumb_max_height);
				?>
					<img src="<?php echo "$datadir[adpics]/$row[picfile]"; ?>" border="0" width="<?php echo $imgsize[0]; ?>" height="<?php echo $imgsize[1]; ?>" align="left" style="border:1px solid black;margin-right:5px;"> 
				<?php 
				}
				?>


				<?php echo $row['adtitle']; ?></a>
<font color="darkred">
				<?php 
				$loc = "";
				if($row['area']) $loc = $row['area'];
				if($xcityid <= 0) $loc .= ($loc ? ", " : "") . $row['cityname'];	
				if($loc) echo "($loc)";
				?>
</font>

				<?php if($row['picfile']) echo "<img src=\"images/adwithpic.gif\" align=\"absmiddle\" title=\"This ad has picture(s)\"> "; ?>

				
							
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


<?php

// Featured ads
if (mysql_num_rows($featres)>0)
{

?>

<tr><td colspan="4" width="100%">

<table class="adslisttable_featured" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid lightblue;border-radius: 3px;-moz-border-radius: 3px;">

<tr>
<td align="center" style="background:lightgray;border-top:1px solid lightblue;border-bottom:1px solid lightblue;border-left:1px solid lightblue;"><?php echo $lang['ADLIST_ADTITLE']; ?></td>
<?php
$colspan = 1;
foreach ($xsubcatfields as $fldnum=>$fld)
{
	if (!$fld['SHOWINLIST']) continue;

	echo "<td style='background:lightgray;border-top:1px solid lightblue;border-bottom:1px solid lightblue;border-left:1px solid lightblue;'&nbsp;";
	//if ($fld['TYPE']=="N") 
	echo " align=\"center\"";
	echo ">$fld[NAME]</td>";
	$colspan++;
}
if ($xsubcathasprice) 
{
	echo "<td align=\"center\" width=\"10%\" style='background:lightgray;border-top:1px solid lightblue;border-bottom:1px solid lightblue;border-left:1px solid lightblue;'&nbsp;>$xsubcatpricelabel</td>";
	$colspan++;
}
?>
<td style="background:lightgray;border-bottom:1px solid lightblue;border-top:1px solid lightblue;border-right:1px solid lightblue;border-left:1px solid lightblue;">&nbsp;</td>
</tr>

<?php	
	
	$css_first = "_first";

	while($row=mysql_fetch_array($featres))
	{
		
		
		$url = buildURL($target_view, array($xcityid, $row['catid'], $row['catname'], 
		    $row['subcatid'], $row['subcatname'], $row['adid'], $row['adtitle']));
		

?>




		<tr>
			<td width="100%" style="background:Azure;border-top:1px solid lightblue;border-bottom:1px solid lightblue;border-left:1px solid lightblue;">
			<a href="<?php echo $url; ?>">	


			<?php 
				if($row['picfile'] && $ad_thumbnails) 
				{ 
					$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$row[picfile]}", $tinythumb_max_width, $tinythumb_max_height);
				?>
					<img src="<?php echo "$datadir[adpics]/$row[picfile]"; ?>"  title="" width="120" height="80" align="left"  style="border:1px solid #FFDAB9;margin-right:5px;" onmouseover="style.borderColor='orange';"
onmouseout="style.borderColor='#FFDAB9'"> 
				<?php 
				}
                                 
                                else

                                {
                             
                                
                                ?>

                                <img src="images/no_image.png" title="" width="120" height="80" align="left"  style="border:1px solid #FFDAB9;margin-right:5px;" onmouseover="style.borderColor='orange';"
onmouseout="style.borderColor='#FFDAB9'"> 
                                
                                <?php 
				}
                                ?>

                             
			<span style="font-size:16px;"><?php echo $row['adtitle']; ?></font></a><div style="float:right;"><img src="<?php echo $script_url?>/images/featured.png" border="0"></div><br>
			<?php 
			$loc = "";
			
			if($xcityid <= 0) $loc .= ($loc ? ", " : "") . $row['cityname'];	
			if($loc) echo "<font color=\"darkred\">&nbsp;$loc</font>";
			?>
			<?php 
if($row['urgent'] == 1  && $row['urgent_paid'] == 1) echo "&nbsp;<img src=\"images/urgent_icon.png\" align=\"absmiddle\">";
?>
			
<br>

<?php 
				if($ad_preview_chars) 
				{ 
					echo "<span class='adpreview'>";
                    

                    echo generateBrief($row['addesc']);
                    
					echo "</span>";
				} 
				?>
							
			


			</td>


			<?php

			foreach ($xsubcatfields as $fldnum=>$fld)
			{
				if (!$fld['SHOWINLIST']) continue;

				echo "<td style='background:Azure;border-top:1px solid lightblue;border-bottom:1px solid lightblue;border-left:1px solid lightblue;' ";
				//if ($fld['TYPE']=="N")
				echo " align='center'";
				echo ">".
					((($fld['TYPE']=="N" && ($row["f$fldnum"]==-1 || $row["f$fldnum"]=="0" || $row["f$fldnum"]=="")) || ($fld['TYPE']!="N" && trim($row["f$fldnum"])==""))?"-":$row["f$fldnum"])."</td>";
			}

			if($xsubcathasprice) 
				echo "<td style=\"background-color:Azure;border-bottom:1px solid lightblue;border-top:1px solid lightblue;border-left:1px solid lightblue;\" align=\"center\">&nbsp;".($row['price'] > 0.00?"$currency".$row['price']:"-")."</td>";
			
			?>

<!--bookmark mod start-->
				<td width="90" align="right" style="background:Azure;border-bottom:1px solid lightblue;border-top:1px solid lightblue;border-right:1px solid lightblue;border-left:1px solid lightblue;">
					<table >
					<tr>
						
						<td style="border-bottom: 0px solid #FFFFFF;border-top: 0px solid #FFFFFF;">
						<input alt="Bookmark" title="Bookmark" id = "bookmarkad<?php echo $row['adid'];?>" name="bookmarkad" type="submit" class="savead" value="" onmouseout="javascript:setHout(<?php echo $row['adid']?>);" onclick="javascript:writeCookie('bookmark',<?php echo $row['adid']?>, '.'); return false;" onmouseover="javascript:setHover(<?php echo $row['adid']?>);" >
						</td>
					</tr>
				</table>
				</td>
			<!--bookmark mod end-->


		</tr>





<?php

		$css_first = "";
	
	}

?>

</table>

</td></tr>

<?php

}

?>

<tr><td colspan="4" width="100%">
		
		<table class="adslisttable" cellpadding="0" cellspacing="0">

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
				//echo "<tr><td height=\"1\"></td></tr>";
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

		
		


		
		
		<tr>

			<td width="100%" style="border-top:1px solid lightblue;border-bottom:1px solid lightblue;border-left:1px solid lightblue;">
				
				<a href="<?php echo $url; ?>">	

				<?php 
				if($row['picfile'] && $ad_thumbnails) 
				{ 
					$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$row[picfile]}", $tinythumb_max_width, $tinythumb_max_height);
				?>
					<img src="<?php echo "$datadir[adpics]/$row[picfile]"; ?>"  width="120" height="80" align="left"  style="border:1px solid #FFDAB9;margin-right:5px;" onmouseover="style.borderColor='orange';"
 onmouseout="style.borderColor='#FFDAB9'"> 
				<?php 
				}
                                 
                                 else

                                {
                             
                                
                                ?>

                                <img src="images/no_image.png"  width="120" height="80" align="left"  style="border:1px solid #FFDAB9;margin-right:5px;" onmouseover="style.borderColor='orange';"
onmouseout="style.borderColor='#FFDAB9'"> 
                                
                                <?php 
				}
                                ?>




				<span style="font-size:16px;"><?php echo $row['adtitle']; ?></span></a><BR>
				<?php 
				$loc = "";
				
				if($xcityid <= 0) $loc .= ($loc ? ", " : "") . $row['cityname'];	
				if($loc) echo "<font color=\"darkred\">&nbsp;$loc</font>";
				?>
<?php 
if($row['urgent'] == 1  && $row['urgent_paid'] == 1) echo "&nbsp;<img src=\"images/urgent_icon.png\" align=\"absmiddle\">";
?>
<br>
<?php 
				
			if($ad_preview_chars) 
			{ 
				echo "<span class='adpreview'>";
				

				echo generateBrief($row['addesc']);

				
				echo " ...</span>";
			} 
			?>
				


			</td>

			<?php

			foreach ($xsubcatfields as $fldnum=>$fld)
			{
				if (!$fld['SHOWINLIST']) continue;

				echo "<td style='border-top:1px solid lightblue;border-bottom:1px solid lightblue;border-left:1px solid lightblue;'";
				//if ($fld['TYPE']=="N")
				echo " align='center'";
				echo ">".
					((($fld['TYPE']=="N" && ($row["f$fldnum"]==-1 || $row["f$fldnum"]=="0" || $row["f$fldnum"]=="")) || ($fld['TYPE']!="N" && trim($row["f$fldnum"])==""))?"-":$row["f$fldnum"])."</td>";
			}

			if($xsubcathasprice) 
				echo "<td style=\"border-bottom: 1px solid lightblue;border-top: 1px solid lightblue;border-left: 1px solid lightblue;\" align=\"center\">&nbsp;".($row['price'] > 0.00?"$currency".$row['price']:"-")."</td>";
			
			?>

<!--bookmark mod start-->
				<td width="90" align="right" style="border-top:1px solid lightblue;border-bottom:1px solid lightblue;border-left:1px solid lightblue;border-right:1px solid lightblue;">
					<table>
					<tr>
						
						<td style="border-top: 0px solid #FFFFFF;">
						<input alt="Bookmark" title="Bookmark" id = "bookmarkad<?php echo $row['adid'];?>" name="bookmarkad" type="submit" class="savead" value="" onmouseout="javascript:setHout(<?php echo $row['adid']?>);" onclick="javascript:writeCookie('bookmark',<?php echo $row['adid']?>, '.'); return false;" onmouseover="javascript:setHover(<?php echo $row['adid']?>);" >
						</td>
					</tr>
				</table>
				</td>
			<!--bookmark mod end-->
			
		

		
		
		

	<?php

	}
	
	
	?>
	
	<?php
	
}
?>

</tr>
</table>

</td></tr>
</table>	
	

<hr>


<?php

if ($adcount > $ads_per_page)
{

?>

<br>
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


</div>

<?php

}

?>
