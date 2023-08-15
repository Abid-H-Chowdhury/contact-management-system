<?php
/**
 * @author Sharif Ahmed
 * @Email winsharif@gmail.com
 * @http://esteemsoftbd.com
 * @copyright 2016
 * 
 */
include_once("headlink.php");
$userID = $_SESSION['user_id'];
global $date;
$sn=0;
$currentPage="setting-groups.php";
if(is_sadmin()){
	$statement="";
}else{
	$statement ="WHERE id < 1000 ";
}
$groups = $db->result_all("SELECT * FROM inv_group  {$statement} ORDER BY gname ASC");

if(isset($_POST["new_item"])) {
    
    $name = escape($_POST["name"]);
    $gorder = escape($_POST["gorder"]);
    
    $data = array('gname' => $name,'gorder' => $gorder);
    if(QB::table('inv_group')->insert($data)) {    
       $flash->success("New Group Added Successfully!!!",$currentPage);
	   exit();
    }
}
?>
<style>
.table thead>tr>th:nth-child(2), .table tbody>tr>td:nth-child(2) {
    text-align: left!important;
}
</style>
<div  class="print_div" id='print-div1'>
<div id="page-wrapper" style="margin: 0 0 0 0;" >    
<div class="row avoid">
    <center><b><?php  $flash->display(); ?></b></center>
	<div class="panel panel-info">
		<div class="panel-heading">
			Investigation Test's Group Settings
		</div>
		<!-- /.panel-heading -->
		<div class="panel-body">
			<form action="" method="POST">
				<div class="col-md-4">
					<div class="form-group">		
						<input type="text" required="Please Enter Group Name for set of test" name="name" class="form-control" placeholder="Group Name"/>
					</div>
				</div>
                <div class="col-md-2">
					<div class="form-group">		
						<input type="number" title="Group Order For Display" name="gorder" class="form-control" placeholder="Group Order"/>
					</div>
				</div>
				<div class="col-md-2">
					<center><input type="submit" name="new_item" class="btn-adds" onclick = "if (! confirm('Are you sure you want to Add Group Name?')) 
					{ return false; }" value="Add New"/></center>
				</div><br />
                
               <table class="table table-striped" id="table">
					<thead>
						<th>SN</th>
						<th>Name</th>
                        <th>Order</th>
                        <th>Edit</th>
					</thead>
					<tbody>
						<?php foreach($groups as $group) { $sn = $sn+1; ?>
						<tr>
							<td><?php echo $sn; ?></td>
							<td><?php echo $group->gname; ?></td>
                            <td><?php echo $group->gorder; ?></td>
                            <td style="text-align: center!important;"><?php echo popup_window(SITE_URL."lab/setting-details.php?groupID={$group->id}","","far fa-edit btn-updates");?></td>
						</tr>
						<?php  } ?>
					</tbody>
				</table> 
			</form>
		</div> 				
	</div>
</div>
</div>
</div>
<?php 
$footer_link ='
<link rel="stylesheet" type="text/css" href="'.SITE_URL.'includes/datatables/css/jquery.dataTables2.css">
<script type="text/javascript" language="javascript" src="'.SITE_URL.'includes/datatables/js/jquery.dataTables.min.js"></script>';	
?>
<?php include("../footer.php"); ?>