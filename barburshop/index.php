<?php

require_once("initvars.inc.php");
require_once("config.inc.php");

if($_GET['lang']){
	$url_safe = preg_replace('/&lang=.{2}/', '', $_SERVER['REQUEST_URI']);
	$url_safe = preg_replace('/\?lang=.{2}$/', '', $url_safe);
	$url_safe = preg_replace('/\?lang=.{2}/', '?', $url_safe);
	header('location: '.$url_safe);
	exit();
	}

require_once('mobile_device_detect.php');
mobile_device_detect(true,false,true,true,true,true,true,$mobile_site_url,false);


if($offline == "yes") 
{ 
echo "<h3 align='center'>".$offmesg."</h3>"; 
exit(); 
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html lang="<?php echo $langx['lang']; ?>">

<head>
<title><?php echo $page_title; ?></title>
<base href="<?php echo $script_url; ?>/">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langx['charset']; ?>">
<meta name="keywords" content="<?php echo $meta_keywords; ?>">
<meta name="description" content="<?php echo $meta_description; ?>">
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="pager.css">
<link rel="stylesheet" type="text/css" href="cal.css">


<link rel="alternate" type="application/rss+xml" title="<?php echo rssTitle("", ""); ?>" 
	href="<?php echo "{$script_url}/{$global_rssurl}"; ?>">
<?php if (!empty($rssurl)) { ?>
<link rel="alternate" type="application/rss+xml" title="<?php echo rssTitle(($xsubcatname?$xsubcatname:$xcatname), ($xcityname?$xcityname:"")); ?>" 
	href="<?php echo "{$script_url}/{$rssurl}"; ?>">
<?php } ?>

<script src="jquery.tools.min.js"></script>

<!-- comments code start -->
<?php
if($xview == "showad" OR $xview == "showevent" OR $xview == "showimg") { 
?> 
<link rel="stylesheet" href="comments/css/comments.css" type="text/css" media="screen" />
<script type="text/javascript" src="comments/js/comment.js"></script>
<?php
}
?>
<!-- comments code end-->
<script language="JavaScript" src="gen_validatorv4.js"
    type="text/javascript" xml:space="preserve"></script>

<?php if ($xview == "showad" OR $xview == "showevent") { ?>
<!-- star rating files -->
<link href="rateanything.css" rel="stylesheet" type="text/css" />
<script src="rateanything.js" type="text/javascript" language="javascript"></script>
<?php } ?>


<?php
if($xview == "ads") { 
?>  

<script type="text/javascript" src="bookmarkAds.js"></script>


<?php
}
?>



<!-- clear input on focus starts here. just add onFocus="clearText(this)"-->
<script>


function clearText(thefield){
if (thefield.defaultValue==thefield.value)
thefield.value = ""
} 
</script>
<!-- clear input on focus ends here -->


<?php

if ($xview == "selectcity") {

?>

<!-- ajax cat loader starts here -->

<script type="text/javascript">

var bustcachevar=1 //bust potential caching of external pages after initial request? (1=yes, 0=no)
var loadedobjects=""
var rootdomain="http://"+window.location.hostname
var bustcacheparameter=""

function ajaxpage(url, containerid){
var page_request = false
if (window.XMLHttpRequest) // if Mozilla, Safari etc
page_request = new XMLHttpRequest()
else if (window.ActiveXObject){ // if IE
try {
page_request = new ActiveXObject("Msxml2.XMLHTTP")
} 
catch (e){
try{
page_request = new ActiveXObject("Microsoft.XMLHTTP")
}
catch (e){}
}
}
else
return false
page_request.onreadystatechange=function(){
loadpage(page_request, containerid)
}
if (bustcachevar) //if bust caching of external page
bustcacheparameter=(url.indexOf("?")!=-1)? "&"+new Date().getTime() : "?"+new Date().getTime()
page_request.open('GET', url+bustcacheparameter, true)
page_request.send(null)
}

function loadpage(page_request, containerid){
if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1))
document.getElementById(containerid).innerHTML=page_request.responseText
}

function loadobjs(){
if (!document.getElementById)
return
for (i=0; i<arguments.length; i++){
var file=arguments[i]
var fileref=""
if (loadedobjects.indexOf(file)==-1){ //Check to see if this object has not already been added to page before proceeding
if (file.indexOf(".js")!=-1){ //If object is a js file
fileref=document.createElement('script')
fileref.setAttribute("type","text/javascript");
fileref.setAttribute("src", file);
}
else if (file.indexOf(".css")!=-1){ //If object is a css file
fileref=document.createElement("link")
fileref.setAttribute("rel", "stylesheet");
fileref.setAttribute("type", "text/css");
fileref.setAttribute("href", file);
}
}
if (fileref!=""){
document.getElementsByTagName("head").item(0).appendChild(fileref)
loadedobjects+=file+" " //Remember this object as being already added to page
}
}
}

</script>

<!-- ajax cat loader ends here -->


<script type="text/javascript">
var whosChanged = null;
function changeMe(el)
{
el.style.backgroundColor = "PaleGreen";
el.style.color = "#000000";
el.style.fontSize ="12px";
if (whosChanged != null)
{
whosChanged.style.backgroundColor = "#FFEFD5"
whosChanged.style.color = ""
whosChanged.style.fontSize ="12px";
}
whosChanged = el;
}


var whosChanged2 = null;
function changeMe2(el)
{
el.style.backgroundColor = "PaleGreen";
el.style.color = "#000000";
el.style.fontSize ="12px";
if (whosChanged2 != null)
{
whosChanged2.style.backgroundColor = "#FFEFD5"
whosChanged2.style.color = ""
whosChanged2.style.fontSize ="12px";
}
whosChanged2 = el;
}


