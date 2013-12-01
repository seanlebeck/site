<?php




require_once("admin.inc.php");
require_once("aauth.inc.php");


if ($demo) $err = "Changes to templates cannot be saved in demo";


if(isset($_POST['filename']) && !isSafeFilename($_POST['filename'])) {
	handle_security_attack();
}


if (!$demo && $_POST['update'])
# Update email template
{
	$tpl = stripslashes($_POST['tpl']);

	$fp = fopen("../mailtemplates/$_POST[filename]", "w") or die("Cannot open template file to write");
	fwrite($fp, $tpl);
	fclose($fp);

	$msg = "Template $_POST[filename] updated";
}


?>
<?php include_once("aheader.inc.php"); ?>

<script language="javascript">
function insertVar(editpane,myvar)
{
	editpane.focus();
	if (editpane.selectionStart || editpane.selectionStart == '0')
	{
		var selstart = editpane.selectionStart;
		var selend = editpane.selectionEnd;
		
		editpane.value = editpane.value.substring(0, selstart) + myvar + editpane.value.substring(selend);
		editpane.selectionStart = selstart + myvar.length;
		editpane.selectionEnd = editpane.selectionStart;
	}
	else if (document.selection)
	{
		var txt = document.selection.createRange().text = myvar;
	}
	editpane.focus();
}
</script>

<h2>Edit Email Templates</h2>

<p style="font-size:10px;">
<a href="#newpost">New Post</a>
- <a href="#newimg">New Image</a>
- <a href="#emailad">Email Ad</a>
<?php //- <a href="#renew">Renew Ad</a> ?>
- <a href="#contact_header">Contact User</a>

</p>

<div class="msg"><?php echo $msg; ?></div>
<div class="err"><?php echo $err; ?></div>

<a name="newpost"></a>
<h3>New Post</h3>
<form action="?" method="post" name="frmTemplate1" class="box">
	<table border="0"><tr>
		<td valign="top"> 
			<textarea name="tpl" cols="60" rows="20"><?php readfile("../mailtemplates/newpost.txt"); ?></textarea>
			<br><br>
			<input type="hidden" name="filename" value="newpost.txt">
			<input type="hidden" name="update" value="1">
			<button type="submit">Update</button><br><br>
		</td>
		<td width="10"></td>
		<td valign="top" class="hint">
			<table cellspacing="1" cellpadding="3">
			<tr><td><b>Insert Variable</b></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate1['tpl'],'{@SITENAME}');">Sitename</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate1['tpl'],'{@ADTITLE}');">Post Title</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate1['tpl'],'{@VERIFICATIONLINK}');">Verification Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate1['tpl'],'{@ADURL}');">View Ad Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate1['tpl'],'{@EDITURL}');">Edit Ad Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate1['tpl'],'{@EXPIRESON}');">Expires On</a></td></tr>
			</table>
		</td>
	</tr></table>
</form>


<a name="newimg"></a>
<h3>New Image</h3>
<form action="?" method="post" name="frmTemplate3" class="box">
	<table border="0"><tr>
		<td valign="top"> 
			<textarea name="tpl" cols="60" rows="20"><?php readfile("../mailtemplates/newimg.txt"); ?></textarea>
			<br><br>
			<input type="hidden" name="filename" value="newimg.txt">
			<input type="hidden" name="update" value="1">
			<button type="submit">Update</button><br><br>
		</td>
		<td width="10"></td>
		<td valign="top" class="hint">
			<table cellspacing="1" cellpadding="3">
			<tr><td><b>Insert Variable</b></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate3['tpl'],'{@SITENAME}');">Sitename</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate3['tpl'],'{@IMAGETITLE}');">Image Title</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate3['tpl'],'{@VERIFICATIONLINK}');">Verification Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate3['tpl'],'{@IMAGEURL}');">View Image Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate3['tpl'],'{@DELETEURL}');">Delete Image Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate3['tpl'],'{@EXPIRESON}');">Expires On</a></td></tr>
			</table>
		</td>
	</tr></table>
</form>

<?php /*NOT NEEDED COZ IMAGES AUTO APPROVED ?>
<a name="img_approved"></a>
<h3>Image Approved</h3>
<form action="?" method="post" name="frmTemplate4" class="box">
	<table border="0"><tr>
		<td valign="top"> 
			<textarea name="tpl" cols="60" rows="20"><?php readfile("../mailtemplates/imgapproved.txt"); ?></textarea>
			<br><br>
			<input type="hidden" name="filename" value="imgapproved.txt">
			<input type="hidden" name="update" value="1">
			<button type="submit">Update</button><br><br>
		</td>
		<td width="10"></td>
		<td valign="top" class="hint">
			<table cellspacing="1" cellpadding="3">
			<tr><td><b>Insert Variable</b></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate4['tpl'],'{@SITENAME}');">Sitename</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate4['tpl'],'{@IMAGETITLE}');">Image Title</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate4['tpl'],'{@IMAGEURL}');">View Image Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate4['tpl'],'{@DELETEURL}');">Delete Image Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> <a href="javascript:insertVar(document.frmTemplate4['tpl'],'{@PASSWORD}');">Password</a></td></tr>
			</table>
		</td>
	</tr></table>
</form>
<?php */ ?>


