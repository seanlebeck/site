<?php

require_once("initvars.inc.php");
require_once("config.inc.php");
require_once($acc_dir . "/" . $inc_path. "/post_security.php");

$msg = "";
$err = "";

if (!$_GET['adid'])
{
	header("Location: $script_url/?view=main&cityid=$xcityid&lang=$xlang");
	exit;
}


$adtable = ($_GET['view'] == "showevent") ? $t_events : $t_ads;
$adid_prefix = (($xview == "events") ? "E" : "A");
$full_adid = ($adid_prefix . $xadid);
$reported = explode(";", $_COOKIE["reported"]);
$is_reported = in_array($full_adid, $reported);

// Make up search query
$qsA = $_GET; $qs = "";
unset($qsA['do'], $qsA['reported'], $qsA['mailed'], $qsA['mailerr'], $qsA['msg'], $qsA['err']);
foreach ($qsA as $k=>$v) $qs .= "$k=$v&";

if ($_GET['do'] == "reportabuse")
{
    
    if (!$_GET['confirm']) {
        ob_clean();
        header("Status: 410 Gone");
        exit;
    }
    

    if (!$is_reported) {

		
    	$sql = "UPDATE $adtable 
    			SET abused = abused + 1 
    			WHERE adid = $_GET[adid] 
    				AND abused < " . ($spam_indicator - 1);
    	
    	mysql_query($sql) or die($sql);
    
    	if(mysql_affected_rows())
    	{
    		echo "<div class=\"msg\">$lang[MESSAGE_ABUSE_REPORT]</div>";
    		
    		if($max_abuse_reports)
    		{
    			
    			$sql = "UPDATE $adtable 
    					SET enabled = '0' 
    					WHERE adid = $_GET[adid]
    						AND abused >= $max_abuse_reports";
    			mysql_query($sql);
    			
    		}
    
    		header("Location: $script_url/?{$qs}reported=y");
    		exit;
    	}
    }

	unset($_GET['do']);
}


if ($xview == "showevent")
{
	// Get the event
	$sql = "SELECT a.*, UNIX_TIMESTAMP(a.timestamp) AS timestamp, UNIX_TIMESTAMP(a.createdon) AS createdon, UNIX_TIMESTAMP(a.expireson) AS expireson, UNIX_TIMESTAMP(feat.featuredtill) AS featuredtill,
			UNIX_TIMESTAMP(a.starton) AS starton, UNIX_TIMESTAMP(a.endon) AS endon
		FROM $t_events a
			LEFT OUTER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'E'
		WHERE a.adid = $xadid
			AND $visibility_condn_admin";
	$ad = mysql_fetch_array(mysql_query($sql));

	$isevent = 1;

	
	$thisurl = buildURL($xview, array($xcityid, $xdate, $xadid, $ad['adtitle']));
	

}
else
{
	// List of extra fields
	$xfieldsql = "";
	if(count($xsubcatfields)) 
	{
		for($i=1; $i<=$xfields_count; $i++)	$xfieldsql .= ", axf.f$i";
	}

	// Get the ad
	$sql = "SELECT a.*, ct.cityname as cityname, UNIX_TIMESTAMP(a.timestamp) AS timestamp, UNIX_TIMESTAMP(a.createdon) AS createdon, UNIX_TIMESTAMP(a.expireson) AS expireson, UNIX_TIMESTAMP(feat.featuredtill) AS featuredtill $xfieldsql
			FROM $t_ads a
				INNER JOIN $t_subcats scat ON scat.subcatid = a.subcatid
                INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				LEFT OUTER JOIN $t_adxfields axf ON a.adid = axf.adid
				LEFT OUTER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'A'
			WHERE a.adid = $xadid
				AND $visibility_condn_admin";
	$ad = mysql_fetch_array(mysql_query($sql));

	$isevent = 0;
	
	$thisurl = buildURL($xview, array($xcityid, $xcatid, $xcatname, $xsubcatid, $xsubcatname, 
	    $xadid, $ad['adtitle']));
	

}


if (!$ad) 
{
    
	header("Location: $script_url/index.php?view=post404&cityid=$xcityid&lang=$xlang");
    
	exit;
}


