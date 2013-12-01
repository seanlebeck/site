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
<script type="text/javascript" src="js/retina.js"></script>
<head>
<title><?php echo $page_title; ?></title>
<base href="<?php echo $script_url; ?>/">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langx['charset']; ?>">
<meta name="keywords" content="<?php echo $meta_keywords; ?>">
<meta name="description" content="<?php echo $meta_description; ?>">
<link href='http://fonts.googleapis.com/css?family=Roboto:400,100italic,100,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Roboto:400,100italic,100,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link href='http://fonts.googleapis.com/css?family=Montserrat+Alternates:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="pager.css">
<link rel="stylesheet" type="text/css" href="styled.css">
<link rel="stylesheet" type="text/css" href="cal.css">
<script type="text/javascript" src="http://j.maxmind.com/app/geoip.js"></script>

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

<?php
$xcity="1";
/// HAHAHA IT WORKED!   SELECT distinct(city_zip) FROM cities WHERE (3958*3.1415926*sqrt((city_latitude-'42.7959')*(city_latitude-'42.7959') + cos(city_latitude/57.29578)*cos('42.7959'/57.29578)*(city_longitude-'-71.057')*(city_longitude-'-71.057'))/180) <= '25';
$location = file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']);
$locationdata = json_decode($location, TRUE);
//$location2 = var_dump(json_decode($location));
 $lat =$locationdata[latitude];
 $long= $locationdata[longitude];
 $zipcode= $locationdata[zipcode];
 $city = $locationdata[city];
  $state = $locationdata[region_code];
//  echo $city; echo $state;
 //echo $lat; echo $long; echo $city; echo $state;
 $radius ="20";
// get all the zipcodes within the specified radius - default 20
function zipcodeRadius($lat, $lon, $radius)
{
    $radius = $radius ? $radius : 20;
//	echo $radius;
    $sql = 'SELECT distinct(city_zip) FROM cities WHERE (3958*3.1415926*sqrt((city_latitude-42.7959)*(city_latitude-42.7959) + cos(city_latitude/57.29578)*cos(42.7959/57.29578)*(city_longitude-'.$lon.')*(city_longitude-'.$lon.'))/180) <= '.$radius.';';
  //  $result = $this->db->query($sql);
    $result = $this->db->query($sql);
  //  $result = mysql_query($sql);
    // get each result
echo $result;	

// print_r($result);
    $zipcodeList = array();
    while($row = mysql_fetch_assoc($result))
    {
        array_push($zipcodeList, $row['city_zip']);
    }
    print_r($zipcodeList);
}
// //	$com =  
			// "SELECT city_name
			// FROM cities
			// WHERE city_state IN ('ME', 'VT', 'MA', 'NH', 'CN', 'RI', 'NY', 'PA', 'DE', 'MY', 'OH')  
			// ";
// $sql = mysql_query("SELECT city_name, city_zip
// FROM cities 
// WHERE city_state IN ('ME', 'VT', 'MA', 'NH', 'CN', 'RI', 'NY', 'PA', 'DE', 'MY', 'OH')  
// ");
while($row = mysql_fetch_array($sql)){
    $array1 = $row['city_name'];
    $array2 = $row['city_zip'];
   // $array3 = $row['url'];
   echo $array1;
   print_r($array2);
}
 ?>
 
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

<?php


include("accounts/catheader.inc.php");

?>
<style>
    .map_modal{
	left: 440px;
    top: 109px;  
  
    top: 0;
	
	color:#404040;
 
    z-index: 1101;
	background: none repeat scroll 0 0 padding-box #FFFFFF;
    border: medium none rgba(0, 0, 0, 0);
    box-shadow: 0 0 40px rgba(0, 0, 0, 0.4);
    outline: 0 none;
    position: absolute;
	width:610px;
	overflow:hidden;
	display:none;
  }
  svg { position:fixed; top:0; left:0; height:100%; width:100% }
</style>

