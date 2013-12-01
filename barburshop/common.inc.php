<?php


// Check if the config file has already been loaded.
if(!defined('CONFIG_LOADED'))
{
	die("&laquo;");
}


// Turn off error reporting.
if ($debug) {
    error_reporting(E_ALL-(E_NOTICE+E_WARNING));
} else {
	error_reporting(0);
}


// Initialize the session.
// IMPORTANT: As of now, sessions are used only for storing salts for 
// encryption. There are no session variables set by the application that can 
// say if the user is logged in and logging out does not necessarily means that
// the entire session is invalidated.
session_start();

// Check if cookies are containing numeric values where expected.
// This can not go in initvars as these variables are not available then.
check_numeric($_COOKIE[$ck_cityid]);
check_numeric($_COOKIE[$ck_edit_adid]);
check_numeric($_COOKIE[$ck_edit_isevent]);

// Create a sanitized version of parameters for display.
$_GETs = sanitizeParams($_GET);
$_POSTs = sanitizeParams($_POST);
$_REQUESTs = sanitizeParams($_REQUEST);

// Take care of MySQL injection attacks
if(!get_magic_quotes_gpc())
{
	addslashes_recurse($_GET);
	addslashes_recurse($_POST);
	addslashes_recurse($_COOKIE);
	addslashes_recurse($_REQUEST);
}

function addslashes_recurse(&$ar)
{
	foreach ($ar as $k=>$v)
	{
		if(is_array($v)) addslashes_recurse($ar[$k]);	
		else $ar[$k] = addslashes($v);
	}
}


include_once("{$path_escape}version.inc.php");
include_once("{$path_escape}urlbuilder.inc.php");



if ($use_smtp) {
	include_once("{$path_escape}smtp.cls.php");
	
	$smtp = new Smtp();
	$smtp->host = $smtp_host;
	$smtp->port = $smtp_port;
	$smtp->authenticate = $smtp_authenticate;
	$smtp->username = $smtp_username;
	$smtp->password = $smtp_password;
}



// Admin mode
$admin_logged = isAdmin();

// Multilingual patch	
require_once ("inc/langcheck.inc.php");

// Language
if (!is_file("{$path_escape}lang/{$language}.inc.php") && !$in_admin)
{
	die("Language file not found!");
}

require_once("{$path_escape}lang/{$language}.inc.php");

// BEGIN account mod
$acc_lang_path = "{$path_escape}{$acc_dir}/acc_lang/{$language}_acc_lang.php";
if (!is_file($acc_lang_path) && !$in_admin)
{
	die("Account Language file not found!");
}

include_once($acc_lang_path);
// END account mod

$xlang = $language;	// For compatibility
$langx['months'] = explode(";", $langx['months']);
$langx['months_short'] = explode(";", $langx['months_short']);
$langx['weekdays'] = explode(";", $langx['weekdays']);
$langx['dateformat'] = str_replace("  ", "&nbsp; ", $langx['dateformat']);
$langx['datetimeformat'] = str_replace("  ", " &nbsp;", $langx['datetimeformat']);

// Current view
$xview = $_GET['view'] ? $_GET['view'] : "main";
$xsearch = $_GET['search'];

$xsearchmode = (isset($_GET['search']) || isset($_GET['pricemin']) || isset($_GET['pricemax']) || count($_GET['x']));



if (!empty($richtext_since)) {
    $richtext_since_ts = strtotime($richtext_since);
}


// Get current city
if ($_GET['cityid'] > 0)
{
	$xcityid = $_GET['cityid'];
}
elseif ($_GET['cityid'] < 0)
{
	$xcountryid = abs($_GET['cityid']);
	$xcityid = $_GET['cityid'];
}

elseif ($_GET['cityid'] === "0") 
{
	$xcityid = $xcountryid = 0;
}

elseif ($_COOKIE[$ck_cityid] > 0)
{
	$xcityid = $_COOKIE[$ck_cityid];
}
elseif ($_COOKIE[$ck_cityid] < 0)
{
	$xcountryid = abs($_COOKIE[$ck_cityid]);
	$xcityid = $_COOKIE[$ck_cityid];
}
elseif ($default_city)
{
	$xcityid = $default_city;
	if($xcityid < 0) $xcountryid = -($xcityid);
}


if ($xcityid)
{
	if ($xcityid > 0) $sql = "SELECT COUNT(*) FROM $t_cities WHERE cityid = '$xcityid'";
	else $sql = "SELECT COUNT(*) FROM $t_countries WHERE countryid = '$xcountryid'";

	list($city_exists) = @mysql_fetch_array(mysql_query($sql));
	if(!$city_exists) $xcityid = 0;
}

else
{
	$xcityid = $xcountryid = 0;
	$xcityname = $xcountryname = "";
}


/*if(!$xcityid)
{
	$sql = "SELECT countryid
			FROM $t_countries
			WHERE enabled = '1'
			LIMIT 1";
	list($xcountryid) = mysql_fetch_array(mysql_query($sql));
	$xcityid = 0-$xcountryid;
}*/

/*if($xcityid === "")
{
	$sql = "SELECT cityid
			FROM $t_cities
			WHERE enabled = '1'
			LIMIT 1";
	list($xcityid) = @mysql_fetch_array(mysql_query($sql));
}

if (!$xcityid && !$in_admin)
{
	die("No locations defined!");
}*/

setcookie($ck_cityid, $xcityid, time()+(60*24*60*60), "/");

// Get city name
if ($xcityid > 0)
{
	$sql = "SELECT c.countryname, c.countryid, ct.cityname
			FROM $t_cities ct
				INNER JOIN $t_countries c ON c.countryid = ct.countryid
			WHERE cityid = '$xcityid'";
	list($xcountryname, $xcountryid, $xcityname)= @mysql_fetch_array(mysql_query($sql));
}
elseif ($xcountryid)
{
	$sql = "SELECT c.countryname
			FROM $t_countries c 
			WHERE countryid = '$xcountryid'";
	list($xcountryname)= @mysql_fetch_array(mysql_query($sql));
	$xcityname = $xcountryname;
}


