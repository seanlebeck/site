<?php


// Enable account mod.  Default: 1 for on, enter 0 to disable
$enable_account = 1;

// No email activation for logged in users.  Users do not have to activate ads via email to post when logged in. 
// Default 1, 0 to disable.
$no_email_logged_in = 1;

// Email used to send automated emails from your site
$main_acc_email = $site_data[1]; 

// Cookie expire time.  DEFAULT 3600 (1 hour), 86400 = 1 day.  You can add more seconds to extend
$cookie_expire_time = 86400;

// Restrict the cookie to a directory.  Example:  /classifieds/
// If you want cookie saved on the whole domain, use /
$cookie_path = '/';

// Domain for cookie to make it more compatiable with user browsers.  Leave out http://www, just used .example.com
// IMPORTANT: Don't forgot the first dot before the domain name. Like .vivaru.com or .google.com
$cookie_domain = $site_data[17];

// Disable site for users for construction
$acc_construction = 0; 

// Only allow users to use numbers and letters for usernames.  1 = enabled, 0 = letters only
$only_num_letters = 1;

// Max Avatar filesize (size is in bytes) Default: 55000
$file_size = 55000; 

// Max avatar height and width
$avatar_max_height = 150;
$avatar_max_width = 150;

// User email activation on signup. If disabled, users can signup without having to verfiy email address.
$user_email_activation = $site_data[18];

// Pagination Values
$acc_ads_per_page = 10; // limit how many user ads to appear on each user panel page
$acc_evs_per_page = 10; // limit how many user ads to appear on each user panel page

// Allow users to login by username instead of email.  Default: 0
$username_login_only = 1; 

// Force users to login first before they can post. 1 to enable. Default 0
$force_user_login_post = $site_data[19];

/************************************
*                                   *
*  NO NEED TO EDIT BELOW THIS LINE  *
*                                   * 
************************************/

$no_captcha_logged_in = 0;


// 1 = ON, 0 = OFF

define("DEBUG", 0);


// Link to go back to previous page

$go_back = '<br /><br /><a href="javascript:history.go(-1)">Click here to go back</a>';


// Define user level access

define('NORMAL', 0);
define('OWNER', 1);
define('ADMIN', 2);
define('MODERATOR', 3);

// Account Cookie names

$ck_username = 'username';
$ck_session = 'session';
$ck_userid = 'userid';

// Mod rewrite links (If changed, you would have to update links in .htaccess file too.

$acc_panel_link = ($sef_urls) ? 'account.html' : 'index.php?view=userpanel';
$acc_login_link = ($sef_urls) ? 'login.html' : 'index.php?view=login';
$acc_signup_link = ($sef_urls) ? 'signup.html' : 'index.php?view=signup';



// Path to includes directory 

$inc_path = 'includes';

// Avatar path 

$avatar_dir = "images/avatars";


// Back path

$back_path = '../';


// Site path

$site_path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . '/';


include_once($inc_path . "/functions.php");



// This displays KB version of avatar size from bytes

$file_avatar_kb = round(($file_size / 1024), 2); // bytes to KB 



if ( DEBUG )
{
	echo '<p>DEBUG mode is <b>ON</b></p>';
	error_reporting(E_ERROR);
}

if ( $main_acc_email == 'sean@shufflebuy.com' && $cookie_domain == '.shufflebuy.com' )
{
	// This message appears if you do not change your account email or cookie domain.
	echo "You need to configure account options. Please edit file acc_config.php located in /accounts/ directory.";
}



?>