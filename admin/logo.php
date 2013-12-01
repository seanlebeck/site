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


function getExtension($str) {
$i = strrpos($str,".");
if (!$i) { return ""; }
$l = strlen($str) - $i;
$ext = substr($str,$i+1,$l);
return $ext;
}
if(isset($_POST['submit']))
{
	//reads the name of the file the user submitted for uploading
	$image=$_FILES['image']['name'];
	//if it is not empty
	
	if ($image)
	{
		//get the original name of the file from the clients machine
		$filename = stripslashes($_FILES['image']['name']);
		
		//get the extension of the file in a lower case format
		$extension = getExtension($filename);
		$extension = strtolower($extension);
		//echo $extension;
		//if it is not a known extension, we will suppose it is an error and will not upload the file,
		//otherwise we will do more tests
		if($extension!="png")
		{
			//print error message
			$unknown_ext = "File format is not supported. Please try again uploading file ending with \".png\".";
			$errors=1;
		}
		else
		{
			//get the size of the image in bytes
			//$_FILES['image']['tmp_name'] is the temporary filename of the file
			//in which the uploaded file was stored on the server
			$size=filesize($_FILES['image']['tmp_name']);
			echo "size : ".$size;
			//compare the size with the maxim size we defined and print error if bigger
			if($size>=(1500*1000))
			{
				$size_exceed = "You have exceeded the file size limit! Try again with lower file size.";
				$errors=1;
			}
			
			//we will give an unique name, for example the time in unix time format
			$image_name="logo.png";
			
			//the new name will be containing the full path where will be stored (images folder)
			$newname="../images/".$image_name;
			echo $newname;
			//echo "image Directory==".$newname;
			
			//we verify if the image has been uploaded, and print error instead
			if(!$errors)
			{
				$copied = copy($_FILES['image']['tmp_name'],$newname);
				$success = 'Successfully Uploaded';

			}
			if (!$copied)
			{
				$copy_unsuccess = 'Copying unsuccessfull!';
				$errors=1;
			}
		}
	}
}




if($demo) $err = "This feature is disabled in the demo";

?>
<?php include_once("aheader.inc.php"); ?>

<h2>Update Logo</h2>

<?php if($err) { ?><div class="err"><?php echo $err; ?></div><?php } ?>
<?php if($copy_unsuccess) { ?><div class="err"><?php echo $copy_unsuccess; ?></div><?php } ?>
<?php if($size_exceed) { ?><div class="err"><?php echo $size_exceed; ?></div><?php } ?>
<?php if($unknown_ext) { ?><div class="err"><?php echo $unknown_ext; ?></div><?php } ?>
<?php if($success) { ?><div class="msg"><?php echo $success; ?></div><?php } ?>
<?php if($filename) { ?><div class="msg">Uploaded filename: <?php echo $filename; ?></div><?php } ?>

<br>

<script language="JavaScript"><!--
function flip() {
    if (document.images)
        document.images['logo'].src = '../images/logo.png';
}
//--></script>
<table>
  <tr>
    <td valign="top"><img src="images/tip.gif" align="absmiddle">&nbsp;</td>
    <td valign="top" class="tip"> Browse for your logo file in your computer by pressing the browse buton. Once selected then press Update button to upload it.</td>
  </tr>
</table>
<form action="" method="post" name="frmImportCats" enctype="multipart/form-data" class="box">
  
  <table><tr><td>File: </td>
<td>
<input type="file" name="image" size="50">
<input type="hidden" name="do" value="import">
<input type="hidden" name="type" value="cats">
<button type="submit" name="submit">Update</button>
</td></tr>

<tr><td>&nbsp;</td><td>
<br><div>
<div>

</div>
Please note:<br>
* Logo must be in PNG format file.<br>
*Any other format of image  will be considered as error<br>

</div>
</td></tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><table width="520" border="0" cellspacing="0" cellpadding="0" class="box">
    <tr>
      <td>Current Logo: (<a href="#" onClick="flip();return false">refresh image</a>)<p></p></td>
    </tr>
    <tr>
      <td><img src="../images/logo.png alt="" name="logo" border="1" id="logo"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table></td>
</tr>
</table>
</form><br>
<?php include_once("afooter.inc.php"); ?>