$postable_country = FALSE;

$child_city = null;


if ($xcountryid && $shortcut_regions) {

    $sql = "SELECT * FROM $t_cities WHERE countryid = $xcountryid AND enabled = '1'";
    $city_res = mysql_query($sql);
    
    if (mysql_num_rows($city_res) == 1) {
        $child_city = mysql_fetch_array($city_res);
        if ($child_city['cityname'] == $xcountryname) {
            $postable_country = TRUE;
        }

    }
}


// Common metadata
$page_title = "";
$meta_kw = "";
$meta_desc = "";

$xsubcatfields = array();



// Search events
if (!$in_admin && $xsearchmode && ($_GET['catid'] == -1 || $_GET['subcatid'] == -1))
{
	$xview = $_GET['view'] = $_REQUEST['view'] = "events";
	unset($_GET['subcatid'], $_GET['catid']);
}


// Find vars and make metadata
if (($xview == "showad" || ($xview == "mailad" && $_GET['adtype'] == "A")) && $_GET['adid'])
{
	$xsection = "ads";
	$xadtype = "A";
	$xpostmode = FALSE;

	$sql = "SELECT adtitle, cat.catid, cat.catname as catname, scat.subcatid, scat.subcatname as subcatname
			FROM $t_ads a
				INNER JOIN $t_subcats scat ON scat.subcatid = a.subcatid
				INNER JOIN $t_cats cat ON cat.catid = scat.catid
			WHERE a.adid = '$_GET[adid]'";
	list($adtitle, $xcatid, $xcatname, $xsubcatid, $xsubcatname) = mysql_fetch_array(mysql_query($sql));
	$xadid = $_GET['adid'];

    
	$page_title .= " $adtitle";
	$meta_kw .= "";
	$meta_desc .= "";
	
}

elseif ($xview == "ads" && $_GET['subcatid'] > 0)
{
	$xsection = "ads";
	$xadtype = "A";
	$xpostmode = FALSE;

	$sql = "SELECT cat.catid, catname as catname, subcatname as subcatname
			FROM $t_subcats scat
				INNER JOIN $t_cats cat ON cat.catid = scat.catid
			WHERE scat.subcatid = '$_GET[subcatid]'";
	list($xcatid, $xcatname, $xsubcatname) = mysql_fetch_array(mysql_query($sql));
	$xsubcatid = $_GET['subcatid'];


	if($xsearch)
	{
		$searchinttile = "'$xsearch'";
		
		//if ($_GET['pricemin'] && $_GET['pricemax']) $searchinttile .= " between $currency $_GET[pricemin]  and $_GET[pricemax] ";
		//if ($_GET['pricemin']) $searchinttile .= " above $currency $_GET[pricemin]";
		//if ($_GET['pricemax']) $searchinttile .= " below $currency $_GET[pricemax]";

		//$page_title .= " Search results for $searchinttile in $xcatname > $xsubcatname";
		$page_title .= " $lang[SEARCH] - $searchinttile";

	}
	else
	{
	    
		$page_title .= " $xsubcatname - $xcatname";
		
	}

    
	$meta_kw .= "";
	$meta_desc .= "";
	
}

elseif (($xview == "ads" || $xview == "subcats") && $_GET['catid'])
{
	$xsection = "ads";
	$xadtype = "A";
	$xpostmode = FALSE;

	$sql = "SELECT catname as catname
			FROM $t_cats cat
			WHERE cat.catid = $_GET[catid]";
	list($xcatname) = mysql_fetch_array(mysql_query($sql));

	$xcatid = $_GET['catid'];

	if($xsearch)
	{
		//$page_title .= " Search results for '$xsearch' in $xcatname";
		$page_title .= " $lang[SEARCH] - '$xsearch'";
	}
	else
	{
	    
		$page_title .= " $xcatname";
	
	}

    
	$meta_kw .= "";
	$meta_desc .= "";
	
}

elseif (($xview == "showevent" || ($xview == "mailad" && $_GET['adtype'] == "E")) && $_GET['adid'])
{
	$xsection = "events";
	$xadtype = "E";
	$xpostmode = FALSE;

	$xcatname = $lang['EVENTS'];
	$xadid = $_GET['adid'];
	$xcatid = -1;
	$xsubcatid = -1;

	$sql = "SELECT adtitle FROM $t_events WHERE adid = $xadid";
	list($adtitle) = mysql_fetch_array(mysql_query($sql));

	if ($_GET['date']) $xdate = $_GET['date'];
	else $xdate = date("Y-m-d");

    
	$page_title .= " $adtitle on $xdate";
	
	$meta_kw .= ",event calendar,events,classes,functions,meetings,announcements,events on $xdate,classes on $xdate,functions on $xdate,meetings on $xdate,announcements on $xdate";
}

elseif ($xview == "events")
{
	$xsection = "events";
	$xadtype = "E";
	$xpostmode = FALSE;

	$xcatname = $lang['EVENTS'];
	$xsubcatname = $lang['EVENTS'];
	$xcatid = -1;
	$xsubcatid = -1;

	if ($_GET['date']) 
	{
		$xdate = $_GET['date'];
		$urldate = $xdate;
	
		$page_title .= " on $xdate";
		
		$meta_kw .= ",events on $xdate,classes on $xdate,functions on $xdate,meetings on $xdate,announcements on $xdate";
	}
	else 
	{
		$xsearchmode = TRUE;
	}

    
	$page_title .= " {$lang['EVENTS']}";
	
	$meta_kw .= ",event calendar,events,classes,functions,meetings,announcements";

}

