<script type="text/javascript">
    FB.init({appId: '<?php echo $fb_app_api; ?>', status: true,
        cookie: true, xfbml: true});
</script>
<?php
include 'includes/config.php';

?>
  <link rel="stylesheet" href="loginstyle.css"> <!---->
<table height="600px">
<body style="color: #404040;direction: ltr;font-family: Roboto,arial,sans-serif,Arial,sans-serif;font-size: 13px;">
  

<td width="50%" style="padding-top:5px;padding:10px;"  valign="top">
<body style="color: #404040;direction: ltr;font-family: Roboto,arial,sans-serif,Arial,sans-serif;font-size: 13px;">

<a style="a:hover,a:visited { color: rgb(66, 127, 237); cursor: pointer; text-decoration: none; }">
<div style="margin-top:-40px;position:relative;z-index:915;" class="main content clearfix">
<div style="	background:#952936;"class="banner"></div>
<div class="card signin-card clearfix">
<img class="profile-img" alt="" src="/images/logo.jpg">
<p class="profile-name"></p>


<form method="post" action="/login.html">
<input class="post" type="text" value="" maxlength="30" size="25" name="username">
<input class="inputpassword" type="password" value="" id="password"  placeholder="password"  maxlength="30" size="25" name="password">
<input type="hidden" value="1" name="savecookies">
<input value="Submit" name="submit" type="submit"  class="rc-button rc-button-submit" style="font-family: Arial,sans-serif; ">Submit</input>
<label class="remember">
<!--<input id="PersistentCookie" type="checkbox" checked="checkbox" class="stayin" value="yes" name="PersistentCookie">-->
<span> Stay signed in </span>
<div style="display:none;" class="bubble-wrap" role="tooltip">
<div class="bubble-pointer"></div>
<div class="bubble">For your protection, keep this checked only on devices you use regularly.<a >Learn more</a>
</div>
</div>
</label>
<a id="link-forgot-passwd" class="need-help-reverse" > Need help? </a>
</form>



</div>
  <div>	  
	  <div class="card signin-card clearfix" style="height:20px;">          
     <div class="G-q-B" style="    background-color: #FFFFFF;line-height: 1.4em;font: 13px Roboto,arial,sans-serif;color:#404040;"><a style="color:#404040;float:left;" href="<?= $acc_signup_link ?>">Sign up</a>
	</div>
	 <div class="G-q-B" style="    background-color: #FFFFFF;line-height: 1.4em;font: 13px Roboto,arial,sans-serif;color:#404040;">
	<a style="color:#404040;float:right;padding-right:15px;">or log in with<span style="float:right;padding-left:15px;line-height:40px;margin-top:-3px;"><fb:login-button size="medium" perms="email" onlogin="window.location='accounts/fbReg.php';"></fb:login-button>

<div style="display:true !;" id="fb-root"></div>


</span>
</div>
</a>
      </div>
      </div>
</body>


</td>
</tr>
</table>
