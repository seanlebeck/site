<?php

// MySQL connection settings
@include('dbconnect.inc.php');

@mysql_connect($db_host, $db_user, $db_pass) or die("Cannot connect to DB");
@mysql_select_db($db_name);
$site_data_que = @mysql_query("select * from vivaru_site_control");
$site_data = @mysql_fetch_array($site_data_que);
$feature_data_que = @mysql_query("select * from vivaru_feature_control");
$feature_data = @mysql_fetch_array($feature_data_que);

/*********************************************************************/
/* VIVARU PHP CLASSIFIEDS CONFIGURATION & SETTINGS */
/* PLEASE TAKE GREAT CARE WHILE EDITING ALL SETTINGS BELOW */
/* MISTAKES HERE MIGHT STOP YOUR SCRIPT FROM WORKING */
/* REMEMBER TO ALWAYS BACKUP ALL FILES BEFORE EDITING */
/*********************************************************************/

// Word separator to use in search engine friendly URLs, if $sef_urls is enabled.
$sef_word_separator = "-";

// Character to use as separator in the path shown on top of the page
$path_sep = " | ";

// Wether to sort cats and subcats alphabetically
$dir_sort = FALSE;

// Wether to sort location alphabetically
$location_sort = FALSE;

//Enable extra upload fields
$enable_extra_uploads = 1; 

//Enable URGENT tag on ads
$enable_urgent_tag = 1; 

// Enabled the Related Ads.
$related_enabled = 1;

// Wether to use star rating system for ads. Set to FALSE to disable.
$star_rating = TRUE;

// Limit related ads to city only.  Only displays ads from related cities. Default 1
$related_city_limit = 0;

// Limit related ads to category only.  Only displays ads from related categories and subcategories. Default 0
$related_cat_limit = 0;

// Limit amount of keyword results.  Default 5
$relate_limit = 10;

// Determine how many seconds sender should wait before emailing someone through
// the contact form if the ad poster enabled contact form. Default is 90.
$ad_contact_limit = 90;

// Max amount of emails sender can send within a 24 hour period.  Default is 20.
$ad_contact_max_count = 20;

// Amount of seconds to wait before DB removes old log of sender IP.
// IT IS RECOMMENDED NOT TO GO BEYOND 86400 seconds (1 day)
$ad_contact_db_empty = 86400;

// Determine the minimum amount of words for ad title.  Default: 1 word
$post_char_title_limit = 1;

// Determine the minimum amount of words for ad description.  Default: 1 word
$post_char_desc_limit = 1;

// The amount of days set in advanced to email users with expiring ads.
// This format is in seconds (86400 = 1 day, 172,800 = 2 days, etc.).  Default is 7 days
$expire_ads_ahead = 604800;

// Select if you want the poster's Ad Creation Date to update when they renew their ad
// Default is 1 (enabled), Disabled is 0
$update_ad_creation = 1;

// Select if you want to receive an email whenever the expire cron is ran that provides details such as 
// how many emails were sent.
// Default is 0 (disabled), Enabled is 1
$reminder_email_master = 1;

// The amount of days set in advanced to renew ads from admin panel.
// This format is in days (1 = 1 day, 2 = 2 days, etc.).  Default is 30 days
$admin_renew_days = 30;

// Whether you want admin renew mod to update ad's creation date at time of renewal
// Set to 0 to disable.  Default is 1
$admin_renew_cre_dt = 1;

// MIME types of files that are to be accepted as images
$pic_filetypes = array("image/gif", "image/jpeg", "image/pjpeg", "image/png");
$image_extensions = array("gif", "jpg", "jpeg", "png");

// Thumbnail dimensions
$tinythumb_max_width = 50;			// Thumbnail in ad list
$tinythumb_max_height = 50;
$smallthumb_max_width = 250;		// Left sidebar
$smallthumb_max_height = 200;
$thumb_max_width = 250;				// Under images
$thumb_max_height = 250;

// The quality of the JPEG file after resizing (in %)
$images_jpeg_quality = 100;

// Name of watermark image filename. Default: watermark.png
$wm_file = 'watermark.png';

// Space from the right of watermark. Default: 5
$wm_right = 5;

// Space from the bottom of watermark. Default: 5
$wm_bottom = 5;

// Number of custom fields. Max 10.
$xfields_count = 10;

// Number of latest ads etc. to show in the homepage. Set to 0 to disable.
$latestads_count = 9;
$latest_featured_ads_count = 9;

// HTML to append to the end of links in the ad
$link_append = " <span class=\"link_marker\">&raquo;</span> ";

// Word with which the bad words are to be replaced.
$badword_replacement = "*****";

// This much number of characters will be taken from the description
// as the title for an ad, if none given. Must be <= 100
$generated_adtitle_length = 70;

// String to be appended to generated ad titles
$generated_adtitle_append = " ...";

