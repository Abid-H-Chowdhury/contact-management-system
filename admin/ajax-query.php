<?php
require_once("class/initialize.php"); 


//******************************************* Accounting Part Start****************************************************//
// show all account heads
if(isset($_GET["get_ac_head"])) { 
	$typeID = $_GET['get_ac_head'];
	$items = $db->result_all("SELECT * FROM ac_head WHERE type='$typeID' ORDER BY hname ASC");
        	echo '<option value="">Select Head</option>';
        foreach($items as $item) { 
		echo '<option value='.$item->id.'>'.$item->hname.'</option>';
		} 	
}
// show all account subheads
if(isset($_GET["get_ac_subhead"])) { 
	$headID = $_GET['get_ac_subhead'];
	$items = $db->result_all("SELECT * FROM ac_headsub WHERE head='$headID' ORDER BY sname ASC");
        	echo '<option value="">Select Sub Head</option>';
        foreach($items as $item) { 
		echo '<option value='.$item->id.'>'.$item->sname.'</option>';
		} 	
}

// show all account items from sub head
if(isset($_GET["get_ac_item_subheadID"])) { 
	$subheadID = $_GET['get_ac_item_subheadID'];
	$items = $db->result_all("SELECT * FROM ac_item WHERE shead='$subheadID' ORDER BY iname ASC");
            echo '<option value="">Select Item</option>';
        foreach($items as $item) { 
		echo '<option value='.$item->id.'>'.$item->iname.'</option>';
		} 	
}
if(isset($_GET["get_ac_item"])) { 
	$headID = $_GET['get_ac_item'];
	$items = $db->result_all("SELECT * FROM ac_item WHERE headID='$headID' ORDER BY iname ASC");
        	echo '<option value="">Select Item</option>';
        foreach($items as $item) { 
		echo '<option value='.$item->id.'>'.$item->iname.'</option>';
		} 	
}
if(isset($_GET["get_service_item_price"])) { 
	$itemID = $_GET['get_service_item_price'];
	$item = $db->result_one("SELECT price FROM ac_itemservice WHERE itemID='$itemID' AND status='1'");
    if(!empty($item->price)){
        echo $item->price; 
    } else { echo "Please assin price"; }
}

//******************************************* Accounting Part End****************************************************//
if(isset($_GET["get_thana"])) {
	$get_thana = $_GET['get_thana'];//district_id
	$Districts = $db->result_all("SELECT * FROM thana WHERE district_id='$get_thana'");
    	echo '<option value="">Select Thana</option>';
        foreach($Districts as $District) { ?>
			<option value="<?php echo $District->id; ?>"><?php echo $District->name; ?></option>
			<?php } 	
}
if(isset($_GET["get_union"])) {
	$thanaID = $_GET['get_union'];//thana id
	$Unions = $db->result_all("SELECT * FROM unions WHERE psID='$thanaID'");
    	echo '<option value="">Select Union</option>';
        foreach($Unions as $Union) { ?>
			<option value="<?php echo $Union->id; ?>"><?php echo $Union->name; ?></option>
			<?php } 	
}
?>
