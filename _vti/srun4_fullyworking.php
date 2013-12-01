
<?php


$cachedir = "cache/";
if ($cachehandle = opendir($cachedir)) {
while (false !== ($file = readdir($cachehandle))) {
if ($file != "." && $file != "..") {
$file2del = $cachedir."/".$file;
unlink($file2del);
echo "<b>Cache cleared</b>";
echo "<br>";
}
}
closedir($cachehandle);
}




$debugmode=1;
$defaultcity="31";
$timezone='EDT';
$positionofuniqueid=6;
$hostName = "localhost";
$userName = "dojoep_dojoep";
$password = "5s[NqPUt[AR9";
$dbName = "dojoep_newhampshire";
///RSS retrieval
 require_once('/home/dojoep/public_html/_vti/magpierss/rss_fetch.inc');
	//$url = $_GET['url'];
	$url="http://boston.ebayclassifieds.com/antiques/?catId=100012&output=rss";
	$rss = fetch_rss( $url );
	$myFilewrite = "../links.php";
	$fw = fopen($myFilewrite, 'w') or die("can't open file");
	//echo "Channel Title: " . $rss->channel['title'] . "<p>";
	//echo "<ul>";
	foreach ($rss->items as $item) {
		$href = $item['link'];
		$title = $item['title'];
		fwrite($fw, $href."\n");
	//echo "<li><a href=$href>$title</a></li>";
	}
	//echo "</ul>";
