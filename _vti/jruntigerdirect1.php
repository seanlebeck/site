<?php

set_time_limit(0);

//include ('/home/dojoep/public_html/_vti/config.php');
class AggregateDatabaseData{
function AggregateDatabaseData($url,$debugmode,$timezone,$hostName,$userName,$password,$dbName, $category){
echo $category;
echo "<br>";
curlResourceJoe($url,$debugmode,$timezone,$hostName,$userName,$password,$dbName, $category);
}//end constructor 
}//end class 


///////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////NEW FUNCTION:
//Sean i replace old php with functions that can be called in a class:
///////////////////////////////////////////////////////////////////////////////////////////////////////////////


function curlResourceJoe($url,$debugmode,$timezone,$hostName,$userName,$password,$dbName, $category){
  echo "Running RSS: $url";
	//sean start ============================================================================================
	
$positionofuniqueid=6;

///RSS retrieval
 require_once('magpierss/rss_fetch.inc');
	//$url = $_GET['url'];
	$rss = fetch_rss( $url );
	$myFilewrite = "../links.php";
	$fw = fopen($myFilewrite, 'w') or die("can't open file");
	
	foreach ($rss->items as $item) {
		$href = $item['link'];
		$title = $item['title'];
		fwrite($fw, $href."\n");
	
	}
	
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



if($debugmode==0){
	if($num_rows>0)
	{
       // echo "select * from jobs where uniqueid='".$uniqueid."'";
      //  echo "<h3>Duplicate entry.Program Terminated</h3>";
				exit();
	}
}


$city=getcity($link);
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
$images = substring_between($output,"http://images.'[","]',.jpg\"/>"); 

//echo "<b>HERE is image string</b>",$images;

//////ebay does not need ripping if using above substring_between craigs....ya fuck that
$images = str_replace(array('\'', '"'), '', $images);
$images = str_replace(array('\'', ']'), '', $images);
$images = str_replace(array('\'', '['), '', $images);
$images = explode(',', $images);


/////////////////////////////////////////////////////////////////////////////////////////////////////

foreach ($images as $image) {
	   $path = $image;
       $picfile = basename($path); 
      // echo "<b>This is name of picture </b>",$picfile;
	 //  echo "<br>";
   
	

}




////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//if title is empty
if($title==''||preg_match("/this posting/i", $title)){
	continue;
}
$varstrings = substring_between($output,">Price:<","listlabel");
//echo "this is the pattern",$varstrings;
$pattern = '/(\$[0-9,]+(\.[0-9]{2})?)/';
preg_match($pattern, $varstrings, $price);
$price = str_replace(array('\'', ','), '', $price);
//echo "<b>title is:</b> ".$title;
//echo "<br>";
//echo "<b>price is:</b> ".$price[0];
$price=substr($price[0], 1);
$content=substring_between($output,"$timezone<br>",'</table>');
$content=substring_between($output,'>Description:<','ad-details-links');
$content = str_replace("/span>", "", $content);
$content = strip_tags($content);


$nextmonth=strtotime("now")+2592000;
$expirationdate=date( 'Y-m-d',$nextmonth);
$content = preg_replace('/\'/','', $content);
$email= substring_between($email,'>','</a>');

$email= html_entity_decode($email);

 
if($email){
	$apply_online=1;
}
else{
	$apply_online=0;
}
  $string = $email;
    $pos = strpos($string, "craigslist.org");
	
     if ($pos  === false)
	{
		//echo "<br>";
		//print "Keyword search: Keyword not found in string! Unique email added!
		//<br />";
	

		
$query = "INSERT into phpclassifieds_ads
(pricesort,adtype,adlink,adsrc,adtitle,addesc,area,email,showemail,password,code,cityid,subcatid,price,othercontactok,hits,ip,verified,abused,enabled,createdon,expireson,timestamp)
VALUES
('".$price."','localtab.png','".$plink."','TigerDirect','".$title."','".$content."','".$city."','".$email."','2','','117.198.242.244.4ab4c865b71db','".$city."','".$category."','".$price."','',0,'','1',0,'1',NOW(),NOW()+INTERVAL 30 DAY,NOW());
";
	////echo $query;
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
	
//echo "This is the adid: ",$id;	
///WHATS WRONG HERE?
foreach ($images as $image) {	
if(!@file_get_contents($image))
{
    //echo 'Couldn´t open image sorry!!';
}
else
{ 
$picfile = basename($image); 
$fileContent = file_get_contents($image);
file_put_contents('../adpics/'.$picfile, $fileContent);
 	$sql1 = "INSERT into phpclassifieds_adpics (adid,picfile) VALUES ('".$id."','".$picfile."')";
     
mysql_query($sql1);

}
}

}
	mysql_close();
	  fclose($fh); // Added this line
}



function burnTheCacheFolder(){
$cachedir = "cache/";
if ($cachehandle = opendir($cachedir)) {
while (false !== ($file = readdir($cachehandle))) {
if ($file != "." && $file != "..") {
$file2del = $cachedir."/".$file;
unlink($file2del);
//
//
}
}
closedir($cachehandle);
} 
echo "<b>Cache cleared</b>";
echo "<br>";
}
//

function burnTheImagesFolder(){
$imagedir = "../adpics/";
if ($imagehandle = opendir($imagedir)) {
while (false !== ($file = readdir($imagehandle))) {
if ($file != "." && $file != "..") {
$file2del = $imagedir."/".$file;
unlink($file2del);
//
//
}
}
closedir($imagehandle);
echo "<b>Images cleared</b>";
echo "<br>";
} 
}

//



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

//EDIT ME////////////////?????????????????????????????????????????????????????????????????????????


	



function &getcity($link){
$chunk = explode("/", $link);
  $city=1;
  return $city;


}

?>


