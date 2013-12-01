<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");

$xsubcatfields = array();

$adid = $_REQUEST['adid'];
$isevent = 0+$_REQUEST['isevent'];

if($isevent) 
{
	$ad_section = "Events";
	$adtype = "E";
	$table = $t_events;
}
else
{
	$ad_section = "Ads";
	$adtype = "A";
	$table = $t_ads;
}

if(!$adid)
{
	header("Location: ads.php");
	exit;
}


if ($_POST['do'] == "post")
{
	$data = $_POST;
	recurse($data, 'stripslashes');
	
	if($_POST['subcatid'] > 0)
	{
		$xsubcatid = $_POST['subcatid'];
		list($xsubcathasprice, $xsubcatpricelabel, $xsubcatfields) = GetCustomFields($xsubcatid);
	}

	
	$expireson_temp = "{$data['exp_y']}-{$data['exp_m']}-{$data['exp_d']}" . substr($data['expireson_orig'], 10);
	$data['expireson'] = date("Y-m-d H:i:s", strtotime($expireson_temp));
	

	// Assume values
	if(!$data['adtitle'])
	{
		$data['adtitle'] = substr($data['addesc'], 0, $generated_adtitle_length) . ((strlen($data['addesc']) > $generated_adtitle_length) ? $generated_adtitle_append : "");
	}
	if(!$data['email']) $data['showemail'] = EMAIL_HIDE;


	// Check errors
	if (!$_POST['addesc'])
		$err .= "&bull; The ad title, description and poster email are mandatory";
	
	$numerr = "";
	if($_POST['price'] && !preg_match("/^[0-9\.]*$/", $_POST['price'])) 
		$numerr .= "- $xsubcatpricelabel<br>";

	if(is_array($data['x']))
	{
		foreach ($data['x'] as $fldnum=>$val)
		{
			if($xsubcatfields[$fldnum]['TYPE'] == "N" && !preg_match("/^-?[0-9]*$/", $val))
			{
				$fldname = $xsubcatfields[$fldnum]['NAME'];
				$numerr .= " - {$fldname}<br>";
			}
		}
	}

	if($numerr) $err .= "&bull; The following fields must be numbers<br>$numerr";
}


