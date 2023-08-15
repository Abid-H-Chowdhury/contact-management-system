<?php
/**
 * @author Sharif Ahmed
 * @Email winsharif@gmail.com
 * @http://esteemsoftbd.com
 * @copyright 2014
 * 
 */
 
require_once("class/initialize.php");

if(isset($_GET["labID"]) && !empty($_GET["labID"])){
        
	$labID = $_GET["labID"];
	
	if(!empty($labID)){
	 
		 $investigationInfo =  $db->result_one("SELECT t1.id FROM investigation t1 WHERE t1.labID='{$labID}' ");
		 $investigation_results = $db->result_all("SELECT t1.labID, t2.server_ac_itemID,t5.server_inv_testID,t4.result FROM `investigation` t1 LEFT JOIN ac_item t2 ON t1.itemID=t2.id LEFT JOIN inv_details t3 ON t1.id=t3.invID LEFT JOIN inv_result t4 ON t1.id=t4.invID LEFT JOIN inv_test t5 ON t4.testID=t5.id WHERE t1.labID='{$labID}'");
	  
		if(!empty($investigation_results)){

		   $array_data= serialize($investigation_results);
		   $server_url = trim(get_setting("PRIMARY_API_SERVER_REQUEST_URL_1"));
		   //The URL with parameters / query string.

			$url = "{$server_url}?lisdata={$array_data}";
			$url = preg_replace("/ /", "%20", $url);
			//Once again, we use file_get_contents to GET the URL in question.
			$contents = file_get_contents($url);
			
			//If $contents is not a boolean FALSE value.
			if($contents == '1'){
				
				$db->query("UPDATE investigation SET  status='4' WHERE id = '{$investigationInfo->id}' ");
				$db->query("UPDATE inv_details SET  ddate='$time' WHERE invID = '{$investigationInfo->id}' ");
				 $flash->success(" LAB ID {$labID} Test Result Sent To Online Server Successfully!!!","index.php?page=dashboard");
			}else{
				$flash->error($contents,"index.php?page=dashboard");
			}
		}
	}
}
?>