<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");


if($_GET['msg']) $msg = $_GET['msg'];
if($_GET['err']) $err = $_GET['err'];

?>
<?php include_once("aheader.inc.php"); ?>

<script language="javascript">
function showMessage() {
	document.getElementById('selection_changed_msg').style.display = 'block';
}
</script>

<h2>Post an Ad</h2>

<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<form method="get" action="" class="box">
<table>

<tr>

<td><b>City:</b></td>
<td>
<select name="cityid" onchange="showMessage();">
<?php
$sql = "SELECT cityid, cityname, countryname
		FROM $t_cities ct
			INNER JOIN $t_countries c ON ct.countryid = c.countryid
		ORDER BY c.pos, ct.pos";
$res = mysql_query($sql) or die(mysql_error());

while($row=mysql_fetch_array($res))
{
	echo "<option value=\"$row[cityid]\"";
	if ($row['cityid'] == $_REQUEST['cityid']) echo " selected";
	echo ">$row[countryname] > $row[cityname]</option>";
}

?>
</select>
</td>

</tr>
<tr>

<td><b>Category:</b></td>

<td>
<select name="subcatid" onchange="showMessage();">
<?php
$sql = "SELECT scat.subcatid, scat.subcatname, cat.catname
		FROM $t_subcats scat
			INNER JOIN $t_cats cat ON scat.catid = cat.catid
		ORDER BY cat.pos, scat.pos";
$res = mysql_query($sql) or die(mysql_error());

while($row=mysql_fetch_array($res))
{
	echo "<option value=\"$row[subcatid]\"";
	if ($row['subcatid'] == $_REQUEST['subcatid']) echo " selected";
	echo ">$row[catname] > $row[subcatname]</option>";
}

?>
<option value="-1" <?php if ($_REQUEST['subcatid'] == -1) echo " selected"; ?>>Events</option>
</select> 

<button type="submit">Go</button></td>

</tr>

</table>

<div class="tip" style="background-color: white; border:1px solid silver; padding:5px; margin-top:5px; width:550px; display:none;" id="selection_changed_msg">
<div style="float:left"><img src="images/tip.gif"></div>
Hit the <b>Go</b> button after making the selection to load the form for the selected city/category.
Otherwise, the ad will be getting posted to the previously selected city and category.
</div>

</form><br>


<?php 
if(($_GET['cityid'] && $_GET['subcatid']))
{
?>
<table><tr><td valign="top"><img src="images/tip.gif" align="absmiddle">&nbsp;</td><td valign="top" class="tip">The email field is optional. If you do not enter one, no reply address will be used with the ad.</td></tr></table><br>
<?php
	include_once("{$path_escape}post.php");
}
else
{
?>
<div class="infobox">Please select a city and category to post to</div>
<?php
}
?>


<?php include_once("afooter.inc.php"); ?>