<?php

/**
 * @author Sharif Ahmed
 * @copyright 2019
 */

//require_once("class/initialize.php");
$time = date("Y-m-d H:i:s");



/*

$all_invoices = $db->result_all("SELECT t1.* FROM `ac_invoice` t1 LEFT JOIN ac_invoice_set t2 ON t1.invNo=t2.invNo WHERE t1.`date` BETWEEN '2020-07-08' AND '2021-07-09' AND t2.id is null");

foreach($all_invoices AS $inv){
    
    Account::insert_invoice_set($inv->uhid,$inv->invNo,$inv->amount,$inv->discount,$inv->paid,$inv->date,NULL,NULL,NULL);
}

$sql="
  INSERT INTO ph_stock ('medID', 'stock', 'stock1', 'time') 
  VALUES 
";

for($i = 1; $i < 17853; $i++){
  $sql.="($i, '0', '0', '$time'),";
}

$sql=rtrim($sql, ",");



$subheads = $db->result_all("SELECT * FROM `customer` ");

foreach($subheads as $shd){
    $id = $shd->id;
    $cid = $shd->cid;
    $password = password_hash($cid, PASSWORD_DEFAULT);
    //if(stristr($cid, 'C') == TRUE){
        $db->query("Update customer SET password='$password' WHERE id='$id'");
    //}
}
*/

/*
echo GetMACAdd();
 
function GetMACAdd(){
  ob_start();
  system('getmac');
  $Content = ob_get_contents();
  ob_clean();
  return substr($Content, strpos($Content,'\\')-20, 17);
}



$subheads = $db->result_all("SELECT * FROM `unions` ");

foreach($subheads as $data){

    $data2 = $db->result_one("SELECT bn_name,url FROM `unions2` WHERE name='$data->name' ");
  	if(!empty($data2)){
      $db->query("Update unions SET bname='$data2->bn_name',url='$data2->url' WHERE id='$data->id'");
    }
    
}
*/
$subheads = $db->result_all("SELECT * FROM `thana` ");

foreach($subheads as $data){

    $name = escape($data->name);
    $data2 = $db->result_one("SELECT bn_name,url FROM `upazilas` WHERE name='$name' ");
  	if(!empty($data2)){
      $db->query("Update  thana SET bname='$data2->bn_name',url='$data2->url' WHERE id='$data->id'");
    }
    
}
?>