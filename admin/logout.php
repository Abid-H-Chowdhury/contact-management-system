<?php 
require_once("class/initialize.php"); 

if($session->logout())
{
	if(isset($db)) { $db->close_connection(); } 
	header("location: index.php"); 
}
?>