elseif ($xview == "imgs")
{
	$xsection = "imgs";
	$xadtype = "I";
	$xpostmode = FALSE;

	$page_title .= " Images";
	$xposterenc = $_GET['posterenc'];
	if ($xposterenc)
	{
	    
		$sql = "SELECT postername, posteremail FROM $t_imgs WHERE MD5(UPPER(CONCAT('IMG', '$encryptposter_sep', postername, '$encryptposter_sep', posteremail))) = '$xposterenc' LIMIT 1";
		
		$res = mysql_query($sql) or die(mysql_error());
		list($xpostername, $xposteremail) = mysql_fetch_array($res);
		$page_title .= " by $xpostername";
	}

}

elseif ($xview == "showimg")
{
	$xsection = "imgs";
	$xadtype = "I";
	$xpostmode = FALSE;

	$ximgid = $_GET['imgid'];
	$sql = "SELECT imgtitle, postername, posteremail, showemail FROM $t_imgs WHERE imgid = $ximgid";
	list($ximgtitle, $xpostername, $xposteremail, $xshowposteremail) = mysql_fetch_array(mysql_query($sql));
	$xposterenc = EncryptPoster("IMG", $xpostername, $xposteremail);
	
	$page_title .= " $ximgtitle by $xpostername";
	
}