if ($_POST['email'] && $_POST['mail'] && $ad['showemail'] == EMAIL_USEFORM)
{
	$err = "";
	
	 // BEGIN Vivaru contact limit addon
	 $res_query = "SELECT sender_ip FROM $t_contact_temp
				   WHERE time_sent > '".(time()-$ad_contact_limit)."'
				   AND sender_ip = '".$_SERVER['REMOTE_ADDR']."'";
	
	 $res_count = "SELECT COUNT(sender_ip) AS max_count FROM $t_contact_temp
				   WHERE sender_ip = '".$_SERVER['REMOTE_ADDR']."'";
	
	 $res = mysql_query($res_query);
	 $resa = mysql_query($res_count);
	 $count = mysql_fetch_array($resa);
	
	 //echo $res_query . '<br>' . $res_count . '<br>'; exit; // testing
	 //$go_back_ad = '<br><br><a href="javascript:history.go(-1)">'.$lang['ERROR_CONTACT_GO_BACK'].'</a>';
	
		
		
	 if(mysql_num_rows($res) > 0)
	 {
		  $mailerr = '<font color="red">' .$lang['ERROR_CONTACT_FORM_FLOOD']. '</font><br><br>';
		  //echo $mailerr;
		  // Added for newer versions
		  $err .= $lang['ERROR_CONTACT_FORM_FLOOD'];
	 }
	 elseif($count['max_count'] >= $ad_contact_max_count )
	 {
		  $mailerr = '<font color="red">' .$lang['ERROR_CONTACT_FORM_MAX']. '</font><br><br>';
		  //echo $mailerr;
		  // Added for newer versions
		  $err .= $lang['ERROR_CONTACT_FORM_MAX'];
	 }
	// END Vivaru contact limit addon

	if (!ValidateEmail($_POST['email'])) 
	{
		$err .= $lang['ERROR_INVALID_EMAIL'] . "<br>";
	}

	if (preg_match("/[\\000-\\037]/", $_POST['email']))
	{
		handle_security_attack("@");
	}
	else if (!$err)
	{
		$thismail_header = file_get_contents("mailtemplates/contact_header.txt");
		$thismail_header = str_replace("{@SITENAME}", $site_name, $thismail_header);
		$thismail_header = str_replace("{@ADTITLE}", $ad['adtitle'], $thismail_header);
		$thismail_header = str_replace("{@ADURL}", "{$script_url}/{$thisurl}", $thismail_header);
		$thismail_header = str_replace("{@FROM}", $_POST['email'], $thismail_header);

		$thismail_footer = file_get_contents("mailtemplates/contact_footer.txt");
		$thismail_footer = str_replace("{@SITENAME}", $site_name, $thismail_footer);
		$thismail_footer = str_replace("{@ADTITLE}", $ad['adtitle'], $thismail_footer);
		$thismail_footer = str_replace("{@ADURL}", "{$script_url}/{$thisurl}", $thismail_footer);
		$thismail_footer = str_replace("{@FROM}", $_POST['email'], $thismail_footer);

		$msg = $thismail_header . "\n" .
				stripslashes($_POST['mail']) . "\n" .
				$thismail_footer;		
		
		
        $xtraheaders = array("Sender: " . $site_email);

		$mailerr = sendMail($ad['email'], $lang['MAILSUBJECT_CONTACT_FORM'], $msg, 
			$_POST['email'], $langx['charset'], "attach", $xtraheaders);
	
		
       $status_sent = 0;
        if ($mailerr)
		{
			$mailresult = "n";
			if ($mailerr == "FAILED") $mailerr = "";
			$status_sent = 0;
		}
		else 
		{
						$status_sent = 1;
            $mailresult = "y";
    }
        
    /*Contact form save mod start*/
    $ip_remote = $_SERVER['REMOTE_ADDR'];
    $msg_sent = stripslashes($msg);
    $msg_sent = @mysql_real_escape_string(htmlspecialchars($msg_sent, ENT_QUOTES));
		$sql = "INSERT INTO $t_contact_form_save 
						SET from_email = '$_POST[email]',
								to_email = '$ad[email]',
						 		message_email = '$msg_sent',
						 		ip_from = '$ip_remote',
						 		status_sent = '$status_sent',
						 		sent_date = NOW()";
		@mysql_query($sql);// or die($sql.mysql_error());
    /*Contact form save mod end*/

		header("Location: $script_url/?$qs&mailed=$mailresult&mailerr=$mailerr");
		exit;
	}

}


