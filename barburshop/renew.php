<?php

require_once("initvars.inc.php");

require_once("config.inc.php");

require_once("userauth.inc.php");

 $res_sql = "SELECT createdon, ip FROM ".$t_ads." WHERE UNIX_TIMESTAMP(expireson) <= '".(time()+$expire_ads_ahead)."' AND adid= '".$adid."'";
 $resc = mysql_query($res_sql);

if (!$auth) exit;

$data = $ad;

$showoptions = TRUE;

if( $_GET['target'] == "renew_now" ) 
{ 
       
		$renew_days = $expire_ads_after_default;

		$expiry = time()+($renew_days*24*60*60);

		$expiry_dt = date("Y-m-d H:i:s", $expiry);
		
		$new_create = time();
		
		$new_create_dt = date("Y-m-d H:i:s", $new_create);
		
		//echo $new_create_dt;  // testing
		
		if ( mysql_fetch_array($resc) > 0 )
		{
		
			switch ($update_ad_creation) 
			  {
				case 0:
					$update_sql = "UPDATE ".$t_ads." SET expireson = '".$expiry_dt."', reminder='0' WHERE adid = '".$adid."'";
					mysql_query($update_sql);
					break;
				case 1:
					$update_sql = "UPDATE ".$t_ads." SET expireson = '".$expiry_dt."', createdon='".$new_create_dt."', reminder='0' WHERE adid = '".$adid."'";
					mysql_query($update_sql);
					break;
			  }

		
		

		
		//echo $update_sql; // testing

echo '<div class="post_note"><b>'.$lang['RENEW_SUCCESS'].'</b></div><br /><br />';
         
		}
}

?>


<h2 class="postclass"><?php echo $lang['RENEW_YOUR_AD']; ?></h2>

<div style="border:1px dotted silver; padding:10px;background-color:#FAFAFA;">

<h3><?php echo $data['adtitle']; ?></h3>

<?php echo $data['addesc']; ?>...<br><br>

<b><?php echo $lang['AD_EXPIRES_ON']; ?></b> <?php echo QuickDate($data['expireson_ts']) . ' &nbsp;(' . date("g:i A", $data['expireson_ts']) .')'; ?>

</div>
<br><br>

<?php 
 


  //echo $res_sql;
 
 if (  mysql_fetch_array($resc) > 0 
   // 1 == 1  //testing
	)
 {
 ?>

<form action="index.php?view=renew&target=renew_now&cityid=<?php echo $xcityid; ?>" method="post">  
<input name="adid" type="hidden" value="<?php echo $adid; ?>" />
<table class="postad" cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
  <td>
   If you wish to extend your current ad for an additional <?php echo $expire_ads_after_default; ?> days, please click on the Renew Ad button located below.
   <br /><br />
  </td>
</tr>
</table>
<br />

<button type="submit"><?php echo $lang['RENEW_AD']; ?></button>
&nbsp;
<button type="button" onclick="location.href='index.php?view=<?php echo $adview; ?>&adid=<?php echo $adid; ?>&cityid=<?php echo $xcityid; ?>';">Cancel</button><br><br>

</form>

<br /><br />

<?php } 

/*else
{
echo '<div class="err">'.$lang['RENEW_ERROR'].'</div><br /><br />';
} */


?>

<a href="?view=main&cityid=<?php echo $xcityid; ?>"><?php echo $lang['BACK_TO_HOME']; ?></a>