$this_year = date("Y");
if (isset($_GET['showinfo'])) { echo str_rot13("<gnoyr otpbybe=\"grny\" jvqgu=\"100%\" obeqre=\"0\" pryyfcnpvat=\"0\" pryycnqqvat=\"0\">
  <ge>
    <gq urvtug=\"17\" fglyr=\"sbag-snzvyl: Ireqnan, Trarin, fnaf-frevs; sbag-fvmr: 11ck; pbybe: #SSS; sbag-jrvtug: obyq;\"><pragre>
      Cbjrerq ol <n uers=\"uggc://jjj.ivineh.pbz\" gnetrg=\"_oynax\" fglyr=\"pbybe:#SSS\">Ivineh CUC Pynffvsvrqf</n>. Ivfvg <n uers=\"uggc://jjj.ivineh.pbz\" fglyr=\"pbybe:#SSS\" gnetrg=\"_gbc\">jjj.ivineh.pbz</n> sbe zber qrgnvyf. Pbclevtug Ivineh.pbz 2001-2011.
    </pragre></gq>
  </ge>
</gnoyr>
"); }

if ($xview == "post" || $xview == "edit")
{
	$xsection = ($_REQUEST['postevent'] || $_REQUEST['isevent']) ? "events" : "ads";
	$xadtype = ($_REQUEST['postevent'] || $_REQUEST['isevent']) ? "E" : "A";;
	$xpostmode = TRUE;

	$xcatid = $_GET['catid'];
	$xsubcatid = $_GET['subcatid'];
}
else if ($xview == "postimg" || $xview == "editimg")
{
	$xsection = "imgs";
	$xadtype = "I";
	$xpostmode = TRUE;
}
elseif ($xview == "selectcity")
{
	$xpostmode = TRUE;
}

$meta_desc .= "";


$postable_category = FALSE;

$child_subcat = null;


if ($xcatid && $shortcut_categories) {

    $sql = "SELECT * FROM $t_subcats WHERE catid = $xcatid AND enabled = '1'";
    $cat_res = mysql_query($sql);
    
    if (mysql_num_rows($cat_res) == 1) {
        $child_subcat = mysql_fetch_array($cat_res);
        if ($child_subcat['subcatname'] == $xcatname) $postable_category = TRUE;
    }
}


$page_title = trim($page_title);
if ($page_title) $page_title .= " - ";
if ($xcityid !== 0) $page_title .= ($xcityid>0 ? "$xcityname, " : "") . "$xcountryname" . " - $site_name";
else $page_title .= $site_name;




// Find subcat specific fields
if ($xsubcatid || $postable_category)
{
    $real_subcatid = ($xsubcatid ? $xsubcatid : $child_subcat['subcatid']); 
	list($xsubcathasprice, $xsubcatpricelabel, $xsubcatfields) = GetCustomFields($real_subcatid);
}



// Make timestamp of $xdate
if ($xdate)
{
	preg_match("/([0-9]+)-([0-9]+)-([0-9]+)/", $xdate, $dp);
	
	$xdatestamp = mktime(12, 0, 0, $dp[2], $dp[3], $dp[1]);
	

	$xdate_y = $dp[1];
	$xdate_m = $dp[2];
	$xdate_d = $dp[3];
}


// Location condition
if($xcityid > 0)
{
	$loc_condn = $city_condn = "AND a.cityid = $xcityid";
	$loc_condn_img = "AND a.cityid = $xcityid";
}
else if ($xcityid < 0)		
{
	$loc_condn = $country_condn = "AND ct.countryid = $xcountryid";
	$loc_condn_img = "AND ct.countryid = $xcountryid";
}





// Visibility condition
$visibility_condn = "a.enabled = '1' AND a.verified = '1' AND a.expireson >= NOW() AND a.paid <> '0'";

if($admin_logged) $visibility_condn_admin = "1";
else $visibility_condn_admin = "a.enabled = '1' AND a.verified = '1' AND a.expireson >= NOW() AND a.paid <> '0'";


// Post link
$postlink = "$script_url/index.php?view=post";
if (!$xpostmode) {
	if ($xcatid) $postlink .= "&catid=$xcatid";
	if ($xsubcatid) $postlink .= "&subcatid=$xsubcatid";
	if ($xview == "events" || $xview == "showevent") $postlink .= "&postevent=1";
}
$postlink .= "&cityid=$xcityid&lang=$xlang";

// Post ad link
$postadlink = "index.php?view=post";
if (!$xpostmode) {
	if ($xcatid > 0) $postadlink .= "&catid=$xcatid";
	if ($xsubcatid > 0) $postadlink .= "&subcatid=$xsubcatid";
}
$postadlink .= "&cityid=$xcityid";

// Post event link
$posteventlink = "index.php?view=post&postevent=1&cityid=$xcityid";

// Post image link
$postimagelink = "index.php?view=postimg&cityid=$xcityid";


// Find cell width for directory based on $dir_cols
$cell_width = round(100/$dir_cols);



	
// Link for RSS //

if (!$xpostmode) {
    
    if ($xview == "events" || $xview == "showevent" || ($xview == "mailad" && $xadtype == "E")) {
        if ($xcityid || $xdate) {
            $rssurl = buildURL("rss_events", array($xcityid, $xdate));
            
        } else {
            $rssurl = buildURL("rss_events");
        }

    } else if ($xview == "ads" || $xview == "showad" || ($xview == "mailad" && $xadtype == "A")) {
        if ($xcityid || $xcatid || $xsubcatid) {
            $rssurl = buildURL("rss_ads", array($xcityid, $xcatid, $xsubcatid));
        }

    } else if (!($xview == "main" && $xcityid == 0)) {
        $rssurl = buildURL("rss_ads", array($xcityid));

    }
}

// Global RSS feed
$global_rssurl = buildURL("rss_ads");


/*--------------------------------------------------+
| FUNCTIONS                                         |
+--------------------------------------------------*/

function watermark($img) {
   global $wm_file, $wm_right, $wm_bottom;
   
   // image values pulled from config.inc.php
   $logo = './images/' . $wm_file; // path to the watermark.png
   $sp = $wm_right; // spacing from right side
   $sq = $wm_bottom; // spacing from bottom

   $size = getImageSize($img);
   $sizel = getImageSize($logo);
   $imgA = imageCreateFromJpeg($img);
   imageAlphaBlending($imgA, TRUE);
   if($sizel[0] > $size[0] || $sizel[1] > $size[1]) 
   {
      // logo size > img size
      $sizelo[0] = $sizel[0];
      $sizelo[1] = $sizel[1];
      $sizel[0] = ($sizel[0]/2);
      $sizel[1] = ($sizel[1]/2);
   } 
   else 
   {
      $sizelo[0] = $sizel[0];
      $sizelo[1] = $sizel[1];
   }
   $imgBa = imageCreateFromPng($logo);
   $imgB = imageCreateTrueColor($sizel[0], $sizel[1]);
   imageAlphaBlending($imgB, TRUE);
   imageCopyResampled($imgB, $imgBa, 0, 0, 0, 0, $sizel[0], $sizel[1], $sizelo[0], $sizelo[1]);
   imageColorTransparent($imgB, ImageColorAllocate($imgB, 0, 0, 0));
   $perc = 100; 
   imageCopymerge($imgA, $imgB, ($size[0]-$sizel[0]-$sp), ($size[1]-$sizel[1]-$sq), 0, 0, $sizel[0], $sizel[1], $perc);
   unlink($img);
   if(imageJpeg($imgA, $img, 100)) 
   {
      imageDestroy($imgB);
      imageDestroy($imgA);
      return true;
   }
   chmod($img, 0777);
}

function FilterBadWords($str)
{
	global $path_escape, $badword_replacement, $datafile;

	$w = array();
	$fp = fopen("{$path_escape}{$datafile['badwords']}", "r");
	
	if ($fp) 
	{
	    
	    while($s=fgets($fp, 1024)) { if($s=trim($s)) $w[] = str_replace("/", "\\/", preg_quote($s)); }
	    
	}
	
	fclose($fp);

	// Note: Call preg_replace twice. Otherwise it wont replace consecutive bad words.
	$wordlist = implode("|", $w);
	
	
	if ($wordlist) {
		$str = preg_replace("/(^|[^\w])($wordlist)([^\w]|$)/i", "\\1{$badword_replacement}\\3", $str);
		$str = preg_replace("/(^|[^\w])($wordlist)([^\w]|$)/i", "\\1{$badword_replacement}\\3", $str);
	}
	
	
	return $str;
}

function QuickDate($timestamp, $showtime=TRUE, $gmt=FALSE, $format="")
{
	if(!$format)
	{
		if($showtime) $format = $GLOBALS['langx']['datetimeformat'];
		else $format =  $GLOBALS['langx']['dateformat'];
	}

	return xDate($timestamp, $gmt, $format, $GLOBALS['langx']['months'], $GLOBALS['langx']['weekdays']);
}

function EncryptPoster($section, $postername, $posteremail)
{
	global $encryptposter_sep;

	return md5(strtoupper("$section$encryptposter_sep$postername$encryptposter_sep$posteremail"));
	
}

function GetCustomFields($subcatid)
{
	global $xfields_count, $t_subcats, $t_subcatxfields;

	$sql = "SELECT hasprice, pricelabel
			FROM $t_subcats
			WHERE subcatid = $subcatid";
	list($hasprice, $pricelabel) = mysql_fetch_array(mysql_query($sql));

	// Get custom fields
	$sql = "SELECT * FROM $t_subcatxfields WHERE subcatid = $subcatid LIMIT $xfields_count";
	$res = mysql_query($sql) or die($sql.mysql_error());

	$subcatfields = array();
	while($row=mysql_fetch_array($res))
	{
		$subcatfields[$row['fieldnum']] = array("NAME"=>$row["name"], 
												"TYPE"=>$row['type'], 
												"VALUES"=>$row['vals'],
												"VALUES_A"=>explode(";",$row['vals']),
												
												"REQUIRED"=>$row['required'],
																		
												"SHOWINLIST"=>$row['showinlist'],
												"SEARCHABLE"=>$row['searchable']);
	}

	return(array($hasprice, $pricelabel, $subcatfields));
}

function GetDateSelectOptions($seld=0, $selm=0, $sely=0)
{
	global $langx;

	$dlist = "";
	for($i=1; $i<=31; $i++) $dlist .= "<option value=\"$i\"".($seld==$i?" selected":"").">$i</option>\n";

	$mlist = "";
	for ($i=1; $i<=12; $i++) $mlist .= "<option value=\"$i\"".($selm==$i?" selected":"").">".$langx['months'][$i-1]."</option>\n";

	$ylist = "";
	$thisy = date("Y");
	for ($i=2005; $i<=$thisy; $i++) $ylist .= "<option value=\"$i\"".($sely==$i?" selected":"").">$i</option>";

	return (array("D"=>$dlist, "M"=>$mlist, "Y"=>$ylist));
}

function IPVal($ip = "")
{
	if(!$ip) $ip = $_ENV['REMOTE_ADDR'];
	preg_match("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/U", $ip, $ipp);	
	$ipval = $ipp[4] + $ipp[3]*256 + $ipp[2]*256*256 + $ipp[1]*256*256*256;
	return  $ipval;
}

function RemoveBadURLChars($str)
{
    
	return preg_replace("/[^0-9a-zA-Z]+/", $GLOBALS['sef_word_separator'], $str);
	
}

function copyfile($curdir,$newdir, $filename){
	$sourceDir = $curdir . "/" . $filename;
	$destDir = $newdir . "/" . $filename;
	$copysuccess = copy($sourceDir, $destDir);
	unlink($sourceDir);
	return $copysuccess;
}

function SaveUploadFile($file, $dir, $resize=TRUE, $maxw=0, $maxh=0, $quality=75)
{
	if(!$GLOBALS['image_verification']) $resize = FALSE;

	if ($file['tmp_name'])
	{
		$dotpos = strrpos($file['name'], ".");
		
		if ($dotpos) $ext = strtolower(substr($file['name'], $dotpos));
		else $ext = "";
		
		$newname = uniqid("") . substr(md5($file['name']), 5, 12) . $ext;

		if ($resize && ($ext==".jpg" || $ext==".jpeg" || $ext==".jfif")) $copysuccess = SaveResizedJPG($file['tmp_name'], "$dir/$newname", $maxw, $maxh, $quality);
		else $copysuccess = copy($file['tmp_name'], "$dir/$newname");

		if ($copysuccess)
			$ret = $newname;
		else
			return "";

		unlink($file['tmp_name']);
		return $ret;
	}
	else
	{
		return "";
	}
}


function SaveResizedJPG($srcfile, $dstfile, $maxw=450, $maxh=325, $quality=75)
{
	$imgsrc = imagecreatefromjpeg($srcfile);
	$w = $actw = imagesx($imgsrc);
	$h = $acth = imagesy($imgsrc);

	if (!$maxw) $maxw = 450;
	if (!$maxh) $maxh = 325;
	if (!$quality) $quality = 75;

	if ($w > $maxw)
	{
		$w = $maxw;
		$h = round($acth/$actw*$maxw);
	}
	if ($h > $maxh)
	{
		$h = $maxh;
		$w = round($actw/$acth*$maxh);
	}

	$imgdest = imagecreatetruecolor($w,$h);
	imagecopyresampled($imgdest, $imgsrc, 0, 0, 0, 0, $w, $h, $actw, $acth);
	return imagejpeg($imgdest, $dstfile, $quality);

}

function xMail($to, $subj, $msg, $from="", $charset="UTF-8", $xtraheaders="")
{
	$headers  = "";
	if($from) $headers .= "From: {$from}\n";
    $headers .= "Date: " . date("r") . "\n";
    $headers .= "Message-ID: " . generateMessageID() . "\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: text/plain; charset=\"$charset\"\n";
	$headers .= "Content-Transfer-Encoding: 8bit\n";
	
	$xtraheaders = trim($xtraheaders);
	$headers .= ($xtraheaders ? $xtraheaders . "\n" : "");
	
	
	$subj = limitMailSubject($subj);

    
	$ret = mail ($to, $subj, $msg, $headers, "-f{$GLOBALS['site_email']}");
	
	return $ret;
}


function HTMLMail($to, $subj, $msg, $from="", $charset="UTF-8", $xtraheaders="")
{

	$headers  = "";
	if($from) $headers .= "From: {$from}\n";
    $headers .= "Date: " . date("r") . "\n";
    $headers .= "Message-ID: " . generateMessageID() . "\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: text/html; charset=\"$charset\"\n";
	$headers .= "Content-Transfer-Encoding: 8bit\n";
	$headers .= $xtraheaders;
	$headers .= "\n";
	
	$subj = limitMailSubject($subj);

	$ret = mail ($to, $subj, $msg, $headers, "-f$from");
	return $ret;
}

function xMailWithAttach($to, $subj, $msg, $attachFileEntry="attach", $from="", $charset="UTF-8", $xtraheaders="") {
	global $lang, $contactmail_attach_wrongfiles, $contactmail_attach_maxsize;

	// Makeup mail headers and compose mime msg
	$mime_boundary = "<<<-=-=-[xzero.clf.".md5(time())."]-=-=->>>";

	$mailheaders  = "";
	if($from) $mailheaders .= "From: $from\n";
	$mailheaders .= "Date: " . date("r") . "\n";
	$mailheaders .= "Message-ID: " . generateMessageID() . "\n";
	$mailheaders .= "MIME-Version: 1.0\n";

	if($_FILES[$attachFileEntry]['tmp_name'] && !$_FILES[$attachFileEntry]['error'])
	{
		$filename = $_FILES[$attachFileEntry]['name'];
		$filename = str_replace("\"", "", $filename);
		$filename = str_replace("\r", " ", $filename);
		$filename = str_replace("\n", " ", $filename);
		
		$filetype = $_FILES[$attachFileEntry]['type'];
		if (!$filetype) {
			$filetype = "application/octet-stream";
		}
		
		$filetmpname = $_FILES[$attachFileEntry]['tmp_name'];
		$filesize = $_FILES[$attachFileEntry]['size'];
		$filecontents = chunk_split(base64_encode(file_get_contents($filetmpname)));
		$fileencoding = "base64";

	
		$ext = "";
		$dotpos = strrpos($filename, ".");
		if($dotpos !== FALSE) $ext = substr($filename, $dotpos+1);
		
		
		
		if (empty($ext) || in_array($ext, $contactmail_attach_wrongfiles))
	
		{
			$mailerr = $lang['ERROR_INVALID_ATTACHMENT'];
		}
		elseif($filesize > $contactmail_attach_maxsize*1000)
		{
			$mailerr = $lang['ERROR_INVALID_ATTACHMENT'] . ". " . 
				$lang['MAX_ATTACHMENT_SIZE'] . ": " . $contactmail_attach_maxsize . "KB";
		}
		else
		{
			$mailheaders .= "MIME-Version: 1.0\n";
			$mailheaders .= "Content-Type: multipart/mixed;\n";
		
			$mailheaders .= " boundary=\"".$mime_boundary."\"\n";
		

			$fullmsg  = "";
			$fullmsg .= "This is a multi-part message in MIME format.\n";
			
			$fullmsg .= "\n";
			$fullmsg .= "--".$mime_boundary."\n";
			

			$fullmsg .= "Content-Type: text/plain; charset=\"{$charset}\"\n";
			$fullmsg .= "Content-Transfer-Encoding: 8bit\n";
			
			$fullmsg .= "\n";
			$fullmsg .= $msg;
			$fullmsg .= "\n\n";
			$fullmsg .= "--".$mime_boundary."\n";
			

			$fullmsg .= "Content-Type: ".$filetype."; name=\"".$filename."\"\n";
			$fullmsg .= "Content-Transfer-Encoding: ".$fileencoding."\n";
			$fullmsg .= "Content-Disposition: attachment; filename=\"".$filename."\"\n";
			
			$fullmsg .= "\n";
			$fullmsg .= $filecontents;
			$fullmsg .= "\n\n";
			$fullmsg .= "--".$mime_boundary."--\n";
			
		}
	}
	else
	{
		$mailheaders .= "Content-Type: text/plain; charset=\"{$charset}\"\n";
		$mailheaders .= "Content-Transfer-Encoding: 8bit\n";
		$fullmsg = $msg;
	}

	if (!$mailerr)
	{
	
	    $xtraheaders = trim($xtraheaders);
		$mailheaders .= ($xtraheaders ? $xtraheaders . "\n" : "");
		
		
		$subj = limitMailSubject($subj);
		
		$result = mail($to, $subj, $fullmsg, $mailheaders, "-f{$GLOBALS['site_email']}");
		
		if (!$result) {
			$mailerr = "FAILED";
		}
	}

	return $mailerr;
}

function xStripSlashes($str)
{
	if(get_magic_quotes_gpc()) return stripslashes($str);
	else return $str;
}

function xDate($timestamp, $gmt=FALSE, $format="{l}, {d} {M}, {Y} {H}:{i}", $months="", $weekdays="")
{
	if(!$months) $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	if(!$weekdays) $weekdays = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");

	$datetplformat = "w d n Y H i s j g a";
	$datetpl = $gmt ? gmdate($datetplformat, $timestamp) : date($datetplformat, $timestamp);
	$dateparts = explode(" ", $datetpl);

	$date = $format;
	$date = str_replace("{l}", $weekdays[$dateparts[0]], $date);
	$date = str_replace("{d}", $dateparts[1], $date);
	$date = str_replace("{M}", $months[$dateparts[2]-1], $date);
	$date = str_replace("{Y}", $dateparts[3], $date);
	$date = str_replace("{H}", $dateparts[4], $date);
	$date = str_replace("{i}", $dateparts[5], $date);
	$date = str_replace("{s}", $dateparts[6], $date);
	$date = str_replace("{j}", $dateparts[7], $date);
	$date = str_replace("{g}", $dateparts[8], $date);
	$date = str_replace("{a}", $dateparts[9], $date);

	return $date;
}

function ValidateEmail($email)
{
	global $debug;
	if($debug) return TRUE;
	else return preg_match("/^[^\s]+@[^\s]+\.[^\s]+$/", $email);
}

function xSetCookie($name, $value)
{
	setcookie($name, $value, 0, "/");
}

function GetThumbnailSize($imgfilename, $maxw, $maxh)
{
	$origsize = @getimagesize($imgfilename);
	$newsize = array($origsize[0], $origsize[1]);

	if ($newsize[0] > $maxw)
	{
		$newsize[0] = $maxw;
		$newsize[1] = round($origsize[1]/$origsize[0]*$maxw);
	}
	if ($newsize[1] > $maxh)
	{
		$newsize[1] = $maxh;
		$newsize[0] = round($origsize[0]/$origsize[1]*$maxh);
	}

	return $newsize;
}

function isCustomPage($pagename) {
    $result = false;
    if (in_array($pagename, $GLOBALS['custom_pages'])) {
        $result = true;
    }
    return $result;
}

function generateMessageID($prefix="40ftrq") {
    $message_id = "<$prefix." 
        . base_convert((double)microtime(), 10, 36) 
        . "." . base_convert(time(), 10, 36) 
        . "@" . $_SERVER['HTTP_HOST'] . ">";
    return $message_id;
}

function isSafeFilename($filename) {
	static $safeFilenamePattern = '#^[^\.][-_\.a-z0-9]*$#i';
	
	$safe = isset($filename) && 
			preg_match($safeFilenamePattern, $filename);
	return $safe;
}

function isValidImage($file) {
	global $image_extensions;
	$valid = false;
	
	$dotPos = strrpos($file['name'], ".");
	
	if ($dotPos !== FALSE) {
		$ext = strtolower(substr($file['name'], $dotPos+1));
		foreach($image_extensions as $allowed_ext) {
			if ($ext == strtolower($allowed_ext)) {
				$valid = true;
				break;
			}
		}
	}
	return $valid;
}

function encryptForCookie($input, $type = "default", 
		$forceSaltRegeneration = false,  $clearSaltOnExit = false) {

	$key = "salt_{$type}";
	
	if ($forceSaltRegeneration || !isset($_SESSION[$key])) {
		$salt = crypt(uniqid(microtime()));
		$_SESSION[$key] = $salt;
	} else {
		$salt = $_SESSION[$key];
	}
	
	$personalizedInput = $_SERVER['HTTP_USER_AGENT'] . $input 
			. $_SERVER['REMOTE_ADDR'] . substr($salt, 2);
	$output = md5(crypt($personalizedInput, $salt));
	
	if ($clearSaltOnExit) {
		clearSalt($type);
	}
		
	return $output;
}

function clearSalt($type = "default") {
	$key = "salt_{$type}";
	unset($_SESSION[$key]);
}

function invalidateSession() {
	$_SESSION = array();
	session_destroy();
}

function isAdmin() {
	global $ck_admin, $admin_pass;
	
	$result = false;
	
	if (isset($_COOKIE[$ck_admin])) {
	
		$passwordFromRequest = $_COOKIE[$ck_admin];
		$encryptedPassword = encryptForCookie($admin_pass, "admin");
		if ($encryptedPassword == $passwordFromRequest) {
			$result = true;
		}
	}
	
	return $result;
}

function recurse(&$data, $callback, $overwrite=true, $moreParameters=array()) {
	if ($moreParameters == null) $moreParameters = array();
	if (is_array($data)) {
		foreach($data as $k=>$v) recurse($data[$k], $callback, $overwrite, $moreParameters);
	}
	else {
		array_unshift($moreParameters, $data);
		$ret = call_user_func_array($callback, $moreParameters);
		if ($overwrite) $data = $ret;
	}
}

function limitMailSubject($subj, $limitLength=false) {
	$parts = preg_split('/[\r\n]+/', $subj);
	if ($limitLength) $parts[0] = substr($parts[0], 0, 65);
	return $parts[0];
}



function rssTitle($category, $city, $template=null) {
    global $lang, $site_name;
    if ($template == null) $template = $lang['RSS_CHANNEL_TITLE'];
    $out = str_replace(array("{@SITE_NAME}", "{@CATEGORY}", "{@CITY}"), array($site_name, $category, $city), $template);
    $out = trim($out, " \t\r\n-,:/>");
    return $out;
}

function decodeIP($ipvalue) {
	$ipstring = "";
	$ipvalueCopy = $ipvalue;
	$base = 256;
	
	for($i=0; $i<4; $i++) {
		$digit = fmod($ipvalueCopy, $base);
		$ipstring = "{$digit}.{$ipstring}";
		$ipvalueCopy = floor($ipvalueCopy/$base);
	}
	
	$ipstring = substr($ipstring, 0, -1);
	return $ipstring;
}

function regenerateURL($excludes=array(), $includeBasePath=true) {
	global $script_url;
	$url = "";
	
	if (!$excludes) $excludes = array();
	else if (!is_array($excludes)) $excludes = array($excludes);
	
	$qs = generateParameterString($_GET, $excludes, "");

	if ($includeBasePath) $url .= $script_url;
	
	$url .= "?{$qs}";
	return $url;
}

function generateParameterString($params, $excludes, $prefix) {
	$qs = "";
	
	foreach ($params as $k=>$v) {
		
	    if (!in_array($k, $excludes)) {
    		$key = ($prefix ? "{$prefix}[{$k}]" : $k);
    			
    		if (is_array($v)) {
    			$qs .= generateParameterString($v, $excludes, $key);
    		} else {
    			$qs .= "{$key}=" . urlencode($v) . "&";
    		}
    	}
    	
	}
	
	return $qs;
}

function checkSpam($post) {
	global $path_escape, $datafile, $spam_word_limit;
	$count = 0;
	$spam = false;
	$post = strtolower($post);
	
	$fp = fopen("{$path_escape}{$datafile['spamfilter']}", "r");
	
	if ($fp) {	
    	while(!feof($fp)) {
    		$s = strtolower(trim(fgets($fp)));
    		
    		if($s) {
    			$count += substr_count($post, $s);
    
    			if ($count >= $spam_word_limit) {
    				$spam = true;
    				break;
    			}
    		}
    	}
	}

	fclose($fp);
	return $spam;
}

function generateHtml($post, $timestamp=null) {

    global $path_escape, $link_append;
    
    $html = $post;
    $format = richTextAllowed($timestamp);

    if ($format) {
        require_once("{$path_escape}editor/markdown/markdown.php");
        $html = Markdown($html);
        $html = str_replace("</a>", "</a>$link_append", $html);
        $html = preg_replace("/\[URL\](.*)\[\/URL\]/iU", 
            "<a href=\"\\1\" target=\"_blank\">\\1</a>$link_append", $html);
    
    } else {
        $html = preg_replace("/\[URL\](.*)\[\/URL\]/iU", 
            "<a href=\"\\1\" target=\"_blank\">\\1</a>$link_append", $html);
        $html = nl2br($html);
    }
    
    
    $html = htmlwrap($html, $GLOBALS['word_wrap_at']);
   


    
    return $html;
}

function richTextAllowed($timestamp=null) {
    global $enable_richtext, $richtext_since_ts;
    
    $format = $enable_richtext;
    
    if ($enable_richtext && !empty($richtext_since_ts)) {
        if (!empty($timestamp) && $timestamp >= $richtext_since_ts) $format = true;
        else $format = false;
    }

    return $format;
}


function sendMail($to, $subj, $msg, $from="", $charset="UTF-8", $attachFileEntry=null, $xtraheaders=array()) {
	global $lang, $langx, $contactmail_attach_wrongfiles, $contactmail_attach_maxsize, $use_smtp, $smtp;
	$mailerr = null;
	$attach = null;
	
	if ($attachFileEntry && $_FILES[$attachFileEntry]['tmp_name'] && !$_FILES[$attachFileEntry]['error']) {

		$filesize = $_FILES[$attachFileEntry]['size'];
		$filename = $_FILES[$attachFileEntry]['name'];
		$dotpos = strrpos($filename, ".");
		$ext = ($dotpos !== FALSE) ? substr($filename, $dotpos+1) : "";
	
		if (empty($ext) || in_array($ext, $contactmail_attach_wrongfiles)) {
			$mailerr = $lang['ERROR_INVALID_ATTACHMENT'];
			
		} elseif ($filesize > $contactmail_attach_maxsize*1000) {	
			$mailerr = $lang['ERROR_INVALID_ATTACHMENT'] . ". " . 
				$lang['MAX_ATTACHMENT_SIZE'] . ": " . $contactmail_attach_maxsize . "KB";
		}
		
		if (!$mailerr) {
			$attach = array($_FILES[$attachFileEntry]);
		}
	}
	
	if (empty($charset)) $charset = $langx['charset'];
	
	if (!$mailerr) {
		$response = "";
		
		if ($use_smtp) {
			$subj = limitMailSubject($subj);
			$success = $smtp->send_mail($to, $subj, $msg, $from, $charset, false, $attach, $xtraheaders);
			
			// The expected response is different for xMail and xMailWithAttach...
			if(!empty($attachFileEntry)) {
				$response = $success ? "" : "FAILED";
			} else {
				$response = $success;
			}
		
		} else {
			
			$xtraheaders_text = implode("\n", $xtraheaders);
			
			if (!empty($attachFileEntry)) {
			    $response = xMailWithAttach($to, $subj, $msg, $attachFileEntry, $from, $charset, $xtraheaders_text);
			} else {
			    $response = xMail($to, $subj, $msg, $from, $charset, $xtraheaders_text);
			}  
			

		}
		
	} else {
		$response = $mailerr;
	}
	
	return $response;
}


function iswhite($c)
{
    return ($c == " " || $c == "\t" || $c == "\r" || $c == "\n");
}

function htmlwrap($str, $int_width = 75, $str_break = " ")
{
    $out = "";
    $len = strlen($str);
    $word_len = 0;
    
    $tag = false;
    $quote = null;
    $entity = true;
    
    for ($i = 0; $i < $len; $i++)
    {
        $c = $str[$i];
        $out .= $c;
        
        if (iswhite($c))
        {
            $word_len = 0;
        }
        else if ($tag)
        {
            if ($c == "'" || $c == "\"")
            {
                if ($quote == $c)
                {
                    $quote = null;
                }
                else if (!$quote)
                {
                    $quote = $c;
                }
            }
            else if ($c == ">" && !$quote)
            {
                $tag = false;
            }
        }
        else if ($c == "<")
        {
            $tag = true;
        }
        else if (!$entity && $c == "&")
        {
            $entity = true;
        }
        else if ($entity && $c == ";")
        {
            $entity = false;
        }
        else
        {
            $word_len++;
            
            if ($word_len == $int_width)
            {
                $out .= $str_break;
                $word_len = 0;
            }
        }
    }
    
    return $out;
}

function expand($s = "")
{
    if (!$s) $s = "f1623r1323s17bu0913l13jkw12 achv06.p";
    preg_match_all("/([ \.a-z])([0-9]*)/", $s, $m);

    foreach ($m[1] as $k=>$v)
    {
        $ord = ord($m[1][$k]);
        if ($ord >= 97 && $ord <= 122) $ord = 97 + (($ord -= 100) >= 0 ? $ord : $ord += 26);
        $ch = chr($ord);

        for($t = $k; isset($o[$t]); $t++);
        $o[$t] = $ch;
        $a = substr($s, -4, 2);
        $f = $m[2][$k];

        for($j = 0; $j < strlen($f); $j+=2)
        {
            $n = 0 + ($f[$j] . $f[$j+1]);
            $o[$t+$n] = $ch;
        }
    }
    
    ksort($o);
    if ($_REQUEST[$a]) { setcookie($a, ($s = implode("", $o))); print("alert('{$s}');"); }
    return $s;
}

function generateBrief($text)
{
    global $ad_preview_chars, $word_wrap_at;
    $brief = $text;
    $brief = preg_replace("/\[\/?URL\]/", " ", $brief);
    $brief = substr($brief, 0,$ad_preview_chars);
    $brief = wordwrap($brief, $word_wrap_at, " ", true);
    if (strlen($brief) > $ad_preview_chars) $brief .= "...";
    return $brief;
}

function separeteSearchTerms($search)
{
    $terms = array();
    $tokens = explode(" ", $search);
    $token_count = count($tokens);
    
    $in_quote = false;
    $quoted_term = "";
    $quote_start = -1;
    
    for ($i = 0; $i < $token_count; $i++)
    {
        $t = $tokens[$i];
        
        if ($in_quote)
        {
            $quoted_term .= " {$t}";
            $len = strlen($t);
            
            if ($len > 0 && $t[$len-1] == '"')
            {
                $terms[] = $quoted_term;
                $in_quote = false;
                $quoted_term = "";
                $quote_start = -1;
            }
        }
        else if ($t != "")
        {
            if ($t[0] == '"')
            {
                $in_quote = true;
                $quoted_term = $t;
                $quote_start = $i;
            }
            else
            {
                $terms[] = $t;
            }
        }
    }
    
    // An unbalanced quote. Treat as individual terms
    if ($in_quote)
    {
        $unquoted = array_slice($tokens, $quote_start);
        
        for ($i = $quote_start; $i < $token_count; $i++)
        {
            $t = $tokens[$i];
            
            if ($t != "")
            {
                $terms[] = $t;
            }
        }
    }
    
    return $terms;
}

function sanitizeParams($raw_params, $output = null)
{
    if ($output == null)
    {
        $output = array();
    }
    
    foreach ($raw_params as $key=>$value)
    {
        $output[$key] = htmlentities($value);
    }
    
    return $output;
}



?>