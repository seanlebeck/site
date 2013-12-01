<?php



if (!isAdmin())
{
	header("Location: index.php");
	exit;
}

$admin_mode = TRUE;


?>