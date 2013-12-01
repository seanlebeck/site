<?php
require_once("initvars.inc.php");
require_once("config.inc.php");
?>

<style>

/* root element for tabs  */
ul.tabs {
    list-style:none;
    margin:0 !important;
    padding:0;
    border-bottom:1px solid #999;
    height:30px;
}

/* single tab */
ul.tabs li {
    float:left;
    text-indent:0;
    padding:0;
    margin:0 !important;
    list-style-image:none !important;
}

/* link inside the tab. uses a background image */
ul.tabs a {
    background: url(images/blue.png) no-repeat -420px 0;
    font-size:12px;
	font-weight:bold;
    display:block;
    height: 30px;
    line-height:30px;
    width: 134px;
    text-align:center;
    text-decoration:none;
    color:#333;
    padding:0px;
    margin:0px;
    position:relative;
    top:1px;
}

ul.tabs a:active {
    outline:none;
}

/* when mouse enters the tab move the background image */
ul.tabs a:hover {
    background-position: -420px -31px;
    color:#fff;
}

/* active tab uses a class name "current". its highlight is also done by moving the background image. */
ul.tabs a.current, ul.tabs a.current:hover, ul.tabs li.current a {
    background-position: -420px -62px;
    cursor:default !important;
    color:#000 !important;
}

/* Different widths for tabs: use a class name: w1, w2, w3 or w2 */


/* width 1 */
ul.tabs a.s { background-position: -553px 0; width:81px; }
ul.tabs a.s:hover { background-position: -553px -31px; }
ul.tabs a.s.current  { background-position: -553px -62px; }

/* width 2 */
ul.tabs a.l { background-position: -248px -0px; width:174px; }
ul.tabs a.l:hover { background-position: -248px -31px; }
ul.tabs a.l.current { background-position: -248px -62px; }


/* width 3 */
ul.tabs a.xl { background-position: 0 -0px; width:248px; }
ul.tabs a.xl:hover { background-position: 0 -31px; }
ul.tabs a.xl.current { background-position: 0 -62px; }


/* initially all panes are hidden */
.panes .pane {
    display:none;
}

/* tab pane styling */
.panes div {
    display:none;
    padding:5px;
    border:1px solid #999;
    border-top:0;
    height:70px;
    font-size:14px;
    background-color:#fff;
	-webkit-box-shadow:0 10px 6px -6px #777;
	-moz-box-shadow:0 10px 6px -6px #777;
	box-shadow:0 10px 6px -6px #777;
}

 .cityselect_modal {
    background-color:#fff;
	width:500px;
	padding:10px;
	padding-left:0px;
    display:none;
    text-align:left;
    border:2px solid #333;

    opacity:0.8;
	border:10px solid white;
	border:10px solid rgba(1, 1, 1, 0.498);
	-moz-border-radius:8px;
	-webkit-border-radius:8px;
	border-radius:8px;
  }

  .cityselect_modal h2 {
    margin:0px;
    padding:5px;
	padding-left:0px;
    font-size:14px;
  }

</style>

<div class="cityselect_modal" id="prompt">
<div style="border:1px solid lightblue;padding:5px;margin:5px;margin-right:0px;margin-left:10px;background:azure;height:250px;overflow:auto;">
<?php include("cities.inc.php"); ?>
</div>
<div style="padding:5px;">
<a href="<?php echo $script_url; ?>/0/" style="text-decoration:underline;">Show Ads in all cities</a>&nbsp;&nbsp;<button type="button" class="close"> Cancel </button>
</div>
</div>

<script>
$(document).ready(function() {
    var triggers = $(".modalInput").overlay({
      // some mask tweaks suitable for modal dialogs
      mask: {
        color: '#ebecff',
        loadSpeed: 200,
        opacity: 0.9
      },
      closeOnClick: true
  });
  });
</script>
<?php

if( empty($_COOKIE[$ck_session]) && empty($_COOKIE[$ck_userid]) )
{

	header("Location: $script_url/login.html");
	exit;
} 

?>

<div style="float:right;padding-top:5px;padding-right:5px;width:400px;">
<table width="100%">
<tr>
<td align="right" valign="middle">
<?php if($cityurl > 0) { ?> <font style="color:crimson;font-weight:bold;"><?php echo $xcityid>0 && !$postable_country?"$xcityname - $xcountryname":$xcountryname; ?></font> <?php } else { ?>Showing Ads in all regions<?php } ?>, <a style="text-decoration:underline;cursor:pointer;" class="modalInput" rel="#prompt">Select City</a>
</td>
</tr>
</table>
</div>

<!-- the tabs -->
<ul class="tabs">
	<li><a href="#">Featured Ads</a></li>
	<li><a href="#">Latest Ads</a></li>
	<?php if ($enable_calendar && !$xpostmode) { ?>
	<li><a href="#">Events</a></li>
	<?php } ?>
<li><a href="#">Search</a></li>
	
	
</ul>

<!-- tab "panes" -->
<div class="panes">

	<div>
	<?php include("picture_carousel.inc.php"); ?>
	</div>
	
	<div>
	<?php include("latest.inc.php"); ?>
	</div>
	
<?php if ($enable_calendar && !$xpostmode) { ?>
<div>
<?php include("upcoming_events.inc.php"); ?>
</div>
<?php } ?>


<div style="background:azure;">
<center>
<?php include("welcome.inc.php"); ?>
</center>
</div>


</div>

