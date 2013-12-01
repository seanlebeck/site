<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");

/*echo "<pre>";
echo "GET:\n";
print_r($_GET);
echo "POST:\n";
print_r($_POST);
echo "Files:\n";
print_r($_FILES);
echo "</pre>";*/



if ($_POST['do'] == "import" && $_FILES['file']['tmp_name'] && !$demo)
{
	$lines = explode("\n", file_get_contents($_FILES['file']['tmp_name']));
	$table = array();
	foreach ($lines as $line)
	{
		$line=trim($line);
		if(!$line) continue;
		$table[] = explode("|", $line);
	}

	
	if($_POST['type'] == "cats")
	{
		$catids = array();
		$c = $sc = 0;

		$sql = "SELECT MAX(catid) FROM $t_cats";
		list($nextcat) = mysql_fetch_array(mysql_query($sql));
		$nextcat++;

		$sql = "SELECT MAX(subcatid) FROM $t_subcats";
		list($nextsubcat) = mysql_fetch_array(mysql_query($sql));
		$nextsubcat++;

		foreach ($table as $entry)
		{
			foreach ($entry as $k=>$v) $entry[$k] = mysql_escape_string($v);

			if($catids[$entry[0]])
			{
				$catid = $catids[$entry[0]];
			}
			else
			{
				$sql = "SELECT catid FROM $t_cats WHERE catname = '$entry[0]'";
				list($catid) = @mysql_fetch_row(mysql_query($sql));

				if (!$catid)
				{
					$sql = "INSERT INTO $t_cats SET catname = '$entry[0]', pos = $nextcat, enabled = '1'";
					if(mysql_query($sql)) { $c++; $nextcat++; }
					else echo $sql.mysql_error();

					$sql = "SELECT LAST_INSERT_ID() FROM $t_cats";
					list($catid) = mysql_fetch_row(mysql_query($sql));
				}

				$catids[$entry[0]] = $catid;
	
			}
				
			if($entry[1])
			{
				$sql = "SELECT subcatid FROM $t_subcats WHERE subcatname = '$entry[1]' AND catid = $catid";
				list($subcatid) = @mysql_fetch_row(mysql_query($sql));

				if (!$subcatid)
				{
					$sql = "INSERT INTO $t_subcats SET subcatname = '$entry[1]', catid = $catid, pos = $nextsubcat, expireafter = $expire_ads_after_default, enabled = '1'";
					if(mysql_query($sql)) { $sc++; $nextsubcat++; }
					else echo $sql.mysql_error();
				}

			}

		}

		$msg = "Imported $c categories and $sc subcategories";

	}
	elseif($_POST['type'] == "locs")
	{
		$countryids = $cityids = array();
		$c = $ct = $a = 0;

		$sql = "SELECT MAX(countryid) FROM $t_countries";
		list($nextcountry) = mysql_fetch_array(mysql_query($sql));
		$nextcountry++;

		$sql = "SELECT MAX(cityid) FROM $t_cities";
		list($nextcity) = mysql_fetch_array(mysql_query($sql));
		$nextcity++;

		$sql = "SELECT MAX(areaid) FROM $t_areas";
		list($nextarea) = mysql_fetch_array(mysql_query($sql));
		$nextarea++;

		foreach ($table as $entry)
		{
			foreach ($entry as $k=>$v) $entry[$k] = mysql_escape_string($v);

			if($countryids[$entry[0]])
			{
				$countryid = $countryids[$entry[0]];
			}
			else
			{
				$sql = "SELECT countryid FROM $t_countries WHERE countryname = '$entry[0]'";
				list($countryid) = @mysql_fetch_row(mysql_query($sql));

				if (!$countryid)
				{
					$sql = "INSERT INTO $t_countries SET countryname = '$entry[0]', pos = $nextcountry, enabled = '1'";
					if(mysql_query($sql)) { $c++; $nextcountry++; }
					else print(mysql_error());

					$sql = "SELECT LAST_INSERT_ID() FROM $t_countries";
					list($countryid) = mysql_fetch_row(mysql_query($sql));
				}

				$countryids[$entry[0]] = $countryid;
	
			}
				
			if($entry[1])
			{
				if($cityids[$countryid."|".$entry[1]])
				{
					$cityid = $cityids[$countryid."|".$entry[1]];
				}
				else
				{
					$sql = "SELECT cityid FROM $t_cities WHERE cityname = '$entry[1]' AND countryid = $countryid";
					list($cityid) = @mysql_fetch_row(mysql_query($sql));

					if (!$cityid)
					{
						$sql = "INSERT INTO $t_cities SET cityname = '$entry[1]', countryid = $countryid, pos = $nextcity, enabled = '1'";
						if(mysql_query($sql)) { $ct++; $nextcity++; }
						else print(mysql_error());

						$sql = "SELECT LAST_INSERT_ID() FROM $t_cities";
						list($cityid) = mysql_fetch_row(mysql_query($sql));
					}

					$cityids[$countryid."|".$entry[1]] = $cityid;

				}
			}

			if($entry[2])
			{
				$sql = "SELECT areaid FROM $t_areas WHERE areaname = '$entry[2]' AND cityid = $cityid AND countryid = $countryid";
				list($areaid) = @mysql_fetch_row(mysql_query($sql));

				if (!$areaid)
				{
					$sql = "INSERT INTO $t_areas SET areaname = '$entry[2]', cityid = $cityid, pos = $nextarea, enabled = '1'";
					if(mysql_query($sql)) { $a++; $nextarea++; }
					else print(mysql_error());
				}
			}

		}
		
		$msg = "Imported $c regions, $ct cities and $a areas";
	}
}



if($demo) $err = "This feature is disabled in the demo";

?>
<?php include_once("aheader.inc.php"); ?>

<h2>Import Data</h2>

<?php if($err) { ?><div class="err"><?php echo $err; ?></div><?php } ?>
<?php if($msg) { ?><div class="msg"><?php echo $msg; ?></div><?php } ?>

<br>

<h3>Import Categories</h3>


<form action="" method="post" name="frmImportCats" enctype="multipart/form-data" class="box">

<table><tr><td>File: </td>
<td>
<input type="file" name="file" size="50">
<input type="hidden" name="do" value="import">
<input type="hidden" name="type" value="cats">
<button type="submit">Import</button>
</td></tr>

<tr><td>&nbsp;</td><td>
<br><div>
Uploaded file should contain lines of the form:<br>
category1<br>
category1|subcategory1<br>
category1|subcategory2<br>
category2<br>
...
</div>
</td></tr>
</table>
</form><br>



<h3>Import Locations</h3>

<form action="" method="post" name="frmImportCats" enctype="multipart/form-data" class="box">

<table><tr><td>File: </td>
<td>
<input type="file" name="file" size="50">
<input type="hidden" name="do" value="import">
<input type="hidden" name="type" value="locs">
<button type="submit">Import</button>
</td></tr>

<tr><td>&nbsp;</td><td>
<br><div>
Uploaded file should contain lines of the form:<br>
region1<br>
region1|city1<br>
region1|city2<br>
region1|city2|area1<br>
region2<br>
...
</div>
</td></tr>
</table>
</form><br>


<?php include_once("afooter.inc.php"); ?>