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
<a  onclick="toggle_visibility('sub-menu')"  class="count" id="profile"></a>
<img class="profile" id="profile<?php echo $id; ?>" onclick="toggle_visibility('profile-sub-menu')" style="transition: zoom 10s ease-in-out 0s;width:auto; height:30px;max-height:30px;"src="<?php if(isset($_COOKIE[$ck_session])) { echo "images/home.png"; } else {  echo "images/home.png"; }?>" valign="middle" />
	
<script type="text/javascript">

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


	</a>	</div>
				</div>
		<ul id="profile-sub-menu" class="sub-menu">
		
			<li style="margin-top:15px;right:130px;width:200px;" class="egg">
			
			<div style="
	background-image:url(images/topprof.png);
    background-position: -82px 0;
    background-repeat: no-repeat;
    height: 11px;
    position: absolute;
    top: -11px;
    width: 20px;
	right: 25px;
			;"class="toppointer"><img src="images/topprof.png" /></div>
			<div id="commentheader"  style="border:none;width:190px;" class="profileheader" >
			<table style="margin-left:10px;">
			<tr><td>
			<img height="25px" width="25px" src="images/sss.png">
			</td>
			<td style="">
			<a valign="middle" style="width:150px;vertical-align:middle;top:6px;" class="profileheader"><?php echo "Account Settings";?></a>
			</td>
			</tr>
			</table>
			</div>
		<div style="overflow:y;" id="view_comments<?php echo $id; ?>"></div>
		<div style="background:#FFFFFF;" id="two_comments<?php echo $id; ?>">
		
				
				<div type="hidden" id="lastviewedmsg"></div>
			
		<div id="comment_ui" style="padding:0 0 0 0;width:200px;line-height:40px;background:#FFFFFF;" class="comment_ui">	
		<div  style="background:#FFFFFF;" class="comment_text">		
		<div  onclick="location.href="#" style="cursor:pointer;width:100%;" class="comment_actual_text">
<?php   if(isset($_COOKIE[$ck_session])) { ?>
		<table height="40px;">
<tr>
<td>
		<img height="25px" width="25px" onmouseout="style.borderColor='none'" onmouseover="style.borderColor='none';" style="margin-left:20px;" src="images/gear.png">	
</td>
<td  >		
		<a href="<?php echo $acc_panel_link."&amp;action=user_profile_edit"; ?>"style="
	color: #737373;
    font-size: 16px;
	padding-left:10px;
    line-height:40px;
	width:140px;
		">profile settings</a>
		
</td>
<td style="margin-right:0px;"><!-- PLACEHOLDER FOR DELETE --></td>
<tr>
<td >
		<a style="font-family:'Roboto',sans-serif;">
		</a>
</td>
</tr>
<tr>
</tr>
</table>
<?php } ?>
</div>


<!-- BOX 2-->
			
		<div  style="background:#FFFFFF;" class="comment_text">		
		<div  onclick="location.href="#" style="width:100%;cursor:pointer;"class="comment_actual_text">

		<table height="40px;">
<tr>
<td>
		<img height="25px" width="25px" onmouseout="style.borderColor='none'" onmouseover="style.borderColor='none';" style="margin-left:20px;" src="images/pin.png">	
</td>
<td  >
		<a href="bookmarkstotal.html" style="
	color: #737373;
	padding-left:10px;
    font-size: 16px;
    line-height:40px;
	width:140px;
		">saved ads</a>
</td>
<td style="margin-right:0px;"><!-- PLACEHOLDER FOR DELETE --></td>
<tr>
<td >
		<a style="font-family:'Roboto',sans-serif;">
		</a>
</td>
</tr>
<tr>
</tr>
</table>


<!-- BOX 3-->
		
		<div  style="background:#FFFFFF;" class="comment_text">		
		<div  onclick="location.href="" style="cursor:pointer;"class="comment_actual_text">
	<?php   if(isset($_COOKIE[$ck_session])) { ?>
		<table height="40px;">
<tr>

<td>
		<img height="25px" width="25px" onmouseout="style.borderColor='none'" onmouseover="style.borderColor='none';" style="margin-left:20px;" src="images/coin.png">	
</td>
<td >

		<a href="<?php echo $script_url ?>/login.html" style="
	color: #737373;
    font-size: 16px;
	padding-left:10px;
    line-height:40px;
	width:140px;
		">my posted ads</a>

</td>
	
<td style="margin-right:0px;"><!-- PLACEHOLDER FOR DELETE --></td>
<tr>
<td >
		<a style="font-family:'Roboto',sans-serif;">
		</a>
</td>
</tr>
<tr>
</tr>
</table>
	<?php } ?>