<script>
// perform JavaScript after the document is scriptable.
$(function() {
    // setup ul.tabs to work as tabs for each div directly under div.panes
    $("ul.tabs").tabs("div.panes > div");
});
</script>

<table border="0" align="left" cellspacing="5" cellpadding="10" class="dir"><tr>

<?php

// Create main directory

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
		
		echo "<td valign=\"top\" width=\"$cell_width%\">";
	}

	$i++;
	$j++;

    
    $catlink = buildURL("ads", array($xcityid, $rowcat['catid'], $rowcat['catname']));
    

	$adcount = 0+$catadcounts[$rowcat['catid']];

?>

	<table class="mainpagecats" border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
	
<th>
				
<?php $category_icon = file_exists("images/category/{$rowcat[catid]}.png") ?
"images/category/{$rowcat[catid]}.png" : "images/category.png"; ?>
<img src="<?php echo $category_icon; ?>" align="absmiddle">&nbsp;<a href="<?php echo $catlink; ?>"><?php echo @langcheck($rowcat['catname']); ?></a>
	<?php if($show_cat_adcount) { ?><span class="count">[<?php echo $adcount; ?>]</span><?php } ?>
	
	</th>

	</tr>

<?php

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

        
        $subcat_url = buildURL("ads", array($xcityid, $rowcat['catid'], $rowcat['catname'], 
            $rowsubcat['subcatid'], $rowsubcat['subcatname']));
        

?>
		<tr>
		
		
		
		<td>
		<a href="<?php echo $subcat_url; ?>"><?php echo langcheck($rowsubcat['subcatname']); ?></a>
		<?php if($show_subcat_adcount) { ?><span class="count">(<?php echo $adcount; ?>)</span><?php } ?>
		<br>
</td>
</tr>

<?php

	}

?>

	
	</table>
	<br>


<?php

	if($j==$thiscolcats || $i==$catcount) echo "</td>";

}


?>

<td align="center" valign="top" style="width:300px;margin:0px;padding:0px;padding-top:5px;">


<?php if ($enable_images && ($xview == "main" || $xsection == "imgs")) { ?>

<div style="width:298px;border:1px solid gray;background:azure;margin-top:10px;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-webkit-box-shadow:0 10px 6px -6px #777;-moz-box-shadow:0 10px 6px -6px #777;box-shadow:0 10px 6px -6px #777;">


<center>



<?php


				$sql = "SELECT COUNT(*) as imgcnt
						FROM $t_imgs a
							INNER JOIN $t_cities ct ON a.cityid = ct.cityid
						WHERE $visibility_condn AND ct.enabled = '1'
							";
				list($imgcnt) = @mysql_fetch_array(mysql_query($sql));

			?>

			<div style="padding:5px;border-bottom:1px solid gray;color:white;background:gray;font-size:14px;font-weight:bold;font-family:verdana;">
				<?php echo $lang['IMAGES']; ?> [<?php echo $imgcnt; ?>]
</div>
				
				<?php
				$rand = rand(0, $imgcnt-1);
				$sql = "SELECT a.*, UNIX_TIMESTAMP(a.createdon) AS createdon 
						FROM $t_imgs a 
							INNER JOIN $t_cities ct ON a.cityid = ct.cityid 
						WHERE $visibility_condn
							
						LIMIT $rand, 1";
				$img = @mysql_fetch_array(mysql_query($sql));

				if ($img)
				{
				
					$posterenc = EncryptPoster("IMG", $img['postername'], $img['posteremail']);
					
					$imgurl = buildURL("showimg", array($xcityid, $posterenc, $img['imgid']));
					$allimgurl = buildURL("imgs", array($xcityid));
					

					$imgsize = GetThumbnailSize("{$datadir[userimgs]}/{$img[imgfilename]}", $smallthumb_max_width, $smallthumb_max_height);
			
				?>
					<br>
					<a href="<?php echo $imgurl; ?>">
					<img src="<?php echo "{$datadir[userimgs]}/{$img[imgfilename]}"; ?>" border="0" class="thumb" id="latestimg" width="<?php echo $imgsize[0]; ?>" height="<?php echo $imgsize[1]; ?>"></a><br>
					<br>
					<?php echo $img['imgtitle']; ?><br>
					<?php echo $lang['POSTED_BY']; ?> <b><?php echo $img['postername']; ?></b>
					<br>

				<?php
				}
				?>

				<div style="padding:10px;">
				<a href="?view=postimg&cityid=<?php echo $xcityid; ?>&lang=<?php echo $xlang; ?>"><?php echo $lang['POST_IMG_LINK']; ?></a> | <a href="<?php echo $allimgurl; ?>"><?php echo $lang['ALL_IMAGES']; ?></a>
				</div>
			
				
				
			
			
</center>



</div>

<?php
}
?>

<div style="width:298px;border:1px solid crimson;background:LightGoldenRodYellow;margin-top:10px;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-webkit-box-shadow:0 10px 6px -6px #777;-moz-box-shadow:0 10px 6px -6px #777;box-shadow:0 10px 6px -6px #777;">

<div style="text-align:left;padding:5px;">
<h2><img src="images/exclamation-mark.png" align="absmiddle"> Safety Tip</h2>
<p>
Arrange to meet the seller in a public place to view the goods in question before handing over any money. We also recommend you take a friend along with you. <a style="text-decoration:underline;" href=""><b>More Tips</b></a>
</p>
</div>

</div>



</td>



</tr>



</table>
