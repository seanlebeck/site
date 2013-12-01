<?php

if (!defined("INIT_DONE")) {

	function handle_security_attack($msg="&laquo;") {
		die($msg);
	}

    function check_variable_injection($varname, &$var, $strict) {
        if(isset($var)) {
			if ($strict) {
				if (isset($_REQUEST[$varname]) || isset($_GET[$varname])
						|| isset($_POST[$varname]) || isset($_COOKIE[$varname])) {
					handle_security_attack();
				}
			} else {
	            if ((isset($_REQUEST[$varname]) && $var == $_REQUEST[$varname])
						|| (isset($_GET[$varname]) && $var == $_GET[$varname])
						|| (isset($_POST[$varname]) && $var == $_POST[$varname])
						|| (isset($_COOKIE[$varname]) && $var == $_COOKIE[$varname])) {
	                handle_security_attack();
	            }
			}
        } else {
            $var = "";
        }
    } 

	function check_numeric($var) {
		if(isset($var)) {
			if ($var == "") {
				$var = 0;
			} else if(strval(0+$var) !== "{$var}")  {
				handle_security_attack();
			}
		}
	}

	function check_numeric_parameter($var) {
		if(isset($_GET[$var])) check_numeric($_GET[$var]);
		if(isset($_POST[$var])) check_numeric($_POST[$var]);
		if(isset($_COOKIE[$var])) check_numeric($_COOKIE[$var]);
		if(isset($_REQUEST[$var])) check_numeric($_REQUEST[$var]);
	}

	function numerize(&$var) {
		if(isset($var)) $var += 0;
	}

	function numerize_parameter($var) {
		if(isset($_GET[$var])) numerize($_GET[$var]);
		if(isset($_POST[$var])) numerize($_POST[$var]);
		if(isset($_COOKIE[$var])) numerize($_COOKIE[$var]);
		if(isset($_REQUEST[$var])) numerize($_REQUEST[$var]);
	}
    
    $xcatid = "";
    $xsubcatid = "";
    $xcityid = "";
    $xcountryid = "";
    $xadid = "";
    $xdate = "";
    $xpostmode = "";
    $specialdates = "";
    $xcatname = "";
    $xsubcatname = "";
    $xsubcathasprice = FALSE;
    $xsubcatfields = array();
    $syndicate = FALSE;
    $msg = "";
    $err = "";
    $title_extra = "";
    $in_admin = FALSE;
    $admin_mode = FALSE;
    
    $postable_country = FALSE;
    $postable_category = FALSE;
   
    //$path_escape = "";

	// Some more sanitization
    check_variable_injection("path_escape", $path_escape, TRUE);

	check_numeric_parameter("cityid");
	check_numeric_parameter("catid");
	check_numeric_parameter("subcatid");
	check_numeric_parameter("adid");
	check_numeric_parameter("imgid");
	check_numeric_parameter("countryid");
	check_numeric_parameter("areaid");
	check_numeric_parameter("pos");
	check_numeric_parameter("picid");
	check_numeric_parameter("page");
	check_numeric_parameter("foptid");
	check_numeric_parameter("eoptid");
	check_numeric_parameter("isevent");
	
    check_numeric_parameter("shortcutcat");
    check_numeric_parameter("shortcutregion");
   

	numerize_parameter("pricemin");
	numerize_parameter("pricemax");
    
    define("INIT_DONE", TRUE);
}

?>