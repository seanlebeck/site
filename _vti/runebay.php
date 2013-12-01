<?php
require_once 'jrunebay1.php';
set_time_limit(0);
ignore_user_abort(1);




function main(){


$rssTextFileLinks = fopen("catidlinksrss.txt", "r");	
$debugmode=1;
$timezone='EDT';
$hostName = "localhost";
$userName = "dojoep_MassDBAD";
$password = "letmein123";
$dbName = "dojoep_MassDB";
//PURGE DB's
mysql_connect($hostName,$userName,$password);
mysql_select_db($dbName) or die("Unable to select database $dbName");
mysql_query('TRUNCATE TABLE phpclassifieds_ads;');
mysql_query('TRUNCATE TABLE phpclassifieds_adpics;');
burnTheImagesFolder();
echo "<b>Images cleared</b>";
burnTheCacheFolder();
echo "<b>Cache cleared</b>";

while(!feof($rssTextFileLinks)){
$rssLink = fgets($rssTextFileLinks);
$rssLink = trim($rssLink);
// Call a new instance of this CLASS below:
$url="http://boston.ebayclassifieds.com$rssLink";
if($url==null){
$url="http://boston.ebayclassifieds.com$rssLink";
echo "Error 404- trying again with url= $url";
}

new AggregateDatabaseData($url,$debugmode,$timezone,$hostName,$userName,$password,$dbName);
//End of call new class

	

}


}

main();

?>