var whosChanged3 = null;
function changeMe3(el)
{
el.style.backgroundColor = "PaleGreen";
el.style.color = "#000000";
el.style.fontSize ="12px";
if (whosChanged2 != null)
{
whosChanged3.style.backgroundColor = "#FFEFD5"
whosChanged3.style.color = ""
whosChanged3.style.fontSize ="12px";
}
whosChanged3 = el;
}


</script>

<?php
}
?>

<script type="text/javascript">
window.onload=function(){
var formref=document.getElementById("switchform")
indicateSelected(formref.switchcontrol)
}
</script>



<?php if ( $xview == "signup" || $xview == "login") { ?>
    <script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
<?php } ?>

</head>

<?php
if ($xview == "showad" OR $xview == "showevent") {
?>

<body onload="load()" onunload="GUnload()" >

<?php

}

else {

?>

<body>

<?php

}

$cityurl = buildURL("main", array($xcityid, $xcityname));
$homeurl = buildURL("main", array(0));

?>

<table align="center" cellpadding="0" cellspacing="0" style="">
<tr>
<td>

<table class="websiteheader" align="center" width="1000" cellspacing="0" cellpadding="0" style="border-top-right-radius: 4px;-moz-border-top-right-radius:4px;border-top-left-radius: 4px;-moz-border-top-left-radius:4px;">


<tr>
<td align="left" valign="middle" style="color:white;">

<a href="<?php echo $homeurl; ?>">

</a>
</td>

<td align="right" valign="middle" style="color:white;">



</td>
</tr>
</table>

<?php if ($xview == "main") { ?>
<table width="1000" align="center" cellspacing="0" cellpadding="0" bgcolor="azure" style="">
<tr>

<td align="left" valign="middle" style="padding-left:10px;width:122px;">
<a href="<?php echo $script_url ?>?view=selectcity&targetview=post&cityid=0">
<img src="images/postneworange.png" style="border:0px;margin-top:1px;">
</a>
</td>

<td align="left" valign="middle" style="padding-left:10px;width:68px;">
<a href="<?php echo $script_url ?>/login.html">
<img src="images/signin.png" style="border:0px;padding-top:1px;">
</a>
</td>

<td align="left" valign="middle" style="padding-left:10px;padding-right:10px;width:78px;">
<a <a href="<?php echo $script_url ?>/signup.html">
<img src="images/register.png" style="border:0px;padding-top:1px;">
</a>
</td>

<td align="left" valign="middle">
<a style="text-decoration:underline;" href="bookmarkstotal.html"><?php echo $lang['BOOKMARKS_DESCRIPTION']; ?></a>&nbsp;&nbsp;&nbsp;<a style="text-decoration:underline;" href="index.php?view=page&pagename=resend"><?php echo $lang['FIND_MY_ADS']; ?></a>
</td>

<td align="right" valign="middle">
[langimgs]
</td>

</tr>
</table>
<?php } ?>

<table width="1000" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="white" style="">
<tr>
<td valign="top" id="contentcell" style="	background:#952936;padding-top:5px;">

<table width="100%" cellspacing="0" cellpadding="0" style="background:#952936;">
		<tr>
		<td id="content">

		<?php

        $page = "main.php";
		switch($xview)
		{
			case "subcats"		: $page = "subcats.php";			break;
		
			case "login"	    : $page = $acc_dir . "/login.php";	         break;
			case "userpanel"    : $page = $acc_dir . "/user_panel.php";	 break;
			case "signup"	    : $page = $acc_dir . "/signup.php";		 break;
			case "forgot"	    : $page = $acc_dir . "/forgot.php";		 break;
		
			
			case "ads"		: $page = "ads.php";				break; 
			case "events"		: $page = "ads.php";				break;
			case "showad"		: $page = "showad.php";				break;
			case "showevent"	              : $page = "showad.php";				break;
			case "post"		: $page = "post.php";				break;
			case "spec"		: $page = "spec.php";			              break;
			case "edit"		: $page = "edit.php";		              	break;
			case "renew"		: $page = "renew.php";				break;
			case "imgs"		: $page = "imgs.php";				break;
			case "showimg"		: $page = "showimg.php";				break;
			case "postimg"		: $page = "postimg.php";				break;
			case "widget"		: $page = "widget.php";				break;
			case "editimg"		: $page = "editimg.php";				break;
			case "activate"		: $page = "activate.php";				break;
			case "selectcity"		: $page = "selectcity.php";				break;
			case "mailad"		: $page = "mailad.php";				break;
			/*bookmark mod start*/
			case "bookmarkstotal"	: $page = "bookmarkstotal.php";	break;
			/*bookmark mod end*/
			
			
	
			case "post404"		: $page = "post404.php";				break;
			
			case "page"			: if (isCustomPage($_GET['pagename'])) { $page = "$_GET[pagename].php"; }	break;
		}

		include_once($page);

		?>

		</td>
		</tr></table>






		</td>

<?php if($xview == "main") { ?>

	
			</td>	

		<?php } 

elseif($show_right_sidebar) { ?>

			<td width="120" valign="top" id="sidebar_right" bgcolor="white" style="padding-right:5px;">

			<?php include("sidebar_right.inc.php"); ?>

			</td>

		<?php } ?>
		

	</tr>







<tr><td colspan="3"><?php include("footer.inc.php"); ?>
</td></tr>


</table>


</td></tr>
</table>

</td></tr></table>

</body>
</html>