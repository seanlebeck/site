

<?php



require_once("initvars.inc.php");
require_once("config.inc.php");
require_once($acc_dir . "/" . $inc_path. "/post_security.php");


$targetview = $_GET['targetview'];
$citylink_view = "post.php?postevent=$_GET[postevent]";
//$citylink_view = "view=selectcity&targetview=$targetview";

if($location_sort) 
{
	$sort1 = "ORDER BY countryname";
	$sort2 = "ORDER BY cityname";
	
	$sort3 = "ORDER BY areaname";
	
}
else
{
	$sort1 = "ORDER BY c.pos";
	$sort2 = "ORDER BY ct.pos";
	
	$sort3 = "ORDER BY pos";
	
}

?>

<div style="border:1px solid lightblue;background:Azure;padding:10px;margin:10px;border-radius: 3px;-moz-border-radius:3px;">

&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $targetview=="postimg"?$lang['POST_IMG']:$lang['POST_AD']; ?></b>


&nbsp;&nbsp;

<?php
// BEGIN Account Mod
if ( $enable_account && ( $xview == "post" || $xview == "selectcity" ) )
{
	
	if ( $_COOKIE[$ck_userid] )
	{
		echo $lang['ACC_LOGGED_IN'] . ' <a href="'.$acc_panel_link.'">'.$_COOKIE[$ck_username].'</a>';
	}
	else
	{
		echo '<a href="'.$acc_login_link.'"><font size="1">'.$lang['ACC_LOGIN_TEXT'].'</font></a>';
	}
	
} 
// END Account Mod
?>


<hr>



<?php
if ($_GET['cityid'] > 0)
{
	$sql = "SELECT * FROM $t_areas WHERE cityid = $_GET[cityid] {$sort3}";	
	$res = mysql_query($sql);

	if(!mysql_num_rows($res))
	{
		header("Location: $script_url/?view=$targetview&postevent=$_GET[postevent]&cityid=$_GET[cityid]");
		exit;
	}
	else
	{
?>

    <div class="postpath"><?php echo "<b>$xcountryname</b> &raquo; <b>$xcityname</b>"; ?> 
    <?php if(!$in_admin) { ?>
    &nbsp; (<a href="?view=selectcity&targetview=post"><?php echo $lang['CHANGE']; ?></a>)
    <?php } ?>
    </div><br>
    
    
	<?php echo $lang['POST_SELECT_AREA']; ?><br>
	<ul class="postcats">

<?php
		while($area = mysql_fetch_array($res))
		{
?>
		
			<li><a href="?view=<?php echo $targetview; ?>&cityid=<?php echo $xcityid; ?>&area=<?php echo $area['areaname']; ?>"><?php echo $area['areaname']; ?></a></li>

<?php
		}
?>

			<li><a href="?view=<?php echo $targetview; ?>&cityid=<?php echo $xcityid; ?>"><b><?php echo $lang['SKIP_STEP']; ?></b></a></li>

	</ul>

<?php
	}
?>

<?php
}
else
{
?>

    
    <?php if ($_GET['cityid'] < 0) { ?>
    <div class="postpath"><?php echo "<b>$xcountryname</b>"; ?> 
    <?php if(!$in_admin) { ?>
    &nbsp; (<a href="?view=selectcity&targetview=post"><?php echo $lang['CHANGE']; ?></a>)
    <?php } ?>
    </div><br>
    <?php } ?>
    


<table style="margin-left:12px;" cellpadding="0" cellspacing="0">

<tr>

<td>
<div style="width: 250px;padding:2px;padding-right:0px;border:5px solid rgba(0, 128, 255, 0.498);-moz-border-radius:8px;-webkit-border-radius:8px;border-radius:8px;">

<div style="height: 280px;overflow: auto;border:1px solid #35608f;margin-right:2px;">

<div style="background:#35608f;color:white;text-align:center;padding:6px;font-size:14px;font-family:verdana;">
<?php echo $lang['POST_SELECT_CITY']; ?>
</div>

	

	<?php

	// Show city list
	
	
	if ($_GET['cityid'] < 0) {
	    $countryid = 0 - $_GET['cityid'];
    	$sql = "SELECT * FROM $t_countries c WHERE c.countryid = {$countryid} AND c.enabled = '1'";
    	
	} else {
	
    	$sql = "SELECT * FROM $t_countries c INNER JOIN $t_cities ct ON c.countryid = ct.countryid AND ct.enabled = '1' WHERE c.enabled = '1' GROUP BY c.countryid $sort1";
    }
	
	
	$resc = mysql_query($sql);
	    	
	while($country = mysql_fetch_array($resc))
	{

	?>

<table width="100%" cellpadding="0" cellspacing="0">



<tr><td align="left" valign="middle">
<div  style="border:1px solid #D3D3D3;font-weight:bold;padding-top:5px;padding-bottom:5px;padding-left:5px;font-size:12px;background:aliceblue;">
	<b><i><?php echo $country['countryname']; ?></i></b><br>
</div>
</td></tr>


	<?php

		$sql = "SELECT * FROM $t_cities ct WHERE countryid = $country[countryid] AND enabled = '1' $sort2";
		$resct = mysql_query($sql);

		while($city=mysql_fetch_array($resct))
		{

		?>
		
		
	


	
		
<tr>

<td align="left" valign="middle">
<a href="javascript:ajaxpage('<?php echo $citylink_view; ?>&cityid=<?php echo $city['cityid']; ?>&lang=<?php echo $xlang; ?>&catid=<?php echo $_GET['catid']; ?>&subcatid=<?php echo $_GET['subcatid']; ?>', 'contentarea');">
<div  style="border:1px solid #D3D3D3; padding-top:5px;padding-bottom:5px;padding-left:20px;font-size:12px;background-color:#FFEFD5;" onclick="changeMe(this);">
<b><?php echo $city['cityname']; ?></b>
</div>
</a>
</td>



</tr>


		
				
		<?php
		
		}

		?>

		


</table>
		

	<?php

	}				

	?>



<?php
}
?>



</div>

</div>

</td>

<td>
<div id="contentarea" style="width: 250px;padding: 2px;background-color:white;border:5px solid rgba(0, 128, 255, 0.498);-moz-border-radius:8px;-webkit-border-radius:8px;border-radius:8px;"></div>
</td>

<td>
<div id="contentarea2" style="width: 250px;padding: 2px;background-color:white;border:5px solid rgba(0, 128, 255, 0.498);-moz-border-radius:8px;-webkit-border-radius:8px;border-radius:8px;"></div>
</td>


</tr>
</table>


</div>

<center>

<img src="images/demo-banner2.png">

</center>