// Show list of categories in left sidebar
$show_cats_in_sidebar = TRUE;

// Show thumbnails with ads in the category pages 
$ad_thumbnails = TRUE;

// In the city list in the homepage, show cities for the currently selected region only.
// Helpful if you have lots of cities and regions listed.
$expand_current_region_only = FALSE;

// From address to use for mails sent using the contact form.
// Set to $site_email to make it the same as the site email set above.
$contactmail_from = $site_email;

// Max number of spam words allowed in a post. If the number exceeds this, the 
// post will be marked as spam and would require admin approval.
$spam_word_limit = 5;

// Select if you want Adult warning to use cookies or not.  By default it uses cookies
// but you can set to 0 to use sessions instead.   Default: 1, 0 to disable.
$alert_use_cookies = 1;

//Number of contact messages per page to show in Admin panel
$msgs_per_page = 10;

//Number of Comments per page to show in Admin panel
$comments_per_page = 10;

//Captcha image. Set to FALSE to disable.
$image_verification = TRUE;

/**********************************************************************************/
/* PAYMENT OPTIONS CONFIGURATION BELOW  */
/**********************************************************************************/

// Include the buyer's IP address on the payment site. 1 = enabled, 0 = disabled.  Default 0.
$pay_inc_ip = 0;

// All google checkout transactions will take place in this currency.
$google_currency = "USD";
// All skrill transactions will take place in this currency.
$skrill_currency = "USD";

// Enable PayPal payment option. 1 = enabled, 0 = disabled.  Default 1.
$pay_enable_paypal = 1;

// Enable Google Merchant payment option. 1 = enabled, 0 = disabled.  Default 1.
// YOU MUST HAVE AN ACTIVE GOOGLE CHECKOUT ACCOUNT FOR THIS TO WORK!
// You can signup at: http://checkout.google.com
$pay_enable_google = 1;

// Your unique Merchant ID. This ID is assigned to you from Google Checkout.
// Replace '12345' with your real Merchant ID.  
$account_google = '12345';

// Enable 2Checkout payment option. 1 = enabled, 0 = disabled.  Default 1.
// YOU MUST HAVE AN ACTIVE 2CO ACCOUNT FOR THIS TO WORK!
// You can signup at: http://www.2checkout.com
$pay_enable_2co = 1;

// Your unique 2CO account ID. This ID is assigned to you from 2Checkout.com.
// Replace '12345' with your real Account ID.  
$account_2co = '12345';

// Enable Moneybookers payment option. 1 = enabled, 0 = disabled.  Default 1.
// YOU MUST HAVE AN ACTIVE MONEYBOOKERS ACCOUNT FOR THIS TO WORK!
// You can signup at: http://www.skrill.com
$pay_enable_moneyb = 1;

// Your unique Moneybookers email login. This info is assigned to you from Moneybookers.com.
// Replace 'NO_ONE@moneybookers.com' with your real Account EMAIL.  
$account_mb = 'NO_ONE@moneybookers.com';

// Enable Bank Transfer payment option. 1 = enabled, 0 = disabled.  Default 1.
$pay_enable_bank = 1;

//Enter your bank details. For line break use <br>.
$bank_details ="Account name: Vivaru Classifieds<br>Account number: 123456789<br>Sort code: 12-34-56";

/**********************************************************************************/
/* MORE SETTINGS. The script will work just fine without modifying this settings  */
/* ONLY MODIFY THIS IF YOU NEED IT AND YOU NKNOW WHAT YOURE DOING */
/**********************************************************************************/

// Wether to show the number of ads near regions and cities in the right sidebar
$show_region_adcount = TRUE;
$show_city_adcount = TRUE;

// Number of ads/events and images to show per page
$ads_per_page = 100;
$images_per_page = 50;

$upcoming_events_count = 5;
$upcoming_featured_events_count = 5;

// Maximum size of the file attachment to the mailer (in KB)
$contactmail_attach_maxsize = 300;

// Files that should be prevented from attaching
$contactmail_attach_wrongfiles = array("exe","com","bat","vbs","js","jar","scr","pif");

// RSS feed - no of items and number of characters to show in the description field
$rss_itemcount = 20;
$rss_itemdesc_chars = 255;

// If set and rich text is enabled, allows rich text only in posts made on or after 
// this date. Considered only if $enable_richtext is set to TRUE. Useful if you had 
// modded the script to use HTML formatting. 
// Format: YYYY-mm-dd. 
//
// To disable, set to an old enough date or blank. 
// Eg: $richtext_since = "";
$richtext_since = "2009-06-01";

// Whether to use regular expression search while searching ads. This might take  
// some additional processing power but will return exact word matches.
$use_regex_search = FALSE;

