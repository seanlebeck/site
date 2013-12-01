<div id="background"></div>
	
	
			<div id="application">
				<div id="header">
				
				<h1>
<a href="/"></a>
</h1>
				<div class="header-view">
			<h1>
			<a class="main-header-link" href=""></a>
			</h1>
				<div class="navigation">
<div class="signin">
<a id="signin" href="/account.html&action=logout">
<?php 
if(isset($_COOKIE[$ck_session]))
{  
$last = $_COOKIE[$ck_session]; ?>
<span align="left" href="/account.html&action=logout" value="<?php session_destroy();
?>" style="cursor:pointer;">Logout</span><div class="fb-login-button" autologoutlink="true" scope="publish_stream" data-show-faces="false" data-width="200" data-max-rows="1"></div>
<?php
} 
else 
{ 
?>
<span style="cursor:pointer;" class="loginmodalInput" rel="#loginprompt">Signin</span>
<?php 
} 
?>	
</a>
</div>
<ul class="global">
<li>
 <?php if(isset($_COOKIE[$ck_session]))
 {  
 $last = $_COOKIE[$ck_session]; ?>
<span><div><?php


 include ("commentengine.php");?></div></span> <?php } ?>
</li>

<li>
<?php
 if(isset($_COOKIE[$ck_session]))
 {  
 $last = $_COOKIE[$ck_session]; ?>
<a style="
  image-rendering: -moz-crisp-edges;         /* Firefox */
                
position:relative;bottom:12px;" href="http://www.vermont.shufflebuy.com/account.html"><img style="margin-bottom:4px;margin-right:0px;transition: zoom 10s ease-in-out 0s;opacity:.9;margin-top:12px;width:auto;height:21px; max-height:21px;" src="images/setup.png" /></a>
	<?php 
	}	 
	?>		
</li>
<li>

</li>
<li>
<a style="cursor:pointer;position:relative;bottom:12px;" align="right" href="/bookmarkstotal.html" id="joebook">

				<?php if($_COOKIE['bookmark'] != "." && $_COOKIE['bookmark'] != ""){
				?><img style="margin-bottom:4px;margin-right:0px;transition: zoom 10s ease-in-out 0s;opacity:.9;margin-top:11px;width:auto;height:25px; max-height:23px;" src="images/bookmark.png" /></a>	
				<?php
				}else{ 	
				?><img style="
				  image-rendering: -moz-crisp-edges;         /* Firefox */
                   image-rendering:   -o-crisp-edges;         /* Opera */
                   image-rendering: -webkit-optimize-contrast;/* Webkit (non-standard naming) */
                   image-rendering: crisp-edges;
                   -ms-interpolation-mode: nearest-neighbor;  /* IE (non-standard property) */
				margin-bottom:4px;margin-right:0px;transition: zoom 10s ease-in-out 0s;opacity:.9;margin-top:11px;width:auto;height:25px; max-height:23px;" src="images/bookmark.png" />
				<?php
				 echo '<span id="mess3"></span>';unset($_COOKIE["bookmark"]);setcookie("bookmark", "", time()-3600);}?></a>		
</li>
</ul>
</div>
</div>