<?php
# CONFIGURATIONS BEGIN

# list the language tags separated with comma
define('LANGCHECK_LANGS','en');

# list the language images separated with comma
# Set in same order as specified above
define('LANGCHECK_LANGIMAGES','images/flags/gb.gif');

# list the language names separated with comma
define('LANGCHECK_LANGNAMES','English');

## DON'T EDIT NEXT LINE ##
# list language - accept_charset patterns (perl regex) separated with comma
define('LANGCHECK_ACCEPT_CHARSET_REGEXES',',/shift_jis/i');

# list language - accept_language patterns (perl regex) separated with comma
define('LANGCHECK_ACCEPT_LANGUAGE_REGEXES','/^en/');

# charset in Content-Type separated with comma (only for fastestcache)
define('LANGCHECK_CHARSETS','utf-8');

# tag name for language image  (default [langimgs]. don't include specialchars)
# If you change this, you need to change your [langimgs] tag in your header.inc.php file (or anywhere else)
define('LANGCHECK_IMAGETAG','langimgs');

## DON'T EDIT NEXT LINE ##
# make regular expression which disallows language tags to cross it
define('LANGCHECK_NEVERCROSSREGEX','/\<\/table\>/');

# the life time of language selection stored in cookie
# 365*86400 = 1 year. (365 = amount of days, 86400 = 1 day, equals 365 * 1)
# If you want to change to less days, only change the value '365' to, i.e. 14
# equals to 14 * 1 = 14 days
define('LANGCHECK_COOKIELIFETIME',365*86400);

# CONFIGURATIONS END


// Patch check

        $xzero_cookie_path = '/' ;
	// deciding the current language (the priority is important)
	$langcheck_langs = explode( ',' , LANGCHECK_LANGS ) ;
	$langcheck_charsets = explode( ',' , LANGCHECK_CHARSETS ) ;
	if( ! empty( $_GET['lang'] ) && $_GET['lang'] == 'all' ) {
		// set by GET (all)
		$langcheck_lang = 'all' ;
	} else if( ! empty( $_GET['lang'] ) && ( $offset = array_search( $_GET['lang'] , $langcheck_langs ) ) !== false ) {
		// set by GET (other than all)
		$langcheck_lang = $_GET['lang'] ;
		$langcheck_charset = $langcheck_charsets[ $offset ] ;
		setcookie( 'langcheck_lang' , $langcheck_lang , time() + LANGCHECK_COOKIELIFETIME , $xzero_cookie_path, '' , 0 ) ;
	} else if( ! empty( $_COOKIE['langcheck_lang'] ) && ( $offset = array_search( $_COOKIE['langcheck_lang'] , $langcheck_langs ) ) !== false ) {
		// set by COOKIE (other than all)
		$langcheck_lang = $_COOKIE['langcheck_lang'] ;
		$langcheck_charset = $langcheck_charsets[ $offset ] ;
	} else {
		$langcheck_lang = $language ;
		$langcheck_charset = $langcheck_charsets[0] ;
	}

	// charset for Content-Type

	ob_start( 'langcheck' ) ;
//}




// ob filter
function langcheck( $s )
{
	global $langcheck_lang , $xoopsUser ;

	// all mode for debug (allowed to system admin only)
	if( is_object( $xoopsUser ) && $xoopsUser->isAdmin(1) && ! empty( $_GET['lang'] ) && $_GET['lang'] == 'all' ) {
		return $s ;
	}

	$langcheck_langs = explode( ',' , LANGCHECK_LANGS ) ;
	// protection against some injection
	if( ! in_array( $langcheck_lang , $langcheck_langs ) ) {
		$langcheck_lang = $langcheck_langs[0] ;
	}

	// escape brackets inside of <input type="text" value="...">
//	$s = preg_replace_callback( '/(\<input)(?=.*type\=[\'\"]?text[\'\"]?)([^>]*)(\>)/isU' , 'langcheck_escape_bracket' , $s ) ;
	$s = preg_replace_callback( '/(\<input)([^>]*)(\>)/isU' , 'langcheck_escape_bracket_textbox' , $s ) ;

	// escape brackets inside of <textarea></textarea>
	$s = preg_replace_callback( '/(\<textarea[^>]*\>)(.*)(<\/textarea\>)/isU' , 'langcheck_escape_bracket_textarea' , $s ) ;

	// multilanguage image tag
	$langimages = explode( ',' , LANGCHECK_LANGIMAGES ) ;
	$langnames = explode( ',' , LANGCHECK_LANGNAMES ) ;
	
	if( empty( $_SERVER['QUERY_STRING'] ) ) {
		$link_base = '?lang=' ;
	} else if( ( $pos = strpos($_SERVER['QUERY_STRING'],'langcheck_lang=') ) === false ) {
		$link_base = '?' . htmlspecialchars($_SERVER['QUERY_STRING'],ENT_QUOTES) . '&amp;lang=' ;
	} else if( $pos < 2 ) {
		$link_base = '?lang=' ;
	} else {
		$link_base = '?' . htmlspecialchars(substr($_SERVER['QUERY_STRING'],0,$pos-1),ENT_QUOTES) . '&amp;lang=' ;
	}
	$langimage_html = '' ;

	foreach( $langcheck_langs as $l => $lang ) {
		$langimage_html .= '<a href="'.$link_base.$lang.'"><img src="'.$langimages[$l].'" alt="flag" title="'.$langnames[$l].'" border="0" width="16" height="11"></a>&nbsp;&nbsp;&nbsp;' ;
	}
	$s = preg_replace( '/\['.LANGCHECK_IMAGETAG.'\]/' , $langimage_html , $s ) ;

	// create the pattern between language tags
	//$pqhtmltags = explode( ',' , preg_quote( langcheck_NEVERCROSSTAGS , '/' ) ) ;
	//$mid_pattern = '(?:(?!(' . implode( '|' , $pqhtmltags ) . ')).)*' ;

	// eliminate description between the other language tags.
	foreach( $langcheck_langs as $lang ) {
		if( $langcheck_lang == $lang ) continue ;
		$s = preg_replace_callback( '/\['.preg_quote($lang).'\].*\[\/'.preg_quote($lang).'(?:\]\<br \/\>|\])/isU' , 'langcheck_check_nevercross' , $s ) ;
	}


	// simple pattern to strip selected lang_tags (remove all tags)
	$s = preg_replace( '/\[\/?'.preg_quote($langcheck_lang).'\](\<br \/\>)?/i' , '' , $s ) ;

	// much complex pattern to strip valid pair of selected lag_tags (BUGGY?)
	// $s = str_replace( '['.$langcheck_lang.']<br />' , '['.$langcheck_lang.']' , $s ) ;
	// $s = str_replace( '[/'.$langcheck_lang.']<br />' , '[/'.$langcheck_lang.']' , $s ) ;
	// $s = preg_replace( '/(\['.preg_quote($langcheck_lang).'\])('.$mid_pattern.')(\[\/'.preg_quote($langcheck_lang).'\])/isU' , '$2' , $s ) ;

	/* list($usec, $sec) = explode(" ",microtime());
	$GIJ_end_time = ((float)$sec + (float)$usec); 
	error_log( ($GIJ_end_time - $GLOBALS['GIJ_start_time']) . "(sec)\n" , 3 , "/tmp/error_log" ) ; */

	return $s ;
}


function langcheck_escape_bracket_textbox( $matches )
{
	if( preg_match( '/type=["\']?text["\']?/i' , $matches[2] ) ) {
		return $matches[1].str_replace('[','&#91;',$matches[2]).$matches[3] ;
	} else {
		return $matches[1].$matches[2].$matches[3] ;
	}
}

function langcheck_escape_bracket_textarea( $matches )
{
	return $matches[1].str_replace('[','&#91;',$matches[2]).$matches[3] ;
}

function langcheck_check_nevercross( $matches )
{
	return preg_match( LANGCHECK_NEVERCROSSREGEX , $matches[0] ) ? $matches[0] : '' ;
}

?>