// Quick solution to create "postable" categories.
// When set to TRUE, if a category has got only one subcategory and both has the
// same name, then the subcategory would be hidden to users and the category 
// would act as a shortcut to the subcategory, thus making it postable.
$shortcut_categories = TRUE;

// Quick solution to create "postable" regions.
// When set to TRUE, if a region has got only one city and both has the
// same name, then the city would be hidden to users and the region 
// would act as a shortcut to the city, thus making it postable.
$shortcut_regions = TRUE;

// Uses stricter measures for admin login. If you are experiencing problems
// with admin login, try setting this to FALSE.
$strict_login = FALSE;

// Admin options
$admin_adpreview_chars = 100;
$admin_ads_per_page = 100;
$admin_images_per_page = 30;

// Hide sidebar by default when managing posts to have more room
$admin_auto_hide_sidebar = FALSE;

// Ensure all these 3 variables are set to FALSE
$debug = FALSE;
$demo = FALSE;
$sandbox_mode = FALSE;

$beta = FALSE;

/****************************************/
/* BEGIN account options     */
/**************************************/

// Extra user password protection.  
// WARNING! Changing the SALT value after script is installed will break all user logins!
// It is best to change this before any user is inserted into the DB, including your own account
// on the dbupdate.php page (first DB setup page).
define('SALT', '7p39(X#i');

// Name of accounts directory (without trailing slashes)
$acc_dir = 'accounts';

// Do not edit these next two lines
define('IN_SCRIPT', true); 

include_once($acc_dir . "/acc_config.php");

/************************************/
/* END account options   */
/**********************************/

/*********************************************************************/
/* SITE DATA ARRAY STARTS HERE. DO NOT MODIFY!
/*********************************************************************/

// Name of the site.
$site_name = $site_data[0];

// Site email address
$site_email = $site_data[1];

// The URL of the script (without trailing slashes)
$script_url = $site_data[2];

// Get the language by user selection or cookie
$language = (($_GET['lang']) ? $_GET['lang'] : $_COOKIE['langcheck_lang']);
// Set default language
if ($site_data[3] == '') { $site_data[3] = 'en'; }
if(!$language) $language = "en";

// Meta keywords and description
$meta_keywords = $site_data[4];
$meta_description = $site_data[5];

// Make your site offline - Online. Yes=Offline - No=Online 
$offline = $site_data[6];

$offmesg = $site_data[7] ;

// Paypal account to receive payments.
$paypal_email = $site_data[8];

// Symbol to show for the specified paypal currency. 
// This is what the user sees next to the prices for paid options.
$paypal_currency_symbol = $site_data[9];

// Admin password
if ($site_data[11] == '') { $site_data[11] = 'admin'; }
$admin_pass = $site_data[11];

// Valid paypal currecncy code for payments. 
// All paypal transactions will take place in this currency.
$paypal_currency = $site_data[12];

// Mobile site URl
$mobile_site_url = $site_data[14];

// Ads comments system. 
$ad_comments = $site_data[16];

/*********************************************************************/
/* SITE DATA ARRAY ENDS HERE
/*********************************************************************/

/*********************************************************************/
/* FEATURE DATA ARRAY STARTS HERE DO NOT MODIFY!
/*********************************************************************/

// ID of the default city. If u want to use a region as default, 
// enter the region id preceeded with a '-'. Set this to 0 and 
// the first city in the database will be taken as the default.
$default_city = $feature_data[0];

// Maximum number of abuse reports after which the ads are to be suspended.
// Should always be less than 99999. Set to 0 to disable.
$max_abuse_reports = $feature_data[1];

// Wether to use SE friendly URLs
// Requries .htaccess and mod_rewrite support
$sef_urls = $feature_data[2];

// Wether to enable the event posting (calendar) and image POSTING
$enable_calendar = $feature_data[3];
$enable_images = $feature_data[4];

// Number of columns in the main directory. If you change to less than 3 then the layout might not look good.
$dir_cols = $feature_data[5];

// Number of columns for locations
$location_cols = $feature_data[6];

// Number of picture upload fields to show in post ad page
$pic_count = $feature_data[7];

// Whether to allow rich formatting in posts.
$enable_richtext = $feature_data[9];

$expire_events_after = $feature_data[10];
$expire_images_after = $feature_data[11];
$expire_ads_after_default = $feature_data[12];

// Wether to show the featured ads sidebar
$show_right_sidebar = $feature_data[13];

// Wether to show the ad count near the subcategory and main category
$show_cat_adcount = $feature_data[14];
$show_subcat_adcount = $feature_data[15];

// Maximum size of pictures (in KB)
$pic_maxsize = $feature_data[16];

// Maximum height and width to which pictures uploaded 
// to the images category as well as those attached to
// ads are to be resized
$images_max_width = $feature_data[17];
$images_max_height = $feature_data[18];

// Show preview of ads in category pages. Specify number of characters to show. Set to 0 to disable.
$ad_preview_chars = $feature_data[19];