if ($_POST['do'] == "post" && !$err)
{
	$data_mysql = $data;

	foreach ($data_mysql as $k=>$v)
	{
		if ($k == "addesc") {
			recurse($data_mysql[$k], 'htmlspecialchars');
			recurse($data_mysql[$k], 'mysql_escape_string');
		}
		else {
			recurse($data_mysql[$k], 'htmlspecialchars');
			recurse($data_mysql[$k], 'mysql_escape_string');
		}
	}

	if(is_array($data_mysql['x']))
	{
		foreach ($data_mysql['x'] as $fldnum=>$val)
		{
			if($xsubcatfields[$fldnum]['TYPE'] == "N") $data_mysql['x'][$fldnum]=0+$val;
		}
	}


	$data_mysql['price'] = 0 + str_replace(",", "", $data_mysql['price']);
	$data_mysql['othercontactok'] = 0 + $data_mysql['othercontactok'];

	$sql = "SET adtitle = '$data_mysql[adtitle]',
				addesc = '$data_mysql[addesc]',
				area ='$data_mysql[area]',
				email = '$data_mysql[email]',
				showemail = '$data_mysql[showemail]',
				#password = '$data_mysql[password]',
				cityid = $data_mysql[cityid],
				urgent = '$data_mysql[urgent]',
				urgent_paid = '$data_mysql[urgent_paid]',
				othercontactok = '$data_mysql[othercontactok]',
                newsletter = '$data_mysql[newsletter]',
				expireson = '$data_mysql[expireson]',
				timestamp = NOW(),";


	if($isevent)
	{
		$sql = "UPDATE $table " . $sql .
				" starton = '$data_mysql[fy]-$data_mysql[fm]-$data_mysql[fd]',
				endon = '$data_mysql[ty]-$data_mysql[tm]-$data_mysql[td]'";
	}
	else
	{
		$sql = "UPDATE $table " . $sql .
				" subcatid = $data_mysql[subcatid],
				price = $data_mysql[price]";
	}

	$sql .= " WHERE adid = $adid";

	mysql_query($sql) or die($sql.mysql_error());
	


	// Save extra fields
	if (is_array($data_mysql['x']) && count($data_mysql['x']))
	{
		$sql = "SELECT COUNT(*) FROM $t_adxfields WHERE adid = $adid"; 
		list($hasrecord) = mysql_fetch_array(mysql_query($sql));

		if($hasrecord) $sql = "UPDATE $t_adxfields SET ";
		else $sql = "INSERT INTO $t_adxfields SET ";

		foreach ($data_mysql['x'] as $fldnum=>$val)
		{
			if($xsubcatfields[$fldnum]['TYPE'] == "N") 
			{
				/*if($val == "") $val = -1;
				else*/
				$val = 0+$val;
			}
			$sql .= "f{$fldnum} = '$val',";
		}
		
		$sql = substr($sql, 0, -1);

		if($hasrecord) $sql .= " WHERE adid = $adid";
		else $sql .= ", adid = $adid";

		mysql_query($sql) or print($sql);
	}


	$msg = "The ad has been updated";


	// Make featured/unfeatured
	if ($data_mysql['makefeatured'])
	{
	    
		$featuredtill_temp = "{$data_mysql['feat_y']}-{$data_mysql['feat_m']}-{$data_mysql['feat_d']}" . substr($data_mysql['featuredtill_orig'], 10);
    	$data_mysql['featuredtill'] = date("Y-m-d H:i:s", strtotime($featuredtill_temp));
       
        
		if($data_mysql['featadid'])
		{
			$sql = "UPDATE $t_featured SET featuredtill = '$data_mysql[featuredtill]' WHERE featadid = $data_mysql[featadid]";
			mysql_query($sql) or die($sql.mysql_error());
		}
		else
		{
			$sql = "INSERT INTO $t_featured SET adid = $adid, adtype = '$adtype', featuredtill = '$data_mysql[featuredtill]'";
			mysql_query($sql) or die($sql.mysql_error());
		}


		if(mysql_affected_rows())
		{
			$sql = "SELECT UNIX_TIMESTAMP(featuredtill) AS featuredtill_ts FROM $t_featured WHERE  adid = $adid AND adtype = '$adtype'";
			list($new_featuredtill_ts) = @mysql_fetch_array(mysql_query($sql));

			if($new_featuredtill_ts > time())
			{
				$msg .= "<br>The ad has been made featured";
			}
			else
			{
				$msg .= "<br><span class=\"imp\">The ad has been cancelled as featured ad</span>";
			}
		}
	}
	else if ($data_mysql['featadid'])
	{
		$sql = "UPDATE $t_featured SET featuredtill = NOW() WHERE featadid = $data_mysql[featadid] AND featuredtill > NOW()";
		mysql_query($sql) or die($sql.mysql_error());

		if(mysql_affected_rows())
		{
			$msg .= "<br><span class=\"imp\">The ad has been cancelled as featured ad</span>";
		}
	}


	// Delete pictures	
	$delpiccnt = 0;
	if (count($data_mysql['delpic']))
	{
		foreach($data_mysql['delpic'] as $picid)
		{
			$sql = "SELECT picfile FROM $t_adpics WHERE picid = $picid";
			list($filename) = mysql_fetch_array(mysql_query($sql));
			unlink("{$path_escape}{$datadir[adpics]}/$filename");

			$sql = "DELETE FROM $t_adpics WHERE picid = $picid";
			mysql_query($sql);

			$delpiccnt++;
		}
	}

	if($delpiccnt) $msg .= "<br>$delpiccnt picture(s) deleted";


	
	// Upload new pictures
	if (count($_FILES['pic']['tmp_name']))
	{
		$ipval = ipval();
		$uploaderror = 0;
		$uploadcount = 0;

		foreach ($_FILES['pic']['tmp_name'] as $k=>$tmpfile)
		{
			if ($tmpfile)
			{
				// Check size
				if ($_FILES['pic']['size'][$k] > $pic_maxsize*1000)
				{
					$uploaderror++;
				}
				elseif (!in_array($_FILES['pic']['type'][$k], $pic_filetypes))
				{
					$uploaderror++;
				}
				else
				{
					$thisfile = array("name"=>$_FILES['pic']['name'][$k],
										"tmp_name"=>$_FILES['pic']['tmp_name'][$k],
										"size"=>$_FILES['pic']['size'][$k],
										"type"=>$_FILES['pic']['type'][$k],
										"error"=>$_FILES['pic']['error'][$k]);

					$newfile = SaveUploadFile($thisfile, "{$path_escape}{$datadir[adpics]}", TRUE, $images_max_width, $images_max_height);
					if($newfile)
					{
						$sql = "INSERT INTO $t_adpics
								SET adid = $adid,
									isevent = '$isevent',
									picfile = '$newfile'";
						mysql_query($sql);

						if (mysql_error())
						{
							$msg .= "<br><span class=\"error\">Error uploading $_FILES[pic][name]</span><br>";
							$uploaderror++;
						}
						else
						{
							$uploadcount++;
						}

					}
					else
					{
						$uploaderror++;
					}
				}

			}
			elseif ($_FILES['pic']['name'][$k])
			{
				$uploaderror++;
			}
		}

		if($uploadcount)
		{
			$msg .= "<br>$uploadcount pictures uploaded";
		}
		if($uploaderror)
		{
			$msg .= "<br>$uploaderror pictures could NOT be uploaded";
		}
	}


	header("Location: $_POST[returl]&msg=".urlencode($msg));
	exit;

}

