<div class="catlist">
<?php

// List of categories

if($dir_sort) 
{
	$sortcatsql = "ORDER BY catname";
	$sortsubcatsql = "ORDER BY subcatname";
}
else
{
	$sortcatsql = "ORDER BY pos";
	$sortsubcatsql = "ORDER BY scat.pos";
}


// First get ads per cat and subcat
$subcatadcounts = array();
$catadcounts = array();
$sql = "SELECT scat.subcatid, scat.catid, COUNT(*) as adcnt
		FROM $t_ads a
			INNER JOIN $t_subcats scat ON scat.subcatid = a.subcatid AND ($visibility_condn)
			INNER JOIN $t_cats cat ON cat.catid = scat.catid
			INNER JOIN $t_cities ct ON a.cityid = ct.cityid
		WHERE scat.enabled = '1'
			$loc_condn
		GROUP BY a.subcatid";

$res = mysql_query($sql) or die(mysql_error().$sql);

while($row=mysql_fetch_array($res))
{
	$subcatadcounts[$row['subcatid']] = $row['adcnt'];
	$catadcounts[$row['catid']] += $row['adcnt'];
}



// Categories
$sql = "SELECT catid, catname AS catname FROM $t_cats WHERE enabled = '1' $sortcatsql";
$rescats = mysql_query($sql) or die(mysql_error());
$catcount = @mysql_num_rows($rescats);

$percol_short = floor($catcount/$dir_cols);
$percol_long = $percol_short+1;
$longcols = $catcount%$dir_cols;

$i = 0;
$j = 0;
$col = 0;
$thiscolcats = 0;

while($rowcat=mysql_fetch_array($rescats))
{
	if ($j >= $thiscolcats)
	{
		$col++;
		$thiscolcats = ($col > $longcols) ? $percol_short : $percol_long;
		$j = 0;
	}

	$i++;
	$j++;

   
    $catlink = buildURL("ads", array($xcityid, $rowcat['catid'], $rowcat['catname']));
    
    
	$adcount = 0+$catadcounts[$rowcat['catid']];

?>

<div class="cat">
<img src="images/bullet.gif" align="absmiddle"> <a href="<?php echo $catlink; ?>"><?php echo langcheck($rowcat['catname']); ?></a>
<?php if($show_cat_adcount) { ?><span class="count">(<?php echo $adcount; ?>)</span><?php } ?>
</div>

<?php

	if($xcatid == $rowcat['catid']) 
	{

		$sql = "SELECT scat.subcatid, scat.subcatname AS subcatname
		FROM $t_subcats scat
		WHERE scat.catid = $rowcat[catid]
			AND scat.enabled = '1'
		$sortsubcatsql";

		$ressubcats = mysql_query($sql) or die(mysql_error()."<br>$sql");
		
		
    	$subcatcount = mysql_num_rows($ressubcats);
    

		while ($rowsubcat = mysql_fetch_array($ressubcats))
		{
		    
    	    if ($shortcut_categories && $subcatcount == 1 
    	            && $rowsubcat['subcatname'] == $rowcat['catname']) {
    	        continue;
    	    }
    	    
	    
			$adcount = 0+$subcatadcounts[$rowsubcat['subcatid']];

			
			$subcat_url = buildURL("ads", array($xcityid, $rowcat['catid'], $rowcat['catname'], $rowsubcat['subcatid'], $rowsubcat['subcatname']));
			

?>

<div class="subcat">
&nbsp; &nbsp; <a href="<?php echo $subcat_url; ?>"><?php echo langcheck($rowsubcat['subcatname']); ?></a>
<?php if($show_subcat_adcount) { ?><span class="count">(<?php echo $adcount; ?>)</span><?php } ?>
</div>

<?php

		}
	}
}

?>
</div>
