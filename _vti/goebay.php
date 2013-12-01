<?php
set_time_limit(0);
//functions include
require 'jrunebay1.php';

//Call main
main();



function main(){
$rssTextFileLinks = fopen("catidlinksrss.txt", "r");	
$ebayCatIdsFile = fopen("ebayCatIds.txt", "r");	
$debugmode=1;
$timezone='EDT';
$hostName = 'localhost';
$userName = 'dojoep_dojoep';
$password = '5s[NqPUt[AR9';
$dbName = 'dojoep_newhampshire';
$numberOfCats = 112;
//Setup counter for incrementing the arrays
$counter=0;
//PURGE DB's
mysql_connect($hostName,$userName,$password);
mysql_select_db($dbName) or die("Unable to select database $dbName");
mysql_query('TRUNCATE TABLE vivaru_ads;');
mysql_query('TRUNCATE TABLE vivaru_adpics;');
burnTheImagesFolder();
echo "<b>Images cleared</b>";
burnTheCacheFolder();
echo "<b>Cache cleared</b>";


//Create arrays
//////////////////file 1 below (cat ids txt file)
$catIds = array();
while (!feof($ebayCatIdsFile)) {
   $catIds[] = fgets($ebayCatIdsFile);
}
fclose($ebayCatIdsFile);
//////////file 2 below (cat rss)
$rssTxt  = array();
while (!feof($rssTextFileLinks )) {
   $rssTxt[] = fgets($rssTextFileLinks);
}
fclose($rssTextFileLinks );
//End of arrays
//////////////////Run arrays:
for($h=0;$h<$numberOfCats;$h++){
$rssLink = trim($rssTxt[$counter]);
$category = trim($catIds[$counter]);
//Check for null files
if($rssLink==null){continue;}
if($category==null){continue;}
$url='http://boston.ebayclassifieds.com'.$rssLink;
 // Call a new instance of 'AggregateDatabaseData' CLASS below:
new AggregateDatabaseData($url,$debugmode,$timezone,$hostName,$userName,$password,$dbName, $category);
//Increment the counter +1
$counter++;	
}

}



?>