<!-- BOX 4-->
		
		<div  style="background:#FFFFFF;" class="comment_text">		
		<div  onclick="location.href="" style="cursor:pointer;"class="comment_actual_text">

		<table height="40px;">
<tr>
<td>
		<img height="25px" width="25px" onmouseout="style.borderColor='none'" onmouseover="style.borderColor='none';" style="margin-left:20px;" src="images/upload2.png">	
</td>
<td  >
		<a href="index.php?view=page&pagename=resend" style="
	color: #737373;
    font-size: 16px;
	padding-left:10px;
    line-height:40px;
	width:140px;
		">send shuffle</a>
</td>
<td style="margin-right:0px;"><!-- PLACEHOLDER FOR DELETE --></td>
<tr>
<td >
		<a style="font-family:'Roboto',sans-serif;">
		</a>
</td>
</tr>
<tr>
</tr>
</table>

	</div>			 
	</div>				
	</div>			


			<div class="bbbbbbb" id="view<?php echo $id; ?>">
					<div style="background-color:#FFFFFF; border-bottom-left-radius: 3px; border-bottom-right-radius: 3px; position: relative; z-index: 100; cursor:pointer;">
					<a style="border-top:1px solid #CCCCCC;height:40px;line-height:40px;border-right:1px solid #CCCCCC;float:left;width:98px;text-align:center;font-style:regular;font-family:'Roboto',sans-serif;font-size:12px" href="#" class="profile_view_comments" id="<?php echo $id; ?>"><font style="font-family:'Roboto',sans-serif;color:#737373;">How it works</font></a>
				
				<?php   if(isset($_COOKIE[$ck_session])) { 
			
				?>
				
				<a   style="cursor:pointer;font-family:'Roboto',sans-serif;font-weight:semibold;color:#404040;border-top:1px solid #CCCCCC;text-decoration:none;height:40px;line-height:40px;float:right;width:100px;text-align:middle;font-style:semibold;font-family:'Roboto',sans-serif;font-size:12px" href="#" class="view_comments" id="<?php echo $id; ?>">					
				<img href="http://www.shufflebuy.com/index.php?view=userpanel&action=logout"   width="20px" height="20px" style="font:'Roboto',sans-serif;font-weight:bold;color:#404040;padding-right:8px;margin-top:9px;margin-left:0;padding-left:20px;display:inline-block;float:left;" src="images/powerred.png">				
				<?= $lang['ACC_LOGOUT'] ?></a>								
				
				<? } else if(!isset($_COOKIE[$ck_session])) { ?>
				
				<a class="loginmodalInput" rel="#loginprompt"style="cursor:pointer;font-family:'Roboto',sans-serif;font-weight:semibold;color:#404040;border-top:1px solid #CCCCCC;text-decoration:none;height:40px;line-height:40px;float:right;width:100px;text-align:middle;font-style:semibold;font-family:'Roboto',sans-serif;font-size:12px" href="#" class="view_comments" id="<?php echo $id; ?>">						
				<img class="loginmodalInput"  width="20px" height="20px" style="font:'Roboto',sans-serif;font-weight:bold;color:#404040;padding-right:8px;margin-top:9px;margin-left:0;padding-left:20px;display:inline-block;float:left;" src="images/power.png">					
				Login</a>			
		
				<? } ?>
	
				</div>
				</div>
			</li>
		</ul>
		<div>
<ul>
</div>

	
<script type="text/javascript">
// $(function() 
// {
// $(".view_comments").click(function() 
// {
// var ID = $(this).attr("id");
// $.ajax({
// type: "POST",
// url: "/dropdown/dropnotification/viewajax.php",
// data: "msg_id="+ ID, 
// cache: false,
// success: function(html){
// $("#view_comments"+ID).add();
// $("#view"+ID).add();
// $("#two_comments"+ID).add();
// $("#view_comments"+ID).prepend(html);
// $("#view"+ID).hide();
// $("#two_comments"+ID).hide();
// }
// });
// return false;
// });
// });
// </script>