$sql = "SELECT *
		FROM $t_adpics p
		WHERE p.adid = $xadid
			AND isevent = '$isevent'
		ORDER BY p.picid";
$pres = mysql_query($sql);

?>


<?php

if(!$_POST['mail'])
{
	if($_GET['mailed'] == "y")		
	{ 
		$msg .= $lang['MESSAGE_MAIL_SENT']."<br>"; 
		$contact_sql = "INSERT INTO $t_contact_temp (sender_ip, time_sent) 
					    VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."')";
		mysql_query($contact_sql);
	}
	elseif ($_GET['mailed'] == "n")	{ $err .= $lang['ERROR_MAIL_NOT_SENT']."<br>".$_GET['mailerr']."<br>"; }

	if($_GET['reported'] == "y")	{ $msg .= $lang['MESSAGE_ABUSE_REPORT']."<br>"; }
}

if($_GET['msg'])				{ $msg .= nl2br(htmlentities($_GET['msg']))."<br>"; }
if($_GET['err'])				{ $err .= nl2br(htmlentities($_GET['err']))."<br>"; }

?>

<?php
if($err) echo "<center><div class=\"err\" style=\"border:1px solid crimson;color:crimson;background:lightpink;font-size:14px;font-weight:bold;padding:10px;margin:10px;\">$err</div></center>";
if($msg) echo "<center><div class=\"msg\" style=\"border:1px solid green;color:green;background:lightgreen;font-size:14px;font-weight:bold;padding:10px;margin:10px;\">$msg</div></center>";
?>

<table class="postheader" width="100%" cellpadding="0" cellspacing="0"> 
<tr>
<td style="border-bottom:1px solid lightblue;padding:3px;font-size:11px;font-weight:normal;color:crimson;">

<?php echo $lang['POST_ID']; ?> <?php echo ($xview=="showevent"?"E":"A"); ?><?php echo $ad['adid']; ?> | Posted: <?php echo QuickDate($ad['createdon']); ?> | <?php
$hits = $ad['hits'];
$already_hit = explode(";", $_COOKIE["hits"]);
if (!in_array($full_adid, $already_hit)) {
    $sql = "update $adtable set hits = hits + 1, timestamp = timestamp where adid = $xadid";
    mysql_query($sql);
    $already_hit[] = $full_adid;
    setcookie("hits", implode(";", $already_hit), 0, "/");
    $hits++;
}
?>

<?php echo $lang['HITS1']; ?> <?php echo $hits; ?> 

</td>

<td align="right" valign="middle" style="border-bottom:1px solid lightblue;padding:3px;">
<a href="?view=mailad&cityid=<?php echo $xcityid; ?>&adid=<?php echo $xadid; ?>&adtype=<?php echo $xadtype; ?><?php if($xdate) echo "&date={$xdate}"; ?>"><?php echo $lang['EMAIL_THIS_AD_LINK']; ?></a>&nbsp;&nbsp;
</td>

</tr>
</table>

<?php 


if($ad['area']) $loc = $ad['area'];
if($xcityid < 0) $loc .= ($loc ? ", " : "") . $ad['cityname'];

?>

<table class="postheader" width="100%">
<tr>
<td align="left" valign="top" width="220" STYLE="border-right: 1px solid lightblue;padding-right: 5px;">


<center>
<div style="padding-top:5px;padding:bottom:5px;">

<?php


if (@mysql_num_rows($pres))