<script>
$(document).ready(function() { 
	
 var triggers = $("#locationmodalInput").overlay({   
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
        <script type="text/javascript">

// $("#locationmodalInput").live('click', function() {
// // Reformat Map container
// // $("#mapsvg-usa").css({"height":"320px"});	
// // $("#mapsvg-usa").css({"position":"relative"});	
// // $("#mapsvg-usa").css({"max-width":"560px"});	
// // $("#mapsvg-usa").css({"max-height":"320px"});	
// // $("#mapsvg-usa").css({"width":"100%"});	
// // $("#mapsvg-usa").css({"top":"0"});	
// // $("#mapsvg-usa").css({"min-height":"320px"});	
// // $("#mapsvg-usa").css({"left":"0"});	
// // $("#mapsvg-usa").css({"padding-bottom":"10px"});	
// // $("#mapsvg-usa").css({"padding-top":"5px"});	
// // Create map header
// $("#gobackNE").css({"padding-left":"50px"});	
// $("#gobackUSA").css({"padding-left":"50px"});	
// $("#locationprompt").css({"position":"relative"});	
// $("#locationprompt").css({"padding-bottom":"10px"});	




// });
// $("#mapsvg-states").live('click', function() {

// });
	</script>

<div class="map_modal"  style="position:absolute;" id="locationprompt">
<?php include("bmaps/index.html") ?>
</div>

<div id="location" data-locationval="location"></div>
<?php




// MAP SECTION

// $xcity="1";
// /// SELECT distinct(city_zip) FROM cities WHERE (3958*3.1415926*sqrt((city_latitude-'42.7959')*(city_latitude-'42.7959') + cos(city_latitude/57.29578)*cos('42.7959'/57.29578)*(city_longitude-'-71.057')*(city_longitude-'-71.057'))/180) <= '25';
// $location = file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']);
// $locationdata = json_decode($location, TRUE);
// // $location2 = var_dump(json_decode($location));
 // $lat =$locationdata[latitude];
 // $long= $locationdata[longitude];
 // $city = $locationdata[city];
  // $state = $locationdata[region_code];
// //  echo $city; echo $state;
 // //echo $lat; echo $long; echo $city; echo $state;
 // $radius ="20";
// // get all the zipcodes within the specified radius - default 20
// function zipcodeRadius($lat, $lon, $radius)
// {
    // $radius = $radius ? $radius : 20;
	// echo $radius;
    // $sql = 'SELECT distinct(city_zip) FROM cities WHERE (3958*3.1415926*sqrt((city_latitude-42.7959)*(city_latitude-42.7959) + cos(city_latitude/57.29578)*cos(42.7959/57.29578)*(city_longitude-'.$lon.')*(city_longitude-'.$lon.'))/180) <= '.$radius.';';
  // //  $result = $this->db->query($sql);
    // $result = $this->db->query($sql);
  // //  $result = mysql_query($sql);
    // // get each result
// echo $result;	print_r($result);
    // $zipcodeList = array();
    // while($row = mysql_fetch_assoc($result))
    // {
        // array_push($zipcodeList, $row['city_zip']);
    // }
    // print_r($zipcodeList);
// }
// while($row = mysql_fetch_array($sql)){
    // $array1 = $row['city_name'];
	
    // $array2 = $row['city_zip'];
   // // $array3 = $row['url'];
   // echo $array1;
   // print_r($array2);
// }
 ?>




<table align="center" cellpadding="0" cellspacing="0"  style="">
<tr>
<td>
<div>

</div>
</td>
<tr>
</table>


<table align="center" cellpadding="0" width="100%" cellspacing="0" style="">
<tr>
<td>
<table class="websiteheader" align="center" width="100%" cellspacing="0" cellpadding="0" style="background:#FFFFFF;border-top-right-radius: 4px;-moz-border-top-right-radius:4px;border-top-left-radius: 4px;-moz-border-top-left-radius:4px;">
<tr>
<td style="width:60px;"></td>
<td align="" valign="middle" style="width:200px;padding-left:30px;height:60px;">
<a href="<?php echo $homeurl; ?>">
 <!--<img src="/images/logo.png">--><a style="font-family: 'Montserrat Alternates', sans-serif;font-size:20pt;color:#404040;">shufflebuy</a>
</a>
</td>
<!--<a style="color:#404040;font-weight:bold;font-family:'Roboto',sans-serif;font-size:26px;padding-top:10px;padding-bottom:10px;color:#317AF1;" href="<?php echo $homeurl; ?>">shufflebuy

</a>-->
</td>
<td style="width:400px;">
<div style="">
<?php   if(isset($_COOKIE[$ck_session])) { ?>
     <td align="middle" valign="middle" style="width:30px;">
	<?php  include("accounts/dropdownindex.php"); ?>
	</td>	
	<?php } ?>
       <td align="middle" valign="middle" style="width:20px;">
	   	<?php   if(isset($_COOKIE[$ck_session])) { ?>
       <img  class="checkinbox" id="<?php echo $id; ?>"  style="margin-right:0px;height:20px;width:auto;max-height:20px;" src="images/nomsg.png" valign="middle">
       <?php } ?>
	   </td>	
	   <?php if(!isset($_COOKIE[$ck_session])) { ?>
	   <td align="left" valign="middle" style="width:45px;">
	   <a href="<?php echo $script_url ?>/signup.html" style="font-family:'Roboto',san-serif;color:#404040;font-style:bold;font-size:11pt;">sign up</a>
	   </td>
	   <?php } ?>
       <td align="left" valign="middle" style="width:45px;">

			<?php   include("accounts/profileddown.php"); ?>

	 </td>
	 </div>
	 <td style="width:60px;"></td>
</td>
</tr>
</table>
<?php if ($xview == "main") { ?>
<table width="100%" align="center" cellspacing="0" cellpadding="0" bgcolor="azure" style="border-left:1px solid lightgray;border-right:1px solid lightgray;border-bottom:1px solid lightgray;">
<tr>

</tr>

</td>
</tr>
</table>
<?php } ?>


<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="white" style="border-left:1px solid lightgray;border-right:1px solid lightgray;">
<tr>


<table width="100%" cellspacing="0" cellpadding="0" bgcolor="white">
		<tr>

		<td  width="100%" id="content">

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
			case "delete_conversation"	: $page = "accounts/messaging/core/pages/inbox.page.inc.php";	break;
			case "new_conversation"	: $page = "accounts/messaging/core/pages/new_conversation.page.inc.php";	break;
			case "view_conversation"	: $page = "accounts/messaging/core/pages/view_conversation.page.inc.php";	break;
			case "messagebox2"	: $page = "accounts/messaging/index.php";	break;			
			case "messagebox"	: $page = "accounts/messaging/core/pages/inbox.page.inc.php";	break;
			case "commentstotal"    : $page = "commentstotal.php"; break;
			
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
