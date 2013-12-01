<?php



require_once("initvars.inc.php");
require_once("config.inc.php");


?>
<div>

    <h2><?php echo $lang['POST_NOT_FOUND']; ?></h2>
    <?php echo $lang['POST_NOT_FOUND_DETAILS']; ?>
    
    <a href="index.php?cityid=<?php echo $xcityid; ?>"><?php echo $lang['BACK_TO_HOME']; ?></a>

</div>