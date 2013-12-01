<?php

require_once("admin.inc.php");



function printStat($sql) {
	list($stat) = mysql_fetch_row(mysql_query($sql));
	$stat += 0;
	echo number_format($stat);
}


?>



<table align="left" valign="top">
<tr>
<td align="left" valign="top">
<font color="green"><b><?php printStat("SELECT COUNT(*) FROM $t_ads WHERE enabled = '0'"); ?></b></font>
</td>
</tr>
</table>




	
