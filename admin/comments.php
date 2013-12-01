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
$offset = ($page-1) * $comments_per_page;

$sql_msgs = "SELECT COUNT(*)
			FROM $t_comments
			WHERE 1";

list($total_msgs) = mysql_fetch_row(mysql_query($sql_msgs));

if ($_GET['do'] == "delete"){
	if( $_GET['id'] ){
		
		$sql = "DELETE FROM $t_comments WHERE id = '$_GET[id]'";
		$res = mysql_query($sql) or die(mysql_error().$sql);
	}
}

else if ($_GET['do'] == "approve" && $row['visible'] == 0) {
	if( $_GET['id'] ){
	
		$sql = "UPDATE $t_comments SET visible = '1' WHERE id = '$_GET[id]'";
		$res = mysql_query($sql) or die(mysql_error().$sql);
	}
}

?>

<h2>Comment system messages: <font color="teal"><?php echo $total_msgs; ?></font></h2>


<?php if($msg) { ?><div class="msg"><?php echo $msg; ?></div><?php } ?>
<?php if($err) { ?><div class="err"><?php echo $err; ?></div><?php } ?>


<?php
$sql = "SELECT msg.*
		FROM $t_comments msg
		ORDER BY id DESC
		LIMIT $offset, $comments_per_page";
$res = mysql_query($sql) or die(mysql_error());

$i = 0;
while ($row=mysql_fetch_array($res))
{
	$i++;
	$cssalt = ($i%2 ? "" : "alt");
?>



<form method="post" action="" name="frmContactForm">
<div style="border:1px solid teal;margin:5px;padding:5px;background-color:white;">
	<table cellspacing="1" cellpadding="4" width="100%" style="margin:2px;background-color:azure;">


	
<tr>
	
	<td colspan="2">
	
		<div style="float:right;">
<?php if ($row['visible'] == 1) { echo "<font color='green'><b>Approved</b></font>"; } else { echo "<font color='red'><b>Not approved</b></font>"; } ?>		
&nbsp;&nbsp;&nbsp;
<a href="javascript:if(confirm('Delete from database?')) location.href = '?do=delete&id=<?php echo $row['id']; ?>';"><img src="images/del.gif" align="absmiddle" border="0" alt="Delete" title="Delete"></a>
<?php if ($row['visible'] == 0) { ?>
&nbsp;&nbsp;&nbsp;
<a href="javascript:if(confirm('Approve comment?')) location.href = '?do=approve&id=<?php echo $row['id']; ?>';"><img src="images/approve.gif" border="0" align="absmiddle" alt="Approve" title="Approve"></a>
<?php } ?>
</div>
		
	</td>
</tr>	
	
	
	<tr>
		<td align="right" style="width:150px;">Comment ID:</td><td align="left"><?php echo $row['id'];?></td>
</tr>

<tr>
		<td align="right" style="width:150px;">Page ID:</td><td align="left"><?php echo $row['object_id'];?></td>
</tr>

	<tr>
		<td align="right" style="width:150px;">Date posted:</td><td align="left"><?php echo $row['created'];?></td>
</tr>

<?php if ($row['sender_name']) { ?>

	<tr>
		<td align="right" style="width:150px;">Sender Name:</td><td align="left"><?php echo $row['sender_name'];?></td>
</tr>

<?php } ?>

<?php if ($row['sender_mail']) { ?>

	<tr>
		<td align="right" style="width:150px;">Sender Email:</td><td align="left"><font color="blue"><?php echo $row['sender_mail'];?></font></td>
</tr>

<?php } ?>

	<tr>
		<td align="right" style="width:150px;">Sender IP:</td><td align="left"><?php echo $row['sender_ip'];?></td>
</tr>
	
<tr>
		<td align="right" style="width:150px;">Comment Text</td><td align="left"><?php echo $row['comment_text'];?></td>
</tr>




	</table>
</div>

<?php
}
?>


</form>
<br>


<?php
if($total_msgs > $comments_per_page){?>
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
					$pager = new pager($url, $total_msgs, $comments_per_page, $page);
				
					$pager->outputlinks();
				?>
				</td>
			</tr>
		</table>
	<?php	}?>
	
	
	