$ad_found = false;
$sql = "SELECT a.*, UNIX_TIMESTAMP(a.createdon) AS createdon, UNIX_TIMESTAMP(a.timestamp) AS timestamp, 
		feat.featadid, feat.featuredtill, UNIX_TIMESTAMP(feat.featuredtill) AS featuredtill_ts 
		FROM $table a 
			LEFT OUTER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = '$adtype' 
		WHERE a.adid = $adid";
$res_orig = mysql_query($sql);

if (mysql_num_rows($res_orig)) {

$data_orig = mysql_fetch_array($res_orig);
$ad_found = true;

recurse($data, 'htmlspecialchars');	// Done before merging with original data as thats already saved as escaped.
if ($_POST['do'] == "post") $data = $data + $data_orig;
else $data = $data_orig;




// Date lists
$dlist = "";
for($i=1; $i<=31; $i++) $dlist .= "<option value=\"$i\">$i</option>\n";

$mlist = "";
for ($i=1; $i<=12; $i++) $mlist .= "<option value=\"$i\">".$langx['months'][$i-1]."</option>\n";

$ylist = "";
$thisy = date("Y");
$starty = 2005;
$endy = $thisy + 5;
for ($i=$starty; $i<=$endy; $i++) $ylist .= "<option value=\"".($i)."\">".($i)."</option>";


list($data['exp_y'], $data['exp_m'], $data['exp_d']) = explode("-", substr($data['expireson'], 0, 10));
list($data['feat_y'], $data['feat_m'], $data['feat_d']) = explode("-", substr($data['featuredtill'], 0, 10));



if ($isevent)
{
	$catname = $lang['EVENTS'];

	// Split dates
	if (!$data['fy'])
	{
		preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/isU", $data['starton'], $m);
		list($dummy, $data['fy'], $data['fm'], $data['fd']) = $m;

		preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/isU", $data['endon'], $m);
		list($dummy, $data['ty'], $data['tm'], $data['td']) = $m;
	}
}
else
{
	$xsubcatid = $subcatid = $data['subcatid'];

	$sql = "SELECT subcatname FROM $t_subcats WHERE subcatid = $subcatid AND enabled = '1'";
	list($catname) = mysql_fetch_array(mysql_query($sql));

	list($xsubcathasprice, $xsubcatpricelabel, $xsubcatfields) = GetCustomFields($xsubcatid);
	$hasprice = $xsubcathasprice;
	$pricelabel = $xsubcatpricelabel;

	$sql = "SELECT * FROM $t_adxfields WHERE adid = $adid LIMIT 1";
	$row = @mysql_fetch_array(mysql_query($sql));
	
	for($i=1; $i<=$xfields_count; $i++)
	{
		$x[$i] = $row["f{$i}"];
	}
	
	if (is_array($data['x'])) $data['x'] = $data['x'] + $x;
	else $data['x'] = $x;
	
}

}
	
?>

<?php include_once("aheader.inc.php"); ?>

<script language="javascript">

