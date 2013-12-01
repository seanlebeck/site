<?php
require_once('dbconnect.inc.php');
mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
mysql_select_db($db_name) or die(mysql_error());

$div_id = $_GET['id'];
$rating = $_GET['rating'];

if($rating){
//Check that this person hasn't already left a rating
$result = mysql_query("SELECT id FROM vivaru_ratings WHERE div_id = '" . $div_id . "' AND ip = '" . $_SERVER['REMOTE_ADDR'] . "'") or die(mysql_error());  
$row = mysql_fetch_array( $result );
if(!$row['id']){
	$result = mysql_query("INSERT INTO vivaru_ratings (rating,div_id,ip) VALUES('" . $rating . "','" . $div_id . "','" . $_SERVER['REMOTE_ADDR'] . "')") or die(mysql_error());  	if($result){ $report = "<span style=\"color: green;\"> You rated this: $rating</span>"; }
} else {
 $report = "<span style=\"color: red;\"> You have already voted.</span>";
}
}
?>

<?php
$result = mysql_query("SELECT avg(rating), COUNT(*) FROM vivaru_ratings WHERE div_id = '" . $div_id . "'") or die(mysql_error());  
$row = mysql_fetch_array( $result );

$x=1;
while($x<=5){
	$stars .= "<li><a onClick=\"javascript:addRating('$div_id','$x');\" class=\"star$x\">$x</a></li>";
	$x++;
}
?>

<ul class='star-rating'>
<li class="current-rating" id="current-rating" style="width: <?php echo round($row[0],1) * 20; ?>%;"></li>
<? echo $stars; ?>
</ul>
<div><small><?php echo "<span style=\"color: grey;font-weight:bold;\">Rating: " . round($row[0],1) . "/5 (" . $row[1] . " Votes)</span> " . $report; ?></small></div>