<a name="emailad"></a>
<h3>Email Ad</h3>
<form action="?" method="post" name="frmTemplate7" class="box">
	<table border="0"><tr>
		<td valign="top"> 
			<textarea name="tpl" cols="60" rows="10"><?php readfile("../mailtemplates/mailad.txt"); ?></textarea>
			<br><br>
			<input type="hidden" name="filename" value="mailad.txt">
			<input type="hidden" name="update" value="1">
			<button type="submit">Update</button><br><br>
		</td>
		<td width="10"></td>
		<td valign="top" class="hint">
			<table cellspacing="1" cellpadding="3">
			<tr><td><b>Insert Variable</b></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate7['tpl'],'{@SITENAME}');">Sitename</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate7['tpl'],'{@ADURL}');">Ad URL</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate7['tpl'],'{@RECEIVERNAME}');">Receiver Name</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate7['tpl'],'{@RECEIVEREMAIL}');">Receiver Email</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate7['tpl'],'{@SENDERNAME}');">Sender Name</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate7['tpl'],'{@SENDEREMAIL}');">Sender Email</a></td></tr>
			</table>
		</td>
	</tr></table>
</form>


<?php /* PENDING 
<a name="renew"></a>
<h3>Renew Ad</h3>
<form action="?" method="post" name="frmTemplate8" class="box">
	<table border="0"><tr>
		<td valign="top"> 
			<textarea name="tpl" cols="60" rows="20"><?php readfile("../mailtemplates/renew.txt"); ?></textarea>
			<br><br>
			<input type="hidden" name="filename" value="renew.txt">
			<input type="hidden" name="update" value="1">
			<button type="submit">Update</button><br><br>
		</td>
		<td width="10"></td>
		<td valign="top" class="hint">
			<table cellspacing="1" cellpadding="3">
			<tr><td><b>Insert Variable</b></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate8['tpl'],'{@SITENAME}');">Sitename</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate8['tpl'],'{@ADID}');">Ad ID</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate8['tpl'],'{@ADTITLE}');">Ad Title</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate8['tpl'],'{@ADURL}');">Ad URL</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate8['tpl'],'{@AD}');">Full Ad</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate8['tpl'],'{@EXPIRESON}');">Ad Expires On</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate8['tpl'],'{@RENEWURL}');">Renew URL</a></td></tr>
			</table>
		</td>
	</tr></table>
</form>
*/ ?>

<a name="contact_header"></a>
<h3>Contact User : Header</h3>
<form action="?" method="post" name="frmTemplate5" class="box">
	<table border="0"><tr>
		<td valign="top"> 
			<textarea name="tpl" cols="60" rows="10"><?php readfile("../mailtemplates/contact_header.txt"); ?></textarea>
			<br><br>
			<input type="hidden" name="filename" value="contact_header.txt">
			<input type="hidden" name="update" value="1">
			<button type="submit">Update</button><br><br>
		</td>
		<td width="10"></td>
		<td valign="top" class="hint">
			<table cellspacing="1" cellpadding="3">
			<tr><td><b>Insert Variable</b></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate5['tpl'],'{@SITENAME}');">Sitename</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate5['tpl'],'{@ADTITLE}');">Ad Title</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate5['tpl'],'{@ADURL}');">View Ad Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate5['tpl'],'{@FROM}');">From Email</a></td></tr>
			</table>
		</td>
	</tr></table>
</form>

<a name="contact_footer"></a>
<h3>Contact User : Footer</h3>
<form action="?" method="post" name="frmTemplate6" class="box">
	<table border="0"><tr>
		<td valign="top"> 
			<textarea name="tpl" cols="60" rows="10"><?php readfile("../mailtemplates/contact_footer.txt"); ?></textarea>
			<br><br>
			<input type="hidden" name="filename" value="contact_footer.txt">
			<input type="hidden" name="update" value="1">
			<button type="submit">Update</button><br><br>
		</td>
		<td width="10"></td>
		<td valign="top" class="hint">
			<table cellspacing="1" cellpadding="3">
			<tr><td><b>Insert Variable</b></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate6['tpl'],'{@SITENAME}');">Sitename</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate6['tpl'],'{@ADTITLE}');">Ad Title</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate6['tpl'],'{@ADURL}');">View Ad Link</a></td></tr>
			<tr><td><b style="color:#CC3300">&#8249;</b> 
			<a href="javascript:insertVar(document.frmTemplate6['tpl'],'{@FROM}');">From Email</a></td></tr>
			</table>
		</td>
	</tr></table>
</form>




<?php include_once("afooter.inc.php"); ?>