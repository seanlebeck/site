<?php


// For more addons visit www.vivaru.com     					    			 |
// Email: info@vivaru.com
			          




require_once("admin.inc.php");
require_once("aauth.inc.php");

include_once("aheader.inc.php");

$_POST['do'] = strtolower($_POST['do']);
$_GET['do'] = strtolower($_GET['do']);
$_REQUEST['do'] = strtolower($_REQUEST['do']);


$page = $_REQUEST['page'] ? $_REQUEST['page'] : 1;
$offset = ($page-1) * $msgs_per_page;

$sql_msgs = "SELECT COUNT(*)
			FROM $t_contact_form_save
			WHERE 1";

list($total_msgs) = mysql_fetch_row(mysql_query($sql_msgs));

if ($_GET['do'] == "delete"){
	if( $_GET['id'] ){
		
		$sql = "DELETE FROM $t_contact_form_save WHERE id = '$_GET[id]'";
		$res = mysql_query($sql) or die(mysql_error().$sql);
	}
}else if ($_GET['do'] == "resend"){
	if( $_GET['id'] ){
		$sql = "SELECT from_email, to_email, message_email FROM $t_contact_form_save WHERE id = '$_GET[id]'";
		$res = mysql_query($sql) or die(mysql_error().$sql);
		list($from_mail, $to_mail, $msg_mail) = mysql_fetch_row(mysql_query($sql));
		
		$header = "MIME-Version: 1.0" . "\r\n";
		$header .= "Content-type:text/plain;charset=" . $langx['charset'] . "\r\n";
		$header .= "From: $from_mail" . "\r\n";
		$result_mail_send = mail($to_mail, $lang['MAILSUBJECT_CONTACT_FORM'], $msg_mail, $header);
		if($result_mail_send){
			$msg = "Resending email successful";
			$status_snt = '1';
		}else{
			$err = "Error resending the mail to $to_mail";
			$status_snt = '0';
		}
		$ip_remote = $_SERVER['REMOTE_ADDR'];
		$sql = "UPDATE $t_contact_form_save SET status_sent = '$status_snt', ip_from = '$ip_remote', sent_date = NOW() WHERE id = '$_GET[id]'";
		mysql_query($sql) or die(mysql_error().$sql);
	}
}

?>

<h2>Contact form messages</h2>

<?php if($msg) { ?><div class="msg"><?php echo $msg; ?></div><?php } ?>
<?php if($err) { ?><div class="err"><?php echo $err; ?></div><?php } ?>


<?php
$sql = "SELECT msg.*
		FROM $t_contact_form_save msg
		ORDER BY id DESC
		LIMIT $offset, $msgs_per_page";
$res = mysql_query($sql) or die(mysql_error());

$i = 0;
while ($row=mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");
?>



<form method="post" action="" name="frmContactForm">
<div style="border:1px solid teal;margin:5px;padding:5px;background-color:white;">
	<table cellspacing="1" cellpadding="6" width="100%">
	<tr>
		<td align="left">From</td><td align="left"><strong><?php echo $row['from_email'];?></strong><div style="float:right;"><a href="javascript:if(confirm('Resend email?')) location.href = '?do=resend&id=<?php echo $row['id']; ?>';"><img src="images/resend_email.gif" border="0" alt="Resend" title="Resend"></a>&nbsp;&nbsp;<a href="javascript:if(confirm('Delete from database?')) location.href = '?do=delete&id=<?php echo $row['id']; ?>';"><img src="images/del.gif" border="0" alt="Delete" title="Delete"></a></div></td>
</tr>
<tr>
		<td align="left">To</td><td align="left"><strong><?php echo $row['to_email'];?></strong></td>
</tr>
</tr>
<tr>
		<td width="40" align="left">IP</td><td align="left"><?php echo $row['ip_from'];?></td>
</tr>
<tr>
		<td width="40" align="left">Date</td><td align="left"><?php echo $row['sent_date'];?></td>
</tr>
<tr>
		<td align="left">Status</td><td align="left"><?php if( $row['status_sent'] == 1){ echo "Success"; }else{ echo "<span style=\"color:#f00000;\">Error</span>"; };?></td>
</tr>
<tr>
		<td align="left">Message</td><td align="left"><?php echo $row['message_email'];?></td>




	</table>
</div>

<?php
}
?>


</form>
<br>


<?php
if($total_msgs > $msgs_per_page){?>
		<table>
			<tr>
				<td>Page:</td>
				<td>
				<?php
					$qsA = $_GET; unset($qsA['page'],$qsA['do']); $qs = "";
					foreach ($qsA as $k=>$v){
						 $qs .= "&$k=$v";
					}
					
					$url = "?page={@PAGE}&$qs";
					$pager = new pager($url, $total_msgs, $msgs_per_page, $page);
				
					$pager->outputlinks();
				?>
				</td>
			</tr>
		</table>
	<?php	}?>