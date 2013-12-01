 <?php

 include("db.php");







if(isSet($_POST['msg_id']))

{

$id = $_POST['msg_id'];



$com=mysql_query("
	SELECT
    comments.*, sale_tmp.*, h.user_id, h.adtitle
		FROM  dojoep_messaging_db.vivaru_comments AS comments
		LEFT JOIN ( SELECT s1.*
						FROM dojoep_newhampshire.vivaru_adpics as s1
						LEFT JOIN dojoep_newhampshire.vivaru_adpics AS s2
						ON s1.adid = s2.adid AND s1.picfile < s2.picfile
						WHERE s2.adid IS NULL ) as sale_tmp
		ON (comments.object_id = sale_tmp.adid)
		LEFT JOIN dojoep_newhampshire.vivaru_ads h ON h.adid = comments.object_id
		WHERE h.user_id = '".$id."'
		AND comments.deleted != 1
		ORDER BY comments.id DESC
		LIMIT 8
");

while($r=mysql_fetch_array($com))

{

$c_id=$r['id'];
$comment=$r['comment_text'];

$cc_sender=$r['sender_name'];
$comment_murl=$r['comment_url'];




?>





<div id="" class="comment_ui" >

<div class="comment_text">

<div onclick="location.href='<?php echo $comment_murl; ?>';" style="cursor:pointer;"class="comment_actual_text">
		<?
		
		if ($r['picfile']){ 
	
	$thumbnail = "$datadir[adpics]/".$r['picfile'];
				} else {
		$thumbnail = "$datadir[adpics]/defaultthumb.png";
		}
		?>				
<img width="40" align="left" height="40" onmouseout="style.borderColor='white'" onmouseover="style.borderColor='#E7EBF2';" style="top: 50%;border:1px solid #E7EBF2;margin-right:5px;" src="<?php echo $thumbnail; ?>">
				<table height="40px;">
				<tr>	
				<td style="height:30px;width:260px;">
				<?php if($cc_sender != NULL) 
					{
					?><a style="font-family:'Roboto', sans-serif;color:#404040;font-size:9pt;text-overflow: ellipsis; white-space: nowrap; overflow: hidden;word-wrap: break-word;"><? echo $cc_sender." ".$comment.""; ?>
				
				</td>				
			    <td style="margin-right:0px;"><!-- PLACEHOLDER FOR DELETE --></td>
				<tr>
	<td style="height:10px;margin-bottom:2px;">
					<a style="font-family:'Roboto',sans-serif;word-wrap:none;width:200px;">
					<?					
					if(date('Ymd') == date('Ymd', $r['created'])) { 				
					$to_time = time();				
					$from_time = date($r['created'], time());				
					echo round(($to_time - $from_time) / 60)." minute ago...";
					
					}
					else
					{				
					//echo  date('D M j g:ha'  ,    $rowsmall['created']);
					$to_time = date('Ymd');				
					$from_time = date('Ymd', $r['created']);		
					
					$day_diff =  round($to_time - $from_time);
					if($day_diff == 1 && $day_diff < 7){
					echo "sent ".$day_diff." day ago";			
					} 
					else if($day_diff > 1 && $day_diff < 7){
					echo "sent ".$day_diff." days ago";		
					}
					else if($day_diff > 7 && $day_diff < 30){
					echo "sent ".date('D M j', $r['created']);		
					} 
					else if($day_diff > 7 && $day_diff > 30 && $day_diff < 364 ) {
					echo "sent ".date('D M j', $r['created']);	
					}
					else if($day_diff > 7 && $day_diff > 30 && $day_diff > 364 ) {
					echo "sent ".date('D M j Y', $r['created']);	
					}
					}
					
					?></a>
					
					</td>
					</tr>
					<tr>
					
					</tr>
					<?
					
					} else {
					echo "<b>user</b> inquired on  ";
					echo  date('D M j g:ha'  ,    $rowsmall['created']);
					echo "<br>".$comment;}?></table>
					</div>
	<form style="display:inline-block;float:right;" method="post">
<input type="submit" class="sean" name="button1" value="" />
</form>
		
		</div>			 
		</div>				
		

<?php } }?>