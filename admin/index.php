<?php
/**
 * @author Sharif Ahmed
 * @Email winsharif@gmail.com
 * @http://esteemsoftbd.com
 * @copyright 2014
 * 
 */
 
include_once("headlink.php");
include_once("menu.php"); 

if (isset($_GET["page"])) {

	global $db,$database,$session,$footer_link,$flash;
	$page = $_GET["page"].".php";
	
	if(file_exists($page)){
	   
       validate_logon();	
		include $page;
	   
	}elseif (file_exists("../".$page)) {
		
		validate_logon();	
		include "../".$page;
        	
	} else {		
		include '404.php';	
	}
}

include_once("footer.php"); 

?>