{
	$i = 0;
?>

<?php
	while ($row = mysql_fetch_array($pres))
	{
		$i++;

		$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$row[picfile]}", $images_max_width, $images_max_height);

$ptrimm = ereg_replace("[^A-Za-z0-9]", "", $row[picfile]);

?>	


<span class="triggers">
<img src="<?php echo "{$datadir[adpics]}/{$row[picfile]}"; ?>"  width="100" height="66" style="border:1px solid gray;cursor:pointer;" rel="#<?php echo $ptrimm; ?>">
</span>


<div class="simple_overlay" id="<?php echo $ptrimm; ?>">
<img src="<?php echo "{$datadir[adpics]}/{$row[picfile]}"; ?>"/>
</div>

<?php } ?>

<?php

	$imgcnt = $i;

}
?>

</div>
</center>


<script>
// What is $(document).ready ? See: http://flowplayer.org/tools/documentation/basics.html#document_ready
$(document).ready(function() {



$("img[rel]").overlay({

mask: {
        color: '#ebecff',
        loadSpeed: 200,
        opacity: 0.9
      },
      closeOnClick: true

});

});
</script>




<center>
<center>
<div style="text-align:left;border:5px solid rgba(0, 128, 255, 0.498);-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;width:200px;height:200px;background:azure;padding:5px;">
<br><br><br><br>
This banner is located and can be replaced in the file showad.php around line 384. 
</div>
</center>
</center>






<br>



<?php if($show_cats_in_sidebar && !($xview == "main" || $xpostmode) && !$show_sidebar_always) { ?>

<div style="border-top:1px solid lightblue;padding:5px;">
<b><?php echo $lang['CATEGORIES']; ?> &raquo;</b><br><img src="images/spacer.gif" height="5"><br>
	<?php include("cats.inc.php"); ?>
</div>
<?php } ?>




</td>


<td align="left" valign="top">

<div class="posttitle"> 



<?php if ($xview == "showevent") { ?>

Event Dates: <?php echo date("d", $ad['starton'])." ".$langx['months_short'][date("n", $ad['starton'])-1] . ", " . date("y", $ad['starton']); ?>
	<?php if($ad['starton'] != $ad['endon']) echo " - " . date("d", $ad['endon']) . " " . $langx['months_short'][date("n", $ad['endon'])-1] . ", " . date("y", $ad['endon']); ?>
	

<?php
}
?>

<div style="border:1px solid #FFAE4D;background:LightYellow;margin:5px;-moz-border-radius:4px;border-radius: 4px;">


<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="left" valign="middle" style="padding:10px;">
<?php if(count($xsubcatfields)) { foreach ($xsubcatfields as $fldnum=>$fld) { if(($fld['TYPE'] == "N" && $ad["f$fldnum"] > 0) || ($fld['TYPE'] != "N" && $ad["f$fldnum"])) { $actualfields++; ?>
<div style="font-family:verdana;font-weight:bold;">
<img src='images/identity.png' align='absmiddle'>&nbsp;<?php echo $fld['NAME']; ?>: <?php echo $ad["f$fldnum"]; ?>
<?php }}} ?>
<br>
<img src='images/identity.png' align='absmiddle'>&nbsp;<?php echo $lang['REPLY_TO']; ?>: 
<?php if ($ad['showemail'] == EMAIL_SHOW) { ?>
<a href="mailto:<?php echo $ad['email']; ?>"><?php echo $ad['email']; ?></a>

<?php } elseif ($ad['showemail'] == EMAIL_USEFORM) { ?>
<a style="font-weight:bold;text-decoration:underline;cursor:pointer;" class="modalInput" rel="#prompt"><?php echo $lang['USE_CONTACT_FORM']; ?></a>

<?php } else { ?>
	<i><?php echo $lang['EMAIL_NOT_SHOWN']; ?></i>

<?php } ?>
</div>

</td>

