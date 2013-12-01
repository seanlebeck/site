<?php



require_once("initvars.inc.php");
require_once("config.inc.php");

header("Content-Type: text/xml; charset={$langx['charset']}");		




if ($xview == "ads" && !($xcatid || $xsubcatid)) {
    $rsschannelurl = buildURL("main", array($xcityid, $xcityname));
    $rssurl = buildURL("rss_ads", array($xcityid));
} else if ($xview == "ads") {
    $rsschannelurl = buildURL("ads", array($xcityid, $xcatid, $xcatname, $xsubcatid, $xsubcatname));
    $rssurl = buildURL("rss_ads", array($xcityid, $xcatid, $xsubcatid));
} else if ($xview == "events") {
    $rsschannelurl = buildURL("events", array($xcityid, $xdate));
    $rssurl = buildURL("rss_events", array($xcityid, $xdate));
}

$rsschannelurl = str_replace("&", "&amp;", "{$script_url}/{$rsschannelurl}");
$rssurl = str_replace("&", "&amp;", "{$script_url}/{$rssurl}");





if ($xview == "events")
{
	$where = "";

	if ($xsearch)
	{
		$searchsql = mysql_escape_string($xsearch);
		$where = "(a.adtitle LIKE '%$searchsql%' OR a.addesc LIKE '%$searchsql%') AND a.endon >= NOW()";
	}
	else if ($xdate)
	{
		$where = "(starton <= '$xdate' AND endon >= '$xdate')";
	}
	else
	{
		$where = "starton >= NOW()";
	}
	
	if($_GET['area']) $where .= "AND a.area = '$_GET[area]'";

	// Get results
	$sql = "SELECT a.*, UNIX_TIMESTAMP(a.createdon) AS timestamp,
				UNIX_TIMESTAMP(a.starton) AS starton, UNIX_TIMESTAMP(a.endon) AS endon,
				COUNT(*) AS piccount, p.adid AS haspics
			FROM $t_events a
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '1'
			WHERE $where
				AND $visibility_condn
				$loc_condn
			GROUP BY a.adid
			ORDER BY a.createdon DESC
			LIMIT $rss_itemcount";
	$res = mysql_query($sql) or die($sql.mysql_error());

	// Vars
	$target_view = "showevent";
	$target_view_sef = "events";
	if ($xdate) $link_extra = "&amp;date=$xdate";
	else $find_date = TRUE;

}
else
{
	// Make up the sql query
	$whereA = array();

	if ($xsearch)
	{
		$searchsql = mysql_escape_string($xsearch);
		$whereA[] = "(a.adtitle LIKE '%$searchsql%' OR a.addesc LIKE '%$searchsql%')";
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

	if (is_array($_GET['x']) && count($_GET['x']))
	{
		foreach ($_GET['x'] as $fldnum=>$val)
		{
			// Ensure numbers
			$fldnum += 0;
			if (!$val || !$fldnum) continue;
			
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

	// Get results
	$sql = "SELECT a.*, UNIX_TIMESTAMP(a.createdon) AS timestamp,
				COUNT(*) AS piccount, p.adid AS haspics, scat.subcatname AS subcatname, scat.catid as catid, cat.catname as catname
			FROM $t_ads a
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				INNER JOIN $t_subcats scat ON a.subcatid = scat.subcatid
				INNER JOIN $t_cats cat ON scat.catid = cat.catid
				LEFT OUTER JOIN $t_adxfields axf ON a.adid = axf.adid
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '0'
			WHERE $where
				AND $visibility_condn
				$loc_condn
			GROUP BY a.adid
			ORDER BY a.createdon DESC
			LIMIT $rss_itemcount";
	$res = mysql_query($sql) or die($sql.mysql_error());

	// Vars
	$target_view = "showad"; 
	$target_view_sef = "posts";

}

if (mysql_num_rows($res)) {
	$firstRow = mysql_fetch_array($res);
	$lastBuildDate = $firstRow['timestamp'];
	mysql_data_seek($res, 0);
} else {
	$lastBuildDate = mktime(0, 0, 0, 1, 1, 2009);	
}


$rss_title = rssTitle(($xsubcatname?$xsubcatname:$xcatname), ($xcityname?$xcityname:""));	
$rss_desc  = rssTitle(($xsubcatname?$xsubcatname:$xcatname), ($xcityname?$xcityname:""), $lang['RSS_CHANNEL_DESC']);


echo '<'.'?xml version="1.0" encoding="'.$langx['charset'].'"?'.'>';	
?>


<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">

	<channel>
		<title><![CDATA[<?php echo $rss_title; ?>]]></title>
		<description><![CDATA[<?php echo $rss_desc; ?>]]></description>
		<link><?php echo "{$rsschannelurl}"; ?></link>
        <atom:link href="<?php echo $rssurl; ?>" rel="self" type="application/rss+xml" />
		<lastBuildDate><?php echo date("r", $lastBuildDate); ?></lastBuildDate>

<?php

if (@mysql_num_rows($res))
{
	if($xview == "events")
	{
		$i = 0;
		while($row=mysql_fetch_array($res))
		{
			$i++;

         
			if ($find_date) $urldate = date("Y-m-d", $row['starton']);
			$url = htmlentities(buildURL($target_view, array($xcityid, $urldate, 
			        $row['adid'], $row['adtitle'])));
           

?>
		<item>
			<title><![CDATA[<?php
				
				echo date("M j, y", $row['starton']);
				if($row['starton'] != $row['endon']) echo " - ".date("M j, y", $row['endon']);
				
				echo ": " . ($row['adtitle']);
				if($row['area']) echo (" ($row[area])"); ?>]]></title>
			<link><?php echo "$script_url/$url"; ?></link>
            
			<guid><?php echo "$script_url/$url"; ?></guid>
			


			<description><![CDATA[<?php 
			$row['addesc'] = strip_tags($row['addesc']);
			$desc = substr(strip_tags($row['addesc']), 0, $rss_itemdesc_chars); 
			if(strlen($row['addesc'])>$rss_itemdesc_chars) 
			{
				if(strpos($desc, "&") !== FALSE && (strpos($desc, ";") === FALSE || strrpos($desc, ";") < strrpos($desc, "&")))
					$desc = substr($row['addesc'], 0, strpos($row['addesc'], ";", $rss_itemdesc_chars)+1);
				$desc .= "...";
			}
			echo $desc;
			?>]]></description>
			<pubDate><?php echo date("r", $row['timestamp']); ?></pubDate>
		</item>

<?php
		}
	}
	else
	{
		$i = 0;
		while($row=mysql_fetch_array($res))
		{
			$i++;

           
            $url = htmlentities(buildURL($target_view, array($xcityid, $row['catid'], $row['catname'], $row['subcatid'], $row['subcatname'], $row['adid'], $row['adtitle'])));
           
            
?>
		<item>
			<title><![CDATA[<?php 
				echo ($row['adtitle']);
				if($row['area']) echo (" ($row[area])");
				if($xsubcathasprice && $row['price']) echo " - ".$currency.$row['price'] ?>]]></title>
			<link><?php echo "$script_url/$url"; ?></link>
            
			<guid><?php echo "$script_url/$url"; ?></guid>
			
			<description><![CDATA[<?php 
			$row['addesc'] = strip_tags($row['addesc']);
			$desc = substr($row['addesc'], 0, $rss_itemdesc_chars); 
			if(strlen($row['addesc'])>$rss_itemdesc_chars) 
			{
				if(strpos($desc, "&") !== FALSE && (strpos($desc, ";") === FALSE || strrpos($desc, ";") < strrpos($desc, "&")))
					$desc = substr($row['addesc'], 0, strpos($row['addesc'], ";", $rss_itemdesc_chars)+1);
				$desc .= "...";
			}
			echo $desc;
			?>]]></description>
			<pubDate><?php echo date("r", $row['timestamp']); ?></pubDate>
		</item>
<?php
		}
	}
}
?>
	</channel>
</rss>