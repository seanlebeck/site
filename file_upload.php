<?php
require_once("{$path_escape}initvars.inc.php");
require_once("{$path_escape}config.inc.php"); 
?>
<style type="text/css">
		body { margin: 0px; font-family: "Verdana", "Helvetica", sans-serif; font-size: 12px; }
		#vs_upload_loader {
			height: 45px;
			width: 100%;
			background-color: #FFFFFF;
			background-image: url(images/image-uploading.gif);
			background-repeat: no-repeat;
			background-position: left center;
		}
</style>
		
<script src="<?php echo $path_escape;?>js/jquery-1.4.4.js" type="text/javascript"></script>
<script type="text/javascript">
	function finishedLoading(){
		var finishedFileWorking = 0;
		var fileNameLoaded= "";
		var reason = "";
		var tempadpicfolder = "";
		if( document.getElementById('finishedFileWorking') != null){//if there was an upload of file
			finishedFileWorking = document.getElementById('finishedFileWorking').value;
			fileNameLoaded = document.getElementById('filenameuploaded').value;
			tempadpicfolder = document.getElementById('tempadpicfolder').value;
			reason = document.getElementById('reasonupload').value;
		}
		if(finishedFileWorking == 1 ){//if upload was successfull
			finishedUploading(tempadpicfolder, fileNameLoaded, finishedFileWorking, reason);
		}else if(finishedFileWorking == 2){//if upload failed
			finishedUploading(tempadpicfolder, fileNameLoaded, finishedFileWorking, reason);
		}
	}

			function doUpload(){
				var maxpics = parseInt(document.getElementById('maxnumoffiles').value);
				var curnumpics = parseInt(parent.$("#image_total").text());
				if(parent.$('#existing_pictures_num').length){//if it is edit mode and there are already existing pics
					curnumpics = parseInt(curnumpics) + parseInt(parent.$('#existing_pictures_num').val());
				}
				if( curnumpics >= maxpics){
					var errmsgtoshow = document.getElementById('strmaximgallowed').value + ': ' + maxpics;
					parent.$("#pics_not_uploaded_err").show();
					parent.$("#pics_not_uploaded_err").text(errmsgtoshow);
					document.getElementById('file').value = "";
					return ;
				}
				document.getElementById('file_upload_form').submit();
				toggleUploadLoading(document.getElementById('file').value);
			}
			
			function toggleUploadLoading(file_name) {
				var form = document.getElementById('file_upload_form');
				var loader = document.getElementById('vs_upload_loader');
				form.style.display = 'none';
				loader.style.display = '';
			}

			function finishedUploading(tempadpicfolder, file_name, result, reason){
				parent.showUploadedPhoto(tempadpicfolder, file_name, result, reason);
			}
</script>

<html>
	<body onload="finishedLoading()">
		<form id="file_upload_form" method="post" enctype="multipart/form-data" action="<?php echo $path_escape;?>file_upload.php">
			<input type="hidden" name="doupload" value="1">
			<input type="hidden" name="maxnumoffiles" id="maxnumoffiles" value="<?php echo $pic_count?>">
			<input type="hidden" name="strmaximgallowed" id="strmaximgallowed" value="<?php echo $lang[MAXIMUM_IMAGES_ALLOWED];?>">
			<input name="file" id="file" size="27" type="file" 
			onchange="doUpload();"/><br />
		</form>
		<div style="display:none" id="vs_upload_loader"></div>


<?php

if($_POST['doupload']){//if file was trying to upload
	$thisfile = array("name"=>$_FILES['file']['name'],
						"tmp_name"=>$_FILES['file']['tmp_name'],
						"size"=>$_FILES['file']['size'],
						"type"=>$_FILES['file']['type'],
						"error"=>$_FILES['file']['error']);

// Check size
		$errorMessages = "";

		if($_FILES['file']['size'] > $pic_maxsize*1000 ){
			$errorMessages = $lang[FILE_NOT_LOADED_CHECK_SIZE];
		}else if (!isValidImage($thisfile)){
		    $errorMessages = $lang['ERROR_UPLOAD_PIC_BAD_FILETYPE'];
		}else{
			$newfile = SaveUploadFile($thisfile, "{$path_escape}$datadir[tempadpics]", TRUE, $images_max_width, $images_max_height);
		}
		
		if($newfile){
			echo "<input type=\"hidden\" id=\"finishedFileWorking\" value=\"1\">";
		}else{
			if(!$errorMessages){
				$errorMessages = $lang[FILE_NOT_LOADED_CHECK_SIZE];
			}
			echo "<input type=\"hidden\" id=\"finishedFileWorking\" value=\"2\">";
		}
		
		echo "<input type=\"hidden\" id=\"filenameuploaded\" value=\"" . $newfile . "\">";
		echo "<input type=\"hidden\" id=\"reasonupload\" value=\"" . $errorMessages . "\">";
		echo "<input type=\"hidden\" id=\"tempadpicfolder\" value=\"" . $path_escape . $datadir['tempadpics'] . "\">";
	
		
		echo "<span style=\"color:#cc0000;font-weight:bold;\">" . $errorMessageToShow . "</span>"; 
	
}
if($_POST['removeuploadedfile']){//if file has to be removed
	remove_uploaded_file( $datadir['tempadpics'] . "/" . $_POST['removeuploadedfile']);
}

function remove_uploaded_file($file_name_path){
	if(is_file($file_name_path)){
		unlink($file_name_path);
	}
}

?>

	</body>
</html>