<?php
if ($loc) {
$formname = "form".$id;
?>

<td align="right" valign="middle">

<script src="http://maps.google.com/maps?hl=en&file=api&v=2.x&key=" type="text/javascript"></script>
<script type="text/javascript">
var coord1 = "";
var coord2 = "";

var map = null;
var geocoder = null;

function load() {
      if (GBrowserIsCompatible()) {
       map = new GMap2(document.getElementById("map"));
       var center = new GLatLng(0, 0);
       map.setCenter(center, 1);
       geocoder = new GClientGeocoder();
      }
      if (geocoder) {
        geocoder.getLatLng(
          '<?php echo $loc; ?>',
          function(point) {
            if (!point) {
              alert("<?php echo $loc; ?>" + " Address not found...");
              var fcenter = new GLatLng(0,0);
              map.setCenter(fcenter, 1);
              var marker = new GMarker(fcenter, {draggable: true});
              map.addOverlay(marker);
              GEvent.addListener(marker, "dragend", function() {
		var point =marker.getPoint();
		map.panTo(point);
		document.getElementById("lat").innerHTML = point.lat().toFixed(5);
		document.getElementById("lng").innerHTML = point.lng().toFixed(5);
	       coord1 = point.lat().toFixed(5);
	       coord2 = point.lng().toFixed(5);
              });
            } else {
              map.setCenter(point, 13);
              var marker = new GMarker(point, {draggable: true});
              map.addOverlay(marker);
              GEvent.addListener(marker, "dragend", function() {
		var point =marker.getPoint();
		map.panTo(point);
		
	       coord1 = point.lat().toFixed(5);
	       coord2 = point.lng().toFixed(5);
              });
              GEvent.addListener(marker, "click", function() {
		var point =marker.getPoint();
		map.panTo(point);
		
	       coord1 = point.lat().toFixed(5);
	       coord2 = point.lng().toFixed(5);
              });
	      GEvent.trigger(marker, "click");
            }
          }
        );
      }

    }

</script>
    

<div id="map" style="width: 250px; height: 110px;border:1px solid LightYellow;background:LightYellow;-moz-border-radius:4px;border-radius: 4px;"></div>


</td>

<?php
}
?>


</tr>
</table>


</div>


</div>


<table cellpadding="0" cellspacing="0">
<tr>

<?php if ($xview == "showad") { ?>
<td align="left" valign="middle" style="padding-left:5px;">
<div style="color:white;background:seagreen;padding:3px;padding-left:5px;padding-right:5px;border:1px solid lightblue;font-size:10px;border-radius:3px;-moz-border-radius:3px;">
<?php echo $ad['cityname']; ?>
</div>
</td>
<?php } ?>

<td align="left" valign="middle" style="padding-left:5px;">

<?php if($xsubcathasprice) { ?> <?php if(($xsubcathasprice && $ad['price'] != 0.00)) { ?><div style="border:1px solid lightblue;background:Gainsboro ;font-size:10px;color:darkslategray;padding:3px;border-radius:3px;-moz-border-radius:3px;"><?php echo  $currency . $ad['price']; ?></div><?php } else { echo '&nbsp;'; } ?><?php } ?>

</td>

<td align="left" valign="middle" style="padding-left:5px;">

<?php 
if( $ad['urgent'] && $ad['urgent_paid'] ) echo "<div style='border:1px solid lightblue;background:orangered;font-size:10px;color:white;padding:3px;border-radius:3px;-moz-border-radius:3px;'>URGENT</div>";
?>

</td>
</tr>
</table>


<table class="post" width="100%"><tr><td> 

<div class="posttitle">
<h2 style="padding:2px;color:midnightblue;font-size:16px;"><?php echo $ad['adtitle']; ?></h2>
</div>

<div class="textonpost">

<div class="wrap">
<?php echo generateHtml($ad['addesc'], $ad['createdon']); ?>
</div>
</div>

<div style="font-size:12px;color:darkslategray;padding:10px;">



<div style="float:left;">
<?php 
if($ad['othercontactok']) echo "<p class=\"disclosure_yes\">$lang[COMMERCIAL_CONTACT_OK]</p>";
else echo "<p class=\"disclosure_no\">$lang[COMMERCIAL_CONTACT_NOT_OK]</p>";
?>
</div>

<?php if($star_rating) { ?>
<div style="float:right;" id="rate_<?php echo ($xview=="showevent"?"E":"A"); ?><?php echo $ad['adid']; ?>"></div>
<?php } ?>



<?php
if(($xsubcathasprice && $ad['price']) || count($xsubcatfields))
{
    
    $actualfields = $xsubcathasprice ? 1 : 0;
?>

</div>

<?php
    
}
?>


<?php 
$adurl = "$script_url/" . buildURL("showad", array($xcityid, $xcatid, $row['catname'], 
            $xsubcatid, $row['subcatname'], $xadid, $adtitle));		
		
?>

</td></tr>

<?php if($ad_comments == 'TRUE') { ?>
<!-- COMMENTS PLACEHOLDER START  -->
<tr><td colspan="2">
<fieldset style="border:1px solid lightblue;margin-top:0px;padding:5px;">
<legend style="color:black;font-size:12px;">&nbsp;Comments&nbsp;</legend>
<?php
    $object_id = $ad['adid']; //identify the object which is being commented
    include('comments/php/loadComments.php'); //load the comments and display    
?>

</fieldset>
</td></tr>
<!-- COMMENTS PLACEHOLDER END  -->
<?php } ?>

<tr><td colspan="2">
<?php
if ( $related_enabled == 1 )
{
	include_once("related_ads.inc.php");
}
?>
</td>
</tr>


</table>

</td>
</tr>
</table>

  <?php if ($ad['showemail'] == EMAIL_USEFORM) { 
/*$qs = ""; $qsA = $_GET; unset($qsA['syndicate']);
foreach ($qsA as $k=>$v) $qs .= "$k=$v&";*/
?>

<style>
 .contact_form_modal {
    background-color:#fff;
	width:500px;
	padding:10px;
	padding-left:0px;
    display:none;
    text-align:left;
    border:2px solid #333;

    opacity:0.8;
	border:10px solid white;
	border:10px solid rgba(1, 1, 1, 0.498);
	-moz-border-radius:8px;
	-webkit-border-radius:8px;
	border-radius:8px;
  }

  .contact_form_modal h2 {
    margin:0px;
    padding:5px;
	padding-left:0px;
    font-size:14px;
  }
</style>

<!-- user input dialog -->
<div class="contact_form_modal" id="prompt">
<p>&nbsp;&nbsp;<?php echo $lang['CONTACT_USER']; ?>: <font color="blue"><?php echo $ad['adtitle']; ?></font></p>
  
<table width="100%" style="border:1px solid lightblue;background:Azure;margin:5px;padding:5px;">
<tr>

<td align="left">

	<form id="contactuserform" action="<?php echo "$script_url/?$qs"; ?>" method="post" enctype="multipart/form-data">
	<table>
	<tr>
		<th colspan="2">
		
	
		
		<font color="green" size="1">Please be sure to enter your contact details so the advertiser can contact you back</font>
</th>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td><?php echo $lang['YOUR_EMAIL']; ?>:</td>
		<td>
		<div id='contactuserform_email_errorloc' class="error_strings" style="margin:2px;color:crimson;font-size:11px;font-weight:bold;"></div>
		<input type="text" size="55" id="email" name="email">
		</td>
	</tr>
	<tr>
		<td valign="top"><?php echo $lang['YOUR_MESSAGE']; ?>:</td>
		<td>
		<div id='contactuserform_mail_errorloc' class="error_strings" style="margin:2px;color:crimson;font-size:11px;font-weight:bold;"></div>
		<textarea cols="60" rows="8" id="mail" name="mail"></textarea>
		</td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		<td><br>
		<button type="submit"><?php echo $lang['BUTTON_SEND_MAIL']; ?></button>
		&nbsp;&nbsp;
		<button type="button" class="close"> Cancel </button>
		</td>
	</tr>
	</table>
	</form>
	
</td>

</tr>
</table>

</div>

<script>
$(document).ready(function() {
    var triggers = $(".modalInput").overlay({
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


<script language="JavaScript" type="text/javascript"
    xml:space="preserve">//<![CDATA[
//You should create the validator only after the definition of the HTML form
  var frmvalidator  = new Validator("contactuserform");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

 frmvalidator.addValidation("email","maxlen=30","Maximul length for email field is 30 characters!");
 frmvalidator.addValidation("email","req","Please enter your email address!");
 frmvalidator.addValidation("email","email","Please enter a valid email address");
 
  frmvalidator.addValidation("mail","req","Please enter your message!");
  frmvalidator.addValidation("mail","maxlen=500","Maximum length for message field is 500 characters!");
 
//]]></script>


<?php } ?>




