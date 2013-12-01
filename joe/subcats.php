<?php


require_once("initvars.inc.php");
require_once("config.inc.php");

?>

<h2><img src="images/category.gif" align="absmiddle"> <?php echo $xcatname; ?></h2>

<table border="0" cellspacing="1" cellpadding="2" width="100%" class="dir_cat">

<?php

// Directory

if($dir_sort) 
{
	$sortsubcatsql = "ORDER BY subcatname";
}
else
{
	$sortsubcatsql = "ORDER BY pos";
}


// First get ads per subcat
$subcatadcounts = array();
$sql = "SELECT scat.subcatid, COUNT(*) as adcnt
		FROM $t_ads a
			INNER JOIN $t_subcats scat ON scat.subcatid = a.subcatid AND ($visibility_condn)
			INNER JOIN $t_cities ct ON a.cityid = ct.cityid
		WHERE scat.enabled = '1'
			#$loc_condn
		GROUP BY a.subcatid";

$res = mysql_query($sql) or die(mysql_error().$sql);

while($row=mysql_fetch_array($res))
{
	$subcatadcounts[$row['subcatid']] = $row['adcnt'];
}


// Subcategories

$sql = "SELECT scat.subcatid, scat.subcatname AS subcatname
	FROM $t_subcats scat
	WHERE scat.catid = $xcatid
		AND scat.enabled = '1'
	$sortsubcatsql";


$res= mysql_query($sql) or die(mysql_error());
$i = 0;

while($row=mysql_fetch_array($res))
{
	$i++;

	if ($i%$dir_cols == 1 || $dir_cols == 1) echo "<tr>";

	$adcount = 0+$subcatadcounts[$row['subcatid']];

    
    $subcat_url = buildURL("ads", array($xcityid, $xcatid, $xcatname, $row['subcatid'], $row['subcatname']));
   
    
?>

		<td width="<?php echo $cell_width; ?>%">
		<a href="<?php echo $subcat_url; ?>">
		<?php echo $row['subcatname']; ?></a>
		<span class="count">(<?php echo $adcount; ?>)</span><br>
		</td>

<?php

	if ($i%$dir_cols == 0) echo "</tr>";

}

?>

</table>