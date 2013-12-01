<?php


function buildURL($type, $p=array(), $force_qs=FALSE) {
    global $sef_urls, $sef_word_separator, $vbasedir;
    $sep = $sef_word_separator;

    if ($sef_urls && !($force_qs || $type=="custom")) {
        switch ($type)
        {
            case "main":
                $locname = paramText($p[1]);
            	$url = "{$p[0]}{$locname}/";
                break;

            case "ads":
            case "posts":
                $catname    = paramText($p[2]);
                $subcatname = paramText($p[4]);
                $paging     = pageParam($p[5]);
                
               
                $url = "{$p[0]}/posts/" 
                    . (!empty($p[1]) ? "{$p[1]}{$catname}/"    : "")
                    . (!empty($p[3]) ? "{$p[3]}{$subcatname}/" : "")
                    . $paging;
              
                break;

            case "events":
                $paging = pageParam($p[2]);
                $url = "{$p[0]}/events/" . (!empty($p[1]) ? "{$p[1]}/" : "") . $paging;
                break;
                
            case "images":
            case "imgs":
                $paging = pageParam($p[2]);
                $url = "{$p[0]}/images/" . (!empty($p[1]) ? "{$p[1]}/" : "") . $paging;
                break;
                
            case "showad":
            case "showpost":
                $catname    = paramText($p[2]);
                $subcatname = paramText($p[4]);
                $postname   = paramText($p[6]);
                $url = "{$p[0]}/posts/{$p[1]}{$catname}/{$p[3]}{$subcatname}/{$p[5]}{$postname}.html";
                break;
            
            case "showevent":
                $postname = paramText($p[3]);
                $url = "{$p[0]}/events/" . ($p[1] ? "{$p[1]}/" : "") . "{$p[2]}{$postname}.html";
                break;
                
            case "showimage":
            case "showimg":
                $url = "{$p[0]}/images/{$p[1]}/{$p[2]}.html";
                break;
                
            case "rss_ads":
                $url = "feed/" . (isset($p[0]) ? "{$p[0]}/" : "") . "posts/" 
                    . ($p[1] ? "{$p[1]}/" : "") . ($p[2] ? "{$p[2]}/" : "");
                break;

            case "rss_events":
                $url = "feed/" . (isset($p[0]) ? "{$p[0]}/" : "") . "events/" 
                    . ($p[1] ? "{$p[1]}/" : "");
                break;
        }
        
        $url = "{$vbasedir}{$url}";
    
    } else {
        switch ($type)
        {
            case "main":
                $url = "?view=main&cityid={$p[0]}";
                break;
             
            case "ads":
            case "posts":
                $url = "?view=ads&catid={$p[1]}&subcatid={$p[3]}&cityid={$p[0]}" . pageParam($p[5]);
                break;

            case "events":
        	    $url = "?view=events&date={$p[1]}&cityid={$p[0]}" . pageParam($p[2]);
                break;
                
            case "images":
            case "imgs":
                $url = "?view=imgs&posterenc={$p[1]}&cityid={$p[0]}" . pageParam($p[2]);
                break;
                
            case "showad":
            case "showpost":
                if (count($p) == 1) $url = "?view=showad&adid={$p[0]}";
                else $url = "?view=showad&adid={$p[5]}&cityid={$p[0]}";
                break;
                
            case "showevent":
                if (count($p) == 1) $url = "?view=showevent&adid={$p[0]}";
                else $url = "?view=showevent&adid={$p[2]}&cityid={$p[0]}&date={$p[1]}";
                break;
            
            case "showimage":
            case "showimg":
                if (count($p) == 1) $url = "?view=showimg&imgid={$p[0]}";
                else $url = "?view=showimg&imgid={$p[2]}&cityid={$p[0]}&posterenc={$p[1]}";
                break;
                
            case "rss_ads":
                $url = "rss.php?view=ads&cityid={$p[0]}&catid=$p[1]&subcatid=$p[2]";
                break;

            case "rss_events":
                $url = "rss.php?view=events&cityid={$p[0]}&date={$p[1]}";
                break;
                
            case "custom":
                if (is_array($p)) {
                    $url = "";
                    if (count($p) > 0) {
                        foreach ($p as $k=>$v) {
                            $url .= "&{$k}={$v}";
                        }
                        $url{0} = "?";
                    }
                } else {
                    $url = $p;
                }
                break;

        }
        
        if ($url{0} == "?") $url = "index.php{$url}";
    }
        
    return $url;
             
}

function pageParam($page, $force_qs=FALSE) {
    global $sef_urls;
    $pageparam = "";
    
    if (!empty($page)) {
        if ($sef_urls && !$force_qs) {
            $pageparam =  "page{$page}.html";
        
        } else {
            $pageparam = "&page=$page";
        }
    }
    
    return $pageparam;
}

function paramText($text) {
    global $sef_word_separator;
    return (!empty($text) ? $sef_word_separator . RemoveBadURLChars(langcheck($text)) : "");
}

?>