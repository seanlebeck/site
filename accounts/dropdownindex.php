<?php
include ('/accounts/includes/user_security.php'); // Works.
include ('/accounts/includes/user_ads.php');  // Works.
include ('/accounts/user_panel.php');  // Works.
	session_start();
	$_SESSION['user_id'] = $_COOKIE[$ck_userid];
	//echo $_SESSION['user_id'];
	//echo $_SESSION['user_id'];
?>


<link rel="stylesheet" type="text/css" href="sjstyle.css">
		<?php $id=$_COOKIE[$ck_userid];?>
<div id="menu" style=""> <!-- THIS IS THE MAIN ICON SHOWING THE COUNT -->
	<ul>
		<li>
					<div style="position:relative;z-index:900;">
					
			<a href="<?= $_SERVER["REQUEST_URI"] ?>#"style="cursor:pointer;padding:0px 0;">
<div class="animate">			
<a  onclick="toggle_visibility('sub-menu')"  class="count" id="notifycount"></a>


<img class="checkbid" id="profile<?php echo $id; ?>" onclick="toggle_visibility('sub-menu')" style="transition: zoom 10s ease-in-out 0s;width:auto; height:20px;max-height:20px;outline:none;"src="images/nocomment.png" valign="middle" />
	
			</a>	</div>
				</div>
		<ul id="sub-menu" class="sub-menu">
		
			<?php		

	
			$msql=mysql_query(
				"SELECT b.adid, f.*
			FROM `dojoep_messaging_db`.`vivaru_comments`` f
				INNER JOIN vivaru_ads b
				ON f.object_id = b.adid
				WHERE b.user_id = '".$_COOKIE[$ck_userid]."'
				UNION select
				
				
				");
			while($messagecount=mysql_fetch_array($msql))
			$id=$messagecount['id'];
			$msgcontent=$messagecount['comment_text'];
			$sender=$rowsmall['sender_name'];		
			?>
			<li style="margin-top:15px;right:102px;" class="egg">
			
			<div class="toppointer"><img src="images/top.png" /></div>
			<div id="commentheader" class="commentheader" ></div>
				<?php 
				$sql=mysql_query(
				"SELECT b.adid, f.*
				FROM  `dojoep_messaging_db`.`vivaru_comments` f
				INNER JOIN vivaru_ads b
				ON f.object_id = b.adid
				WHERE b.user_id = '".$_COOKIE[$ck_userid]."'");
				$comment_count=mysql_num_rows($sql);
					if($comment_count>2)
					{
					$second_count=$comment_count-2;
					} 
					else 
					{
					$second_count=0;
					}
					?>
		<?php $id=$_COOKIE[$ck_userid];?>
		<div style="overflow:y;" id="view_comments<?php echo $id; ?>"></div>
		<div id="two_comments<?php echo $id; ?>">
		
	
				<?php
				
				
// TO DO fix sql query to only output one photo


				$listsql=mysql_query("

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
		WHERE h.user_id = '".$_COOKIE[$ck_userid]."'
		AND comments.deleted != 1
		ORDER BY comments.id DESC
		LIMIT 4
		
					"); // builds container
					// $me = mysql_fetch_array($listsql);
					// print_r($me);
	
				while($rowsmall=mysql_fetch_array($listsql))
				{ 
				
				$c_id=$rowsmall['id'];
				//echo $rowsmall['thumbnail'];
				$c_createdon = $rowsmall['created'][1]; 
				$c_sender=$rowsmall['sender_name'];
				//echo $c_sender;
				$comment=$rowsmall['comment_text'];
			
			
				$comment_curl=$rowsmall['comment_url'];
				$comment_curl=implode(' ', array_slice(explode(' ', $comment_curl), 0, 10));
	
				?>
				
				<div type="hidden" id="lastviewedmsg"></div>
				
		<div id="comment_ui" class="comment_ui">
		
		<div class="comment_text">
			
		<div  onclick="location.href='<?php echo $comment_curl; ?>'" style="cursor:pointer;"class="comment_actual_text">
		<?
		
		if ($rowsmall['picfile']){ 
		//echo $thumbnail;
		$thumbnail = "$datadir[adpics]/".$rowsmall['picfile'];
		} else {
		$thumbnail = "$datadir[adpics]/defaultthumb.png";
		}
		?>
<img width="40" align="left" height="40" onmouseout="style.borderColor='white'" onmouseover="style.borderColor='#E7EBF2';" style="top: 50%;border:1px solid #E7EBF2;margin-right:5px;" src="<?php echo $thumbnail; ?>">
				<table height="40px;">
				<tr>
				<td style="height:30px;width:260px;">
				<?php if($c_sender != NULL) 
					{
					?><a style="font-family:'Roboto', sans-serif;color:#404040;font-size:9pt;text-overflow: ellipsis; white-space: nowrap; overflow: hidden;word-wrap: break-word;"><? echo $c_sender." ".$comment.""; ?>
				
				</td>
				<td style="margin-right:0px;"><!-- PLACEHOLDER FOR DELETE --></td>
				<tr>
				<td style="height:10px;margin-bottom:2px;">
					<a style="font-family:'Roboto',sans-serif;word-wrap:none;width:200px;">
					<?					
					if(date('Ymd') == date('Ymd', $rowsmall['created'])) { 				
					$to_time = time();				
					$from_time = date($rowsmall['created'], time());				
					echo round(($to_time - $from_time) / 60)." minute ago...";
					
					}
					else
					{				
					//echo  date('D M j g:ha'  ,    $rowsmall['created']);
					$to_time = date('Ymd');				
					$from_time = date('Ymd', $rowsmall['created']);		
					
					$day_diff =  round($to_time - $from_time);
					if($day_diff == 1 && $day_diff < 7){
					echo "sent ".$day_diff." day ago";			
					} 
					else if($day_diff > 1 && $day_diff < 7){
					echo "sent ".$day_diff." days ago";		
					}
					else if($day_diff > 7 && $day_diff < 30){
					echo "sent ".date('D M j', $rowsmall['created']);		
					} 
					else if($day_diff > 7 && $day_diff > 30 && $day_diff < 364 ) {
					echo "sent ".date('D M j', $rowsmall['created']);	
					}
					else if($day_diff > 7 && $day_diff > 30 && $day_diff > 364 ) {
					echo "sent ".date('D M j Y', $rowsmall['created']);	
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
				<?php }?>
				
<?php
// if(isset($_POST['button1'])) {
// $q = mysql_query(
// "UPDATE ` `dojoep_messaging_db`.`vivaru_comments`` SET `deleted` = 1 WHERE `id` = '".$c_id."'"
// )or die(mysql_error());
// }
// if(isset($_POST['button2'])){
// echo("You clicked button two!");
// }//if isset
// if(isset($_POST['button3'])){
// echo("You clicked button three!");
// }//if isset
// if(isset($_POST['button4'])){
// echo("You clicked button four!");
// }//if isset
// ?> 


			<div class="bbbbbbb" id="view<?php echo $id; ?>">
					<div style="background-color: #F7F7F7; border-bottom-left-radius: 3px; border-bottom-right-radius: 3px; position: relative; z-index: 100;height:40px; cursor:pointer;">
					<a style="height:40px;line-height:40px;border-right:1px solid #CCCCCC;float:left;width:49%;text-align:center;font-style:regular;font-family:'Roboto',sans-serif;font-size:12px" href="#" class="view_comments" id="<?php echo $id; ?>"><font style="font-family:'Roboto',sans-serif;color:#737373;">View all <?php echo $comment_count; ?> comments</font></a>
					<a style="height:40px;line-height:40px;border-left:1px solid white;float:right;width:49%;text-align:center;font-style:regular;font-family:'Roboto',sans-serif;font-size:12px" href="#" class="view_comments" id="<?php echo $id; ?>"><font style="font-family:'Roboto',sans-serif;color:#737373;" >manage comments</font></a>			
					</div>
				</div>
			</li>
		</ul>
		<div>
<ul>

</div>




<?php
		//inbox message start
			




	$sql = "SELECT
                            `conversations`.`conversation_id`,
                            `conversations`.`conversation_subject`,
							`conversations`.`conversation_subject`,
							`conversations_messages`.`adid`,
                            MAX(`conversations_messages`.`message_date`) AS `conversation_last_reply`,
                            MAX(`conversations_messages`.`message_date`) > `conversations_members`.`conversation_last_view` AS `conversation_unread`
			FROM `dojoep_messaging_db`.`conversations` 
			LEFT JOIN `dojoep_messaging_db`.`conversations_messages` ON `conversations`.`conversation_id` = `conversations_messages`.`conversation_id`
			INNER JOIN `dojoep_messaging_db`.`conversations_members` ON `conversations`.`conversation_id`= `conversations_members`.`conversation_id`
			LEFT JOIN `vivaru_adpics` p ON p.adid = `dojoep_messaging_db`.`conversations_messages`.`adid` 
			WHERE `conversations_members`.`user_id` =  '".$_COOKIE[$ck_userid]."'
                        AND `conversations_members`.`conversation_deleted` = 0
			GROUP BY `conversations`.`conversation_id`
			ORDER BY `conversation_last_reply` DESC";

        $result = mysql_query($sql);    
        
        $conversations = array();
        $count = '0';
        while (($row = mysql_fetch_assoc($result))  !== false){
		
		if ($row['conversation_unread'] == 1) {
		$count ++;
		}
	
                $conversations[] = array(
                        'id' 		=> $row['conversation_id'],
                        'subject'    => $row['conversation_subject'],
                        'last_reply' => $row['conversation_last_reply'],
						'adid' => $row['adid'],
                        'unread_messages' => ($row['conversation_unread'] == 1),

		);			
	}	
	


                          // Here we gonna have more error checks around submission of form
if (empty($conversations)){


    $errors[] = 'You have no messages. ';
} else {

 $success[] = 'You have messages. ';
 }
 // Here we check to see if error array is empty and if it isn't 
if (empty($errors) === false){
    foreach ($errors as $error){
       // echo '<div class="msg error">', $error, '</div>';
    }
}


// inbox message end
?>
<audio id="soundHandle_comment" value="" style="display: none;"></audio>
<audio id="soundHandle_message" value="" style="display: none;"></audio>

    <script type="text/javascript" charset="utf-8">


 var oldTitle = "shufflebuy";
  var msg = 'shufflebuy';
  var timeoutId;
  document.title = "shufflebuy";
  var blink = function() { document.title = document.title == msg ? ' ' : msg; };
  var clear = function() {
    clearInterval(timeoutId);
    document.title = oldTitle;
    window.onmousemove = null;
    timeoutId = null;
  };



    function addmsg(newcomment, newmessage){

// use Jquery to create live elements 

//$("<div></div>").html('<p id="boxtext" style="font-weight:bold;" class="usertext">'+username+'</p><p style="display:inline-block;float:right;font-size:9pt;color:#CCCCCC;pading-right:8px;">'+date+'</p><p class="usertext" id="boxtext" class="text">'+text+'</p>').appendTo("#message");



//$("#container").animate({"scrollTop": $('#messages_viewconversation')[0].scrollHeight}, "slow");


	// alert(newcomment);
		// alert(newmessage);
	var lastcomment = $('#soundHandle_comment').val();
	var lastmessage = $('#soundHandle_message').val();

		if(lastcomment != newcomment && !!lastcomment) {
			  soundHandle_comment.src = '/alertcomment.mp3';
			  //    document.title = "("+newcomment+")shufflebuy";
					soundHandle_comment.play();
	//				  msg = newcomment+"comment";
	//				  document.title = setInterval(blink, 2000);
//						window.onmousemover = clear;

			  }
			  
		if(lastmessage != newmessage && !!lastmessage) {
			  soundHandle_message.src = '/alertmessage.mp3';
			  soundHandle_message.play();
			     if (!timeoutId) {
			  msg = newmessage+" new msg";
			    timeoutId = setInterval(blink, 2000);
      window.onmousemove = clear;
						}
			  
			   
					
				}


//alert(newcomment);
			  
  //  $('#soundHandle_comment').val(newcomment);
  //  $('#soundHandle_message').val(newmessage);
//	alert( $('#soundHandle_comment').val());
	$('.checkbid').addClass('animated pulse');
	$("#commentheader")[0].innerHTML = newcomment+" new messages";
    $('.checkbid').attr('src','images/hascomment.png'); 
	$('.checkinbox').addClass('animated pulse');
//	$("#commentheader")[0].innerHTML = newmessage;
    $('.checkinbox').attr('src','images/hasmsg.png'); 
	
	//	$('<div class="comment_ui"></div>').html( '<div class="comment_text"><div class="comment_actual_text" style="cursor:pointer;" onclick="location.href=''"><img width="40" align="left" height="40" src="adpics/52791ba08991b134d6972ed0d.jpg" style="top: 50%;border:1px solid #E7EBF2;margin-right:5px;" onmouseover="style.borderColor='#E7EBF2';" onmouseout="style.borderColor='white'"><font style="color:#000"><b>sean</b>inquired on Wed Dec 31 69 6:06pm<br>asfsdfdsf</font></div><form method="post" style="display:inline-block;float:right;"><input class="sean" type="submit" value="" name="button1"></form></div>').hide().prependTo("#two_comments3").slideDown();


        
    }

    function waitForMsg(){
        /* This requests the url "msgsrv.php"
        When it complete (or errors)*/
		var ID_count = "<?php echo $_COOKIE[$ck_userid]; ?>";
        $.ajax({
            type: "POST",
            url: "/accounts/get_commentcount.php",
			data: "userid="+ ID_count, 
            async: true, /* If set to non-async, browser shows page as "Loading.."*/
            cache: false,
            timeout:20000, /* Timeout in ms */

            success: function(data){ /* called when request to barge.php completes */
		//	alert(data);
			   // var myArr = $.parseJSON(data);
			   
            var arr = eval('(' + data + ')');
      alert(arr);
	
			var newcomment = arr.newcomment;
			var newmessage_id = arr.newmessage_id;
			var newmessage = arr.newmessage;
		//	alert(newmessage_id);
			
	//		alert(arr.newmessage);
            addmsg(newcomment, newmessage); /* Add response to a .msg div (with the "new" class)*/
			  setTimeout(
                    waitForMsg, /* Request next message */
                    10000 /* ..after 1 seconds */
                );
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                addmsg("error", textStatus + " (" + errorThrown + ")");
                setTimeout(
                    waitForMsg, /* Try again after.. */
                    10000); /* milliseconds (15seconds) */
            }
        });
    };

    $(document).ready(function(){
  //      waitForMsg(); /* Start the inital request */
    });
    </script>
   
   
	
	
<script type="text/javascript">
$(function() 
{
$(".view_comments").click(function() 
{
var ID = $(this).attr("id");
$.ajax({
type: "POST",
url: "/dropdown/dropnotification/viewajax.php",
data: "msg_id="+ ID, 
cache: false,
success: function(html){
$("#view_comments"+ID).add();
$("#view"+ID).add();
$("#two_comments"+ID).add();
$("#view_comments"+ID).prepend(html);
$("#view"+ID).hide();
$("#two_comments"+ID).hide();
}
});
return false;
});
});


</script>
<script type="text/javascript">
$(function() 
{
$(".checkbid").click(function() 
{
var ID = $(this).attr("id");
$.ajax({
type: "POST",
url: "checkedbid.php",
data: "bidid="+ ID, 
cache: false,
success: function(data){vff
  clearconsole();
}
});
return false;
setInterval(3000);
});
});


</script>
<script type="text/javascript">
<!--
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block'){
          e.style.display = 'none';
       }
	   else{
          e.style.display = 'block';
}
	  
    }
$('html').click(function() {
  //Hide the menus if visible
}); 
$('#menucontainer').click(function(event){
    event.stopPropagation();
});	
$(document).click(function(event) { 
    if($(event.target).parents().index($('#menu')) == -1) {
	var ID = "<? echo $_COOKIE[$ck_userid]; ?>"
        if($('#menu').is(":visible")) {
$("#view_comments"+ID).empty();
$("#view"+ID).show();
$("#two_comments"+ID).show();
$('#sub-menu').hide()
        }

    }        
})
</script>