// Moderation options
$moderate_ads = $feature_data[20];
$moderate_events = $feature_data[21];
$moderate_images = $feature_data[22];

// Paid Promotions //
$enable_promotions = $feature_data[23];

// Enable featured ads
$enable_featured_ads = $feature_data[24];

// Enable extended ads (ads that run longer)
$enable_extended_ads = $feature_data[25];

// Symbol for currency to use for prices. Will display across the website.
$currency = $feature_data[26];

/*********************************************************************/
/* FEATURE DATA ARRAY ENDS HERE
/*********************************************************************/

// Set to true if you would like to use SMTP for sending emails instead of 
// php's mail() function.
$use_smtp = FALSE;

// SMTP host and port. Default values should work on most servers.
$smtp_host = "localhost";
$smtp_port = 25;

// Wether to use SMTP authentication. Most servers do not need authentication.
// If set to true, also provide the SMTP username and password.
$smtp_authenticate = FALSE;
$smtp_username = "username";
$smtp_password = "password";

/*--------------------------------------------------+
| DON'T EDIT ANYTHING BELOW                         |
+--------------------------------------------------*/

// Table names
$tprefix			= "vivaru_";
$t_site_control		= $tprefix . "site_control";
$t_feature_control	= $tprefix . "feature_control";
$t_ratings			= $tprefix . "ratings";
$t_acc_users		= $tprefix . "acc_users";
$t_contact_form_save = $tprefix . "contact_form_save";
$t_countries		= $tprefix . "countries";
$t_cities			= $tprefix . "cities";
$t_areas			= $tprefix . "areas";
$t_cats			= $tprefix . "cats";
$t_subcats		= $tprefix . "subcats";
$t_ads			= $tprefix . "ads";
$t_adpics			= $tprefix . "adpics";
$t_events			= $tprefix . "events";
$t_eventpics		= $tprefix . "eventpics";
$t_subcatxfields	= $tprefix . "subcatxfields";
$t_adxfields		= $tprefix . "adxfields";
$t_imgs				= $tprefix . "imgs";
$t_imgcomments		= $tprefix . "imgcomments";
$t_featured			= $tprefix . "featured";
$t_options_featured = $tprefix . "options_featured";
$t_options_extended	= $tprefix . "options_extended";
$t_promos_featured	= $tprefix . "promos_featured";
$t_promos_extended	= $tprefix . "promos_extended";
$t_payments			= $tprefix . "payments";
$t_ipns				= $tprefix . "ipns";
$t_ipblock			= $tprefix . "ipblock";
$t_comments			= $tprefix . "comments";
$t_likes			= $tprefix . "likes";
// BEGIN account mod table names
$t_users		    = $tprefix . "acc_users";
// END account mod table names
$t_contact_temp		= $tprefix . "contact_temp";
$t_contact_update	= $tprefix . "contact_update";

// Cookie names
$ck_admin			= "vivaru_admin";
$ck_lang			= "vivaru_lang";
$ck_cityid			= "vivaru_cityid";
$ck_edit_adid		= "vivaru_edit_adid";
$ck_edit_isevent	= "vivaru_edit_isevent";
$ck_edit_codemd5	= "vivaru_edit_codemd5";
$ck_admin_theme		= "vivaru_admin_theme";

// Data files
$datafile['badwords'] = "data/badwords.dat";
$datafile['spamfilter'] = "data/spamfilter.dat";

// Directories
//images mod start
$datadir['tempadpics'] = "tempadpics";

//images mod end
$datadir['adpics'] = "adpics";
$datadir['userimgs'] = "userimgs";

// More settings
$vbasedir = "";
$custom_pages = array("terms","privacy","contactus","thankyou","recommend_site","cities.inc","resend");
$encryptposter_sep = ">@<";
$word_wrap_at = 100;
$spam_indicator = $max_abuse_reports >= 99999 ? $max_abuse_reports + 100 : 99999;

// Enumeration for show email option
define ('EMAIL_HIDE',		0);
define ('EMAIL_SHOW',		1);
define ('EMAIL_USEFORM',	2);

// Input sanitization must be done before loading the config.
if (!defined('INIT_DONE')) {
	die("Initialization not done");
}

if(!defined('CONFIG_LOADED'))
{

// Constant to indicate if the config has been loaded
	define('CONFIG_LOADED', TRUE);

// Start output buffering
	ob_start();

// Connect to the database
    $cn = mysql_connect($db_host, $db_user, $db_pass) or die("Cannot connect to DB");
    mysql_select_db($db_name) or die("Error accessing DB");

// Dependancies
require_once("{$path_escape}ipblock.inc.php");
require_once("{$path_escape}paid_cats/mod_config.php");
require_once("{$path_escape}common.inc.php");
require_once("{$path_escape}calendar.cls.php");
}

?>
