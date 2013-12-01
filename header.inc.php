<table width="100%"  align="center" border="0" cellspacing="0" cellpadding="0">



<tr>

<td align="left" valign="middle" width="225">


<?php

$homeurl = buildURL("main", array(0));

?>
<a href="<?php echo $homeurl; ?>">
<img src="images/logo.png" border="0" style="margin-left:2px;border:0px;">
</a>

</td>


<td align="center" valign="middle" style="padding-right:15px;padding-top:5px;">
<a id="newadbutton" href="?view=selectcity&targetview=post&cityid=0&lang=en"><span>&nbsp;&nbsp;</span></a>


<?php
$cityurl = buildURL("main", array($xcityid, $xcityname));
?>

</td>
</tr>
</table>