fclose($fw);
//RSS end
mysql_connect($hostName, $userName, $password) or die("Unable to connect to host $hostName");
mysql_select_db($dbName) or die("Unable to select database $dbName");
$myFile="../links.php";
$fh = fopen($myFile, 'r');
// create a new curl resource
while ((feof ($fh) === false)){
$ch = curl_init();
$link = fgets($fh);
$chunk = explode("/", $link);
$uniqueid=rtrim($chunk[$positionofuniqueid]);
$uniqueid = str_replace("?ad=", "", $uniqueid);
echo "<br>";
echo "<b>this is link: </b>",$link;
echo "<br>";
echo "<b>this is unique id: </b>",$uniqueid;

//uncomment
if($debugmode==0){
	if($num_rows>0)
	{
        echo "select * from jobs where uniqueid='".$uniqueid."'";
        echo "<h3>Duplicate entry.Program Terminated</h3>";
				exit();
	}
}

//print_r($chunk);
$city=getcity($link);
echo "<br>";
echo "<b>This is city: </b>",$city;
echo "<br>";
//$chunk[3];city
//$chunk[4];class
// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// grab URL, and return output
$output = curl_exec($ch);
// close curl resource, and free up system resources
curl_close($ch);
// Print output
//echo $output;ag
$title = substring_between($output,'<title>','</title>');
$title = preg_replace('/\'/','', $title);
$title = preg_replace('/\"/','', $title);
$title = strip_tags($title);

$images = substring_between($output,"urlSelect : '[","]',"); 

//echo "<b>HERE is image string</b>",$images;
//echo "<br>";
//////ebay does not need sanitized if using above substring_between
$images = str_replace(array('\'', '"'), '', $images);
$images = str_replace(array('\'', ']'), '', $images);
$images = str_replace(array('\'', '['), '', $images);
//echo "<br>";
//echo "<b>HERE is image string after sanitation</b>",$images;
//echo "<br>";
$images = explode(',', $images);


/////////////////////////////////////////////////////////////////////////////////////////////////////

foreach ($images as $image) {
	   $path = $image;
       $picfile = basename($path); 
       echo "<b>This is name of picture </b>",$picfile;
	   echo "<br>";
   
	

}
////////////////////////////////////JOE ABOVE SECTION WILL WRITE THE FILE NAMES (STRIPPING OUT URL) TO PHPCLASSIFIEDS_ADPICS....THE adid IS WAAAAY BELOW
//////////// AT THE BOTTOM...THE adid FOR IMAGE MUST MATCH THE PHPCLASSIFIEDS_ADS IN ORDER TO SHOW THUMBNAIL.....BUT THE adid IS NOT GENERATED UNTIL WE INSERT
///// THE ACTUAL ADS. 




////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//if title is empty or it is removed by craigslist
if($title==''||preg_match("/this posting/i", $title)){
	continue;
}
$varstrings = substring_between($output,">Price:<","listlabel");
//echo "this is the pattern",$varstrings;
$pattern = '/(\$[0-9,]+(\.[0-9]{2})?)/';
preg_match($pattern, $varstrings, $price);
$price = str_replace(array('\'', ','), '', $price);

echo "<b>title is:</b> ".$title;
echo "<br>";
echo "<b>price is:</b> ".$price[0];
$price=substr($price[0], 1);
//$content=substring_between($output,"$timezone<br>",'</table>');
//echo "<br>This is supposed to be content ".$content."<br>";
$content=substring_between($output,'>Description:<','ad-details-links');
$content = str_replace("/span>", "", $content);
$content = strip_tags($content);
echo "<b>content is: </b>".$content;
$creationdate=substring_between($output,'Date Posted:</span><span class="listvalue">','Description:');

echo "<br>";
echo "<b>This is creation date: </b>",$creationdate;

//$creationdate = explode(">", $creationdate);
//$creationdate=$creationdate[1];

$nextmonth=strtotime("now")+2592000;
$expirationdate=date( 'Y-m-d',$nextmonth);
$modifieddatedate=$creationdate;
$content = preg_replace('/\'/','', $content);
//$content = preg_replace('#</?br[^>]*>#is', '', $content);
$content = strip_tags($content);




//EMAIL SECTION FOR NON CRAIGSLIST SITES
echo "<br>";
echo "MY email is ".$email;
echo "<br>";
//$email= substring_between($output,'Reply to:</span> <a href="mailto:','<br>');
//echo "THE EMAIL IS NEEDED HERE 1".$email;
$email= substring_between($email,'>','</a>');
//echo "THE EMAIL IS NEEDED HERE2".$email;
$email= html_entity_decode($email);
//$email = "Ad Located On Craigslist";
//echo "<br>";
echo "<b>This is email: </b>",$email;
//echo "<br>";

if($email){
	$apply_online=1;
}
else{
	$apply_online=0;
}
$category=getcategory($link);
echo "<b>This is category: </b>",$category;
//find the keyword in the string
  $string = $email;
    $pos = strpos($string, "craigslist.org");
	
     if ($pos  === false)
	{
		echo "<br>";
		print "Keyword search: Keyword not found in string! Unique email added!
		<br />";
	

		
$query = "INSERT into phpclassifieds_ads
(adtitle,addesc,area,email,showemail,password,code,cityid,subcatid,price,othercontactok,hits,ip,verified,abused,enabled,createdon,expireson,timestamp)
VALUES
('".$title."','".$content."','".$city."','".$email."','2','','117.198.242.244.4ab4c865b71db','".$city."','".$category."','".$price."','',0,'','1',0,'1',NOW(),NOW()+INTERVAL 30 DAY,NOW());
";
	//echo $query;
	$result = mysql_query($query);
	$id=mysql_insert_id();
if (!$result) {
	die('Invalid query: ' . mysql_error());
			  }
    }  else 
				{
		print "<b>----nope, this one didnt work...-----</b><br />";
		}
	$address= substring_between($output,'<li> Location: ','<li> it');
	
echo "This is the adid: ",$id;	

foreach ($images as $image) {	
if(!@file_get_contents($image))
{
    echo 'Couldn´t open image sorry!!';
}
else
{
$picfile = basename($image); 
$fileContent = file_get_contents($image);
//echo "here is the image",$image;
echo 'Saving image: '.$picfile;
file_put_contents('/home/dojoep/public_html/adpics/'.$picfile, $fileContent);
 	$sql1 = "INSERT into phpclassifieds_adpics (adid,picfile) VALUES ('".$id."','".$picfile."')";
mysql_query($sql1);
}
}
}



mysql_close();





//function to get a substring between between two other substrings
function substring_between($haystack,$start,$end) {
   if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) {
       return false;
   } else {
       $start_position = strpos($haystack,$start)+strlen($start);
       $end_position = strpos($haystack,$end);
       return substr($haystack,$start_position,$end_position-$start_position);
   }
}
function getuniqueid($positionofuniqueid) {
}
function getcategory($link){
global $defaultcategory;
$chunk = explode("/", $link);
switch ($chunk[5]) {
case 'act':
    $category=42;
    break;
	default:
		$category=42;
		break;
}
	return $category;
}
function getcity($link){
global $defaultcity;
$chunk = explode("/", $link);   $city=1;	return $city;





}



	return $city;
	


 









?>
