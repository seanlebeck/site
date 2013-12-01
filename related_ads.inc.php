<?php
$words = array(
	'a',
	'about',
	'after',
	'ago',
	'again',
	'all',
	'almost',
	'along',
	'alot',
	'also',
	'am',
	'an',
	'and',
	'answer',
	'any',
	'anybody',
	'anybodys',
	'anywhere',
	'another',
	'are',
	'arent',
	'around',
	'as',
	'ask',
	'askd',
	'at',
	'atleast',
	'bad',
	'be',
	'because',
	'been',
	'before',
	'being',
	'best',
	'better',
	'between',
	'big',
	'both',
	'btw',
	'but',
	'by',
	'can',
	'cant',
	'come',
	'could',
	'couldnt',
	'day',
	'days',
	'days',
	'did',
	'didnt',
	'do',
	'does',
	'doesnt',
	'dont',
	'down',
	'each',
	'etc',
	'either',
	'else',
	'even',
	'ever',
	'every',
	'everybody',
	'everybodys',
	'everyone',
	'far',
	'find',
	'for',
	'found',
	'from',
	'get',
	'go',
	'going',
	'gone',
	'good',
	'got',
	'gotten',
	'guess',
	'had',
	'has',
	'have',
	'havent',
	'having',
	'her',
	'here',
	'hers',
	'him',
	'his',
	'home',
	'how',
	'hows',
	'href',
	'I',
	'Ive',
	'if',
	'in',
	'ini',
	'into',
	'is',
	'isnt',
	'it',
	'its',
	'its',
	'just',
	'know',
	'large',
	'last',
	'less',
	'let',
	'like',
	'liked',
	'little',
	'looking',
	'look',
	'looked',
	'looking',
	'lot',
	'makes',
	'maybe',
	'many',
	'me',
	'more',
	'most',
	'much',
	'must',
	'mustnt',
	'my',
	'near',
	'need',
	'never',
	'new',
	'news',
	'no',
	'none',
	'not',
	'nothing',
	'now',
	'of',
	'off',
	'often',
	'old',
	'on',
	'once',
	'only',
	'oops',
	'or',
	'other',
	'our',
	'ours',
	'out',
	'over',
	'page',
	'please',
	'probably',
	'put',
	'question',
	'questions',
	'questioned',
	'quote',
	'rather',
	'really',
	'recent',
	'said',
	'saw',
	'say',
	'says',
	'she',
	'see',
	'sees',
	'should',
	'sites',
	'small',
	'so',
	'some',
	'something',
	'sometime',
	'somewhere',
	'soon',
	'take',
	'than',
	'true',
	'thank',
	'that',
	'thatd',
	'thats',
	'the',
	'their',
	'theirs',
	'theres',
	'theirs',
	'them',
	'then',
	'there',
	'these',
	'they',
	'theyll',
	'theyd',
	'theyre',
	'this',
	'those',
	'though',
	'through',
	'thus',
	'time',
	'times',
	'to',
	'too',
	'under',
	'until',
	'untrue',
	'up',
	'upon',
	'use',
	'users',
	'version',
	'very',
	'via',
	'want',
	'was',
	'way',
	'we',
	'well',
	'went',
	'were',
	'werent',
	'what',
	'when',
	'where',
	'which',
	'who',
	'whom',
	'whose',
	'why',
	'wide',
	'will',
	'with',
	'within',
	'without',
	'wont',
	'world',
	'worse',
	'worst',
	'would',
	'wrote',
	'www',
	'yes',
	'yet',
	'you',
	'youd',
	'youll',
	'your',
	'youre',
	'yours'
);
$rel_str = $ad['adtitle'];
function word_map($str) 
{
   $str = "/\b".$str."\b/i";
  
   return $str;
}
$field_names = array_map("word_map", $words);
$rel_str = preg_replace("/[^a-zA-Z0-9\s]/", "", $rel_str);
$rel_str = preg_replace($field_names, "", $rel_str);
$rel_str = explode(" ", $rel_str);
$r = 1;
$related_sql = '';
foreach ($rel_str as $value) 
{
	if ( !empty($value) )
	{
		 if ($r == 1)
		 {
		      $related_sql .= "WHERE ( adtitle LIKE '".$value."%'";
		 }
		 $related_sql .= " OR adtitle LIKE '".$value."%'";
		 $r++;
	}
}
$rel_city_sql = ($related_city_limit == 1) ? "AND cityid = '".$ad['cityid']."'" : "";
$rel_cat_sql = ($related_cat_limit == 1) ? "AND subcatid = '".$xsubcatid."'" : "";
$relate_display_limit = "LIMIT $relate_limit";
$rel_sql = "SELECT * FROM $t_ads
		";
//echo '<br>' . $rel_sql;
$rel_res = @mysql_query($rel_sql);
if ( @mysql_num_rows($rel_res) > 0 )
{	
$rel_array = array();
       		
	
	$rel_sql_res = mysql_num_rows($rel_res);
	echo '<ol>';
	while ( $rel_row = mysql_fetch_array($rel_res) )
	{
		
		// Get ad's cat ID
		$cat_sql = "SELECT catid FROM $t_subcats WHERE subcatid = '".$rel_row['subcatid']."'";
		$cat_res = mysql_query($cat_sql);
		$cat_row = mysql_fetch_array($cat_res);
	
		//echo $cat_sql;
		if($sef_urls) 
		{		
			$rel_url = "{$vbasedir}$xcityid/posts/{$cat_row[catid]}/{$rel_row[subcatid]}/$rel_row[adid]-" . RemoveBadURLChars($rel_row['adtitle']) . ".html";
			echo "HERE";
			echo $rel_url;
			print_r($rel_url);
			echo "ENDHERE";
array_push($rel_array, $rel_row[adid]);



		}
		else 
		{
			$rel_url = "?view=$target_view&adid=$rel_row[adid]&cityid=$xcityid&lang=$xlang{$link_extra}";
			echo "HERE";
			echo $rel_url;
			print_r($rel_url);
			echo "ENDHERE";
					array_push($rel_array, $rel_row[adid]);
		}
		//echo '<li><a href="'.$rel_url.'" title="'.$rel_row['adtitle'].'">'.stripslashes($rel_row['adtitle']).'</a></li>';	
	} 
//print_r($rel_array);
$adshuffles = $rel_array;
$adshuffles = array_slice($adshuffles, 0, 20);   // returns "a", "b", and "c"

?><div class="related_ads: id="<?php echo $rel_array['adid'];?>"></div><?php
//print_r($links);
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$count = 0;
$currentIndex = NULL;
foreach($links as $link) {
    if(strpos($link, "/".$currentPage)>-1)  $currentIndex = $count;
    $count++; 
}
if($currentIndex) {
    do{
        $random = mt_rand(0, sizeof($links) - 1);
    } while($random==$currentIndex);
} else {
    $random = mt_rand(0, sizeof($links) - 1);
}
$random_link = $links[$random];
//		echo $random_link;
	echo '</ol></td></tr></table></div>';
	
}
?>