function insertLink(link)
{
	var editpane = document.frmPost.addesc;
	var linkcode = "[URL]http://" + link + "[/URL]";

	editpane.focus();
	/*if (document.selection)
	{
		document.selection.createRange().text = linkcode;
	}
	else*/
	if (editpane.selectionStart || editpane.selectionStart == '0')
	{
		var selstart = editpane.selectionStart;
		var selend = editpane.selectionEnd;
		
		editpane.value = editpane.value.substring(0, selstart) + linkcode + editpane.value.substring(selend);
		editpane.selectionStart = selstart + linkcode.length;
		editpane.selectionEnd = editpane.selectionStart;
	}
	else
	{
		editpane.value = editpane.value + linkcode;
	}

	editpane.focus();

}
</script>

<br>

<?php if (!$ad_found) { ?>

<h2>The specified ad cannot be found in the database!</h2>
<b><a href="<?php echo $_REQUEST['returl']; ?>">Go Back</a></b>


<?php } else { ?>

<h2>Editing: "<?php echo $data['adtitle']; ?>"</h2>

<?php if($err) echo "<div class=\"err\">$err</div>"; ?>
<?php if($msg) echo "<div class=\"msg\">$msg</div>"; ?>

<b><a href="<?php echo $_REQUEST['returl']; ?>">Go Back</a></b>

<table><tr><td>

<form action="" method="post" name="frmPost" enctype="multipart/form-data" class="box">


<table class="postad" border="0" width="100%">

	<tr>
		<td><b>Ad ID:</b></td><td><?php echo $isevent?"E":"A"; ?><?php echo $adid; ?></td>
	</tr>

	<tr>
		<td><b>Created On:</b></td><td><?php echo date("r", $data['createdon']); ?></td>
	</tr>

	<tr>
		<td><b>Last Update:</b></td><td><?php echo date("r", $data['timestamp']); ?></td>
	</tr>

	<tr>
		<td colspan="2">&nbsp;</td>

	</tr>

	<tr>
		<td><b>Title:</b> <span class="marker">*</span></td><td><input name="adtitle" type="text" id="adtitle" size="75" maxlength="100" value="<?php echo $data['adtitle']; ?>">&nbsp;</td>

	</tr>

	<tr>
		<td><b>Location:</b></td><td><input name="area" type="text" size="40" maxlength="50" value="<?php echo $data['area']; ?>"></td>
	</tr>

  
    
	<tr>
		<td valign="top"><b>Post:</b> <span class="marker">*</span></td><td>
        
        <?php 
        if(richTextAllowed($data['createdon'])) { 
            $wmd_editor = array("name"=>"addesc", "content"=>$data['addesc']);
            include("{$path_escape}editor/wmd_editor.inc.php"); 
            
        } else {
        ?>
        
        <textarea name="addesc" cols="78" rows="10" id="addesc"><?php echo $data['addesc']; ?></textarea><br>
        
        <?php 
        } 
        ?>
                
		</td>
	</tr>
    
 
	
	<?php
	if ($hasprice)
	{
	?>
	
	<tr>
		<td><b><?php echo $pricelabel; ?>:</b></td>
		<td><?php echo $currency; ?> <input type="text" name="price" size="5" maxlength="10" value="<?php echo $data['price']; ?>"></td>
	</tr>


	<?php
	}
	?>



	<?php foreach($xsubcatfields as $fldnum=>$fld) { ?>
	<tr>
		<td valign="top"><b><?php echo $fld['NAME']; ?>: </b></td>
		<td>

				<?php

		switch($fld['TYPE'])
		{
			case "N":

		?>

			<input name="x[<?php echo $fldnum; ?>]" type="text" size="8" value="<?php echo $data['x'][$fldnum]; ?>">

		<?php

			break;

			case "D":

		?>

			<select name="x[<?php echo $fldnum; ?>]">
			<?php
			foreach ($fld['VALUES_A'] as $v)
			{
				echo "<option value=\"$v\"";
				if ($data['x'][$fldnum] == $v) echo " selected";
				echo ">$v</option>";
			}
			?>
			</select>

		<?php

			break;
			
			default:

		?>

			<input name="x[<?php echo $fldnum; ?>]" type="text" size="30" value="<?php echo $data['x'][$fldnum]; ?>">

		<?php

			break;

		}

		?>	

		</td>
	</tr>
	<?php } ?>



	<?php
	if($isevent)
	{
	?>

		<tr>

			<td><b>Starts On:</b> <span class="marker">*</span></td>
			<td>
			
			<select name="fm">
			<?php echo $mlist; ?>
			</select>
			
			<select name="fd">
			<?php echo $dlist; ?>
			</select> , 
			
			<select name="fy">
			<?php echo $ylist; ?>
			</select>
			
			</td>
			</tr>


		<tr>
			<td><b>Ends On: </b><span class="marker">*</span></td>
			<td>
			
			<select name="tm">
			<?php echo $mlist; ?>
			</select>
			
			<select name="td">
			<?php echo $dlist; ?>
			</select> , 
			
			<select name="ty">
			<?php echo $ylist; ?>
			</select>

			</td>
		</tr>
			
		<?php
		if ($data['fm'])	
		{
		?>

			<script language="javascript">

			document.frmPost.fm.options[<?php echo $data['fm']-1; ?>].selected = true;
			document.frmPost.fd.options[<?php echo $data['fd']-1; ?>].selected = true;
		
			document.frmPost.fy.options[<?php echo $data['fy']-$starty; ?>].selected = true;
		
			document.frmPost.tm.options[<?php echo $data['tm']-1; ?>].selected = true;
			document.frmPost.td.options[<?php echo $data['td']-1; ?>].selected = true;
			
			document.frmPost.ty.options[<?php echo $data['ty']-$starty; ?>].selected = true;
		

			</script>

		<?php
		}
		?>

	<?php
	}
	?>

	<tr>
		<td valign="top" width="20%"><b>Email:</b> <span class="marker">*</span> </td>
		<td><input name="email" type="text" id="email" size="30" maxlength="50" value="<?php echo $data['email']; ?>">

		<table border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td><input name="showemail" type="radio" value="0" <?php if(!$data['showemail'] || $data['showemail']===EMAIL_HIDE) echo "checked"; ?>></td>
			<td>Hide Email</td>
			</tr>
		<tr>
			<td><input name="showemail" type="radio" value="2" <?php if($data['showemail']==EMAIL_USEFORM) echo "checked"; ?>></td>
			<td>Use Contact Form</td>
			</tr>
		<tr>
			<td><input name="showemail" type="radio" value="1" <?php if($data['showemail']==EMAIL_SHOW) echo "checked"; ?>>&nbsp;</td>
			<td>Show Email</td>
			</tr>
		</table>
		</td>
	</tr>


	<?php /* ?>
	<tr>
		<td valign="top" width="10%"><b>Password:</b> <span class="marker">*</span> </td>
		<td><input name="password" type="text" id="password" size="30" maxlength="50" value="<?php echo $data['password']; ?>" class="password_hidden" onfocus="this.className='password_revealed';" onblur="this.className='password_hidden';"><br>
		<span class="hint">Click inside the box above to reveal/change the password.</span></td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<?php */ ?>

	<tr>
		<td valign="top" width="20%"><b>Category:</b> </td>
		<td>
		<?php if(!$isevent) { ?>
		<select name="subcatid">
		<?php

		$sql = "SELECT catid, catname
				FROM $t_cats
				ORDER BY catname";
		$res = mysql_query($sql);

		while ($row=mysql_fetch_array($res))
		{
			$sql = "SELECT subcatid, subcatname
					FROM $t_subcats
					WHERE catid = $row[catid]
					ORDER BY subcatname";

			$ress = mysql_query($sql);

			if (mysql_num_rows($ress))
			{
				while ($s = mysql_fetch_array($ress))
				{
					echo "<option value=\"$s[subcatid]\"";
					if ($data['subcatid'] == $s['subcatid'])
					{
						echo " selected"; 
						$cat = $row['catname']; 
						$subcat = $s['subcatname'];
					}
					echo ">$row[catname] > $s[subcatname]</option>";
				}

			}
		}

		?>
		</select><br>
		<span class="caution"><b>IMPORTANT:</b><br> Changing the category may cause inconsistencies with info in extra fields.</span>
		<?php } else { ?>
		Events
		<?php } ?>
		</td>
	</tr>

	<tr>
		<td valign="top" width="20%"><b>City:</b> </td>
		<td>
		<select name="cityid">
		<?php

		$sql = "SELECT countryid, countryname
				FROM $t_countries
				ORDER BY countryname";
		$res = mysql_query($sql);

		while ($row=mysql_fetch_array($res))
		{
			$sql = "SELECT cityid, cityname
					FROM $t_cities ct
					WHERE countryid = $row[countryid]
					ORDER BY cityname";
			$resct = mysql_query($sql);

			if (mysql_num_rows($resct))
			{
				while ($ct = mysql_fetch_array($resct))
				{
					echo "<option value=\"$ct[cityid]\"";
					if ($data['cityid'] == $ct['cityid'])
					{
						$country = $row['countryname'];
						$city = $ct['cityname'];
						echo " selected"; 
					}
					echo ">$row[countryname] > $ct[cityname]</option>\r\n";
				}
			}
		}

		?>
		</select>
		</td>
	</tr>
	
	<tr><td colspan="2">&nbsp;</td></tr>

	<tr>
		<td valign="top" width="10%"><b>Expires On:</b> </td>
		
		<td><input name="expireson_orig" type="hidden" id="expireson_orig" value="<?php echo $data['expireson']; ?>">
		<select name="exp_m"><?php echo $mlist; ?></select>
		<select name="exp_d"><?php echo $dlist; ?></select> , 
		<select name="exp_y"><?php echo $ylist; ?></select>
		
		<script type="text/javascript">
		
		document.frmPost.exp_m.options[<?php echo $data['exp_m']-1; ?>].selected = true;
		document.frmPost.exp_d.options[<?php echo $data['exp_d']-1; ?>].selected = true;
		document.frmPost.exp_y.options[<?php echo $data['exp_y']-$starty; ?>].selected = true;

		</script>
		</td>
	
	</tr>


</table>
<br>


<table class="postad" cellspacing="0" cellpadding="0" border="0" width="100%">


<tr><td colspan="2">&nbsp;</td></tr>

	<tr>

		<td style="border:1px dotted black;padding:5px;">
<font color="brown">If you want to mark the ad as URGENT please tick both checkboxes below:</font>
<br><br>
		<input type="checkbox" name="urgent" value="1" <?php if($data['urgent'] == 1) echo "checked"; ?>> <?php echo $lang['URGENT']; ?>
		<br>
		<input type="checkbox" name="urgent_paid" value="1" <?php if($data['urgent_paid'] == 1) echo "checked"; ?>> Mark <b>URGENT</b> Ad as PAID

		</td>

	</tr>
<tr><td colspan="2">&nbsp;</td></tr>

	<tr>
		<td>
		<input type="checkbox" name="othercontactok" value="1" <?php if($data['othercontactok'] == 1) echo "checked"; ?>> It is OK to contact this user with commercial interests.
		</td>
	</tr>

<tr><td colspan="2">&nbsp;</td></tr>

	<tr>

		<td>

		<input type="checkbox" name="newsletter" value="1" <?php if($data['newsletter'] == 1) echo "checked"; ?>> <?php echo $lang['POST_NEWSLETTER_OPTION']; ?>

		</td>

	</tr>

</table>
<br><br><br>


<table>
<tr><td colspan="2"><h3>Featured Ad</h3></td></tr>

<?php 
if($data['featuredtill_ts'])
{
?>
<tr><td colspan="2">
<?php
	if($data['featuredtill_ts'] > time())
	{
		$isfeatured = TRUE;
?>
<span class="msg">This is a featured ad</span>
<?php
	}
?>
</td></tr>
<?php
}
?>


<script type="text/javascript">

function toggleFeatured(chk) {
    if(chk.checked) {
        chk.form.featuredtill_orig.disabled = false;
        chk.form.feat_y.disabled = false;
        chk.form.feat_m.disabled = false;
        chk.form.feat_d.disabled = false;
    }
    else {
        chk.form.featuredtill_orig.disabled = true;
        chk.form.feat_y.disabled = true;
        chk.form.feat_m.disabled = true;
        chk.form.feat_d.disabled = true;
    }
}

function defaultFeatured(chk) {
    if(chk.checked && !chk.form.featuredtill_orig.value) {
        <?php
        $new_feat_date = time() + 10*24*60*60;
        $new_feat_y = date("Y", $new_feat_date);
        $new_feat_m = date("m", $new_feat_date);
        $new_feat_d = date("d", $new_feat_date);
        ?>
        chk.form.featuredtill_orig.value = '<?php echo "{$new_feat_y}-{$new_feat_m}-{$new_feat_d} 23:59:59"; ?>';
        chk.form.feat_m.options[<?php echo $new_feat_m-1; ?>].selected = true;
        chk.form.feat_d.options[<?php echo $new_feat_d-1; ?>].selected = true;
        chk.form.feat_y.options[<?php echo $new_feat_y-$starty; ?>].selected = true;

    }
}

</script>


<tr>
<td valign="top">Feature this ad:</td>
<td>
<?php
$featchkplus = "";
$featchkaction = "toggleFeatured(this); ";
if ($isfeatured) 
{
	$featchkplus = " checked";
}
else
{
	$featchkaction .= " defaultFeatured(this);";
	$feattxtplus = " disabled";
}
?>
<input type="hidden" name="featadid" value="<?php echo $data['featadid']; ?>">
<input type="checkbox" name="makefeatured" size="30" value="1" onChange="<?php echo $featchkaction; ?>" <?php echo $featchkplus; ?>>
</td>
</tr>

<tr>
<td valign="top">Run as featured till:</td>

<td><input type="hidden" name="featuredtill_orig" value="<?php echo $isfeatured?$data['featuredtill']:""; ?>" <?php echo $feattxtplus; ?>> 

<select name="feat_m" <?php echo $feattxtplus; ?>><?php echo $mlist; ?></select>
<select name="feat_d" <?php echo $feattxtplus; ?>><?php echo $dlist; ?></select> ,
<select name="feat_y" <?php echo $feattxtplus; ?>><?php echo $ylist; ?></select>

<?php 
if ($data['featuredtill']) { 
?>
<script type="text/javascript">
document.frmPost.feat_m.options[<?php echo $data['feat_m']-1; ?>].selected = true;
document.frmPost.feat_d.options[<?php echo $data['feat_d']-1; ?>].selected = true;
document.frmPost.feat_y.options[<?php echo $data['feat_y']-$starty; ?>].selected = true;
</script>

<?php 
} 
?>

<br>
Note: Entering an old date will cause the ad to be cancelled as a featured ad
</span>

</td>
</tr>

</table>
<br><br><br>


<h3>Pictures</h3>
<table class="postad" cellspacing="2" cellpadding="0" border="0">

<?php

$sql = "SELECT * FROM $t_adpics WHERE adid = $adid AND isevent = '$isevent'";
$pics = mysql_query($sql);
$pics_present = mysql_num_rows($pics);

if(!$pics_present)
{
?>

	<tr><td><span class="info">No pictures for this ad</span></td></tr>

<?php
}
else
{
	while($pic = mysql_fetch_array($pics))
	{

?>

	<tr><td>
	<img src="../<?php echo $datadir['adpics']; ?>/<?php echo $pic['picfile']; ?>">
	</td><td>
	<input type="checkbox" name="delpic[]" value="<?php echo $pic['picid']; ?>"> 
	<span class="extracaution">DELETE!</span>
	</td></tr>

	<tr><td colspan="2">&nbsp;</td></tr>

<?php
	}
}
?>

</table>

<br> <br>

<?php
if($pics_present < $pic_count)
{
?>

<b>Upload More Pictures:</b><br>

<?php
	for($i=$pics_present+1; $i<=$pic_count; $i++)
	{
?>

		<input type="file" name="pic[]" size="69"><br>

<?php
	}
?>

<br> <br>

<?php
}
?>

<br> 


<?php
if($isevent)
{
?>
	<input name="isevent" type="hidden" id="isevent" value="1">
<?php
}
?>

<input name="do" type="hidden" id="do" value="post">
<input name="returl" type="hidden" id="do" value="<?php echo $_REQUEST['returl']; ?>">


<button type="submit">Update</button>
<button type="button" onclick="location.href='<?php echo $_REQUEST['returl']; ?>';">Cancel</button>
</td>


</form>

</td></tr></table>

<?php
}
?>

<?php include_once("afooter.inc.php"); ?>