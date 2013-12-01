<?php
set_time_limit(0);
//functions include
require 'jruntigerdirect1.php';

//Call main
main();



function main(){
$debugmode=1;
$timezone='EDT';
$hostName = 'localhost';
$userName = 'dojoep_MassDBAD';
$password = 'letmein123';
$dbName = 'dojoep_MassDB';
$numberOfCats = 112;
//Setup counter for incrementing the arrays
$counter=0;
//PURGE DB's
mysql_connect($hostName,$userName,$password);
mysql_select_db($dbName) or die("Unable to select database $dbName");
mysql_query('TRUNCATE TABLE phpclassifieds_ads;');
mysql_query('TRUNCATE TABLE phpclassifieds_adpics;');
//burnTheImagesFolder();
//burnTheCacheFolder();



$url='http://www.tigerdirect.com/xml/rsstigercat34.xml';
 // Call a new instance of 'AggregateDatabaseData' CLASS below:
new AggregateDatabaseData($url,$debugmode,$timezone,$hostName,$userName,$password,$dbName, $category);
//Increment the counter +1
	


}



?>