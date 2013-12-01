<?php
    /***
     *  Encode MailAddresses against Spam Bots
     ***/
    function jsEncode($address, $text){
        preg_match('!^(.*)(\?.*)$!',$address,$match);
        if(!empty($match[2])) {
            $smarty->trigger_error("mailto: hex encoding does not work with extra attributes. Try javascript.");
            return;
        }
        $address_encode = '';
        for ($x=0; $x < strlen($address); $x++) {
            if(preg_match('!\w!',$address[$x])) {
                $address_encode .= '%' . bin2hex($address[$x]);
            } else {
                $address_encode .= $address[$x];
            }
        }
        $text_encode = '';
        for ($x=0; $x < strlen($text); $x++) {
            $text_encode .= '&#x' . bin2hex($text[$x]).';';
        }

        $mailto = "&#109;&#97;&#105;&#108;&#116;&#111;&#58;";
        return '<a href="'.$mailto.$address_encode.'" '.$extra.'>'.$text_encode.'</a>';
    }

    /***
     *  Get rid of all HTML in the input
     ***/
    function cleanInput($str){
        return nl2br(htmlspecialchars(strip_tags(trim(urldecode($str)))));
    }


    /***
     *  Make links clickable
     ***/
    function twitterify($ret) {
        $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);
        $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);
        $ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\" rel=\"nofollow\">@\\1</a>", $ret);
        $ret = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\" rel=\"nofollow\">#\\1</a>", $ret);
        return $ret;
    }
    
    /***
     *  Comment Like Text
     ***/
    function commentLikeText($total, $me=true){
        global $lang;
        
        if($me){
            if($total == 0){
                return $lang['youlikethis'];
            }elseif($total == 1){
                return $lang['youandone'];
            }else{
                return str_replace('XXX',$total,$lang['youandxx']);
            }       
        }else{
            if($total == 1){
                return $lang['onelikes'];
            }else{
                return str_replace('XXX',$total,$lang['xxlikethis']);
            }
        }
    }