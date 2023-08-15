<?php
Rbac::isAthorized(228);
$sn = 0; $sn1=0; $sn2=0; $sn3=0;
$userID = $_SESSION['user_id'];
$per_page = 25;
global $date;

include("pagination.php");
$currentPage ="index.php?page=investigations";

$statement1 = " ac_item t1 INNER JOIN inv_test t2 ON t1.id=t2.itemID GROUP BY t2.itemID ";   
$total_item = $db->number_rows("SELECT * FROM  $statement1");
                                                       
if(isset($_GET["search_item"])) {
    
    $item_Name = isset($_GET["item_name"]) ? $_GET["item_name"] : NULL;
    $subhead_Name = isset($_GET["ac_subheadID"]) ? $_GET["ac_subheadID"] : NULL;
    $ac_headID = isset($_GET["ac_headID"]) ? $_GET["ac_headID"] : NULL;  
    $status = isset($_GET["status"]) ? $_GET["status"] : NULL;
    
    $statement = " ac_item t1 INNER JOIN inv_test t2 ON t1.id=t2.itemID WHERE t1.head='$ac_headID' "; 
    empty($item_Name) ? NULL : $statement .= " AND t1.iname LIKE'%$item_Name%' ";
    empty($subhead_Name) ? NULL : $statement .= " AND  t1.shead='$subhead_Name'";
    ($status=="") ? NULL : $statement .= " AND t2.status='$status' ";

    if(!is_sadmin()){
     $statement .=" AND  t2.system !=1 ";
     }
    $statement .= " GROUP BY t2.itemID ORDER BY t1.iname ASC ";    
    $ac_items = $db->result_all("SELECT t1.id AS itemID,t1.iname,t1.shead,t2.* FROM  $statement LIMIT {$startpoint} , {$per_page}");    
    $total_item = $db->number_rows("SELECT * FROM  $statement");
    
    $statement = " (SELECT COUNT(DISTINCT t2.itemID) as num FROM $statement) as temp";

    }
if(isset($_GET["delete-item"])) {
    $deletedID = $db->escape_value($_GET["delete-item"]);
    if($db->query("DELETE FROM ac_item WHERE id='$deletedID'")) {
        do_alert("Item Deleted Successfully!!!",$currentPage);
		exit();
    }
}
?>
<div id="page-wrapper">
<div class="row print_div" id='print-div1'>
    <div class="col-lg-12">
        <div class="panel panel-info">
            <div class="panel-heading avoid">
                <i class="fa fa-flask"></i> List Of Investigation
                <div class="pull-right avoid">
                    <button class="btn-white btn-blue">
                        <a href="investigation-new.php" onclick="popupwindow('investigation-new.php','Item', 1000, 480); return false;"> Add New Test/Parameter</a>
                    </button>  
                <?php if(isset($ac_items)) { ?>                    
                    <button class="btn-white btn-print avoid" id="print" type="button">
        				<i class="fa fa-print"></i> Print
        			</button>
                    <button class="btn-white btn-export dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                    <ul class="dropdown-menu arch-main" role="menu">
                        <li><a href="#" onclick="$('#table').tableExport({type: 'excel',fileName: 'Investigation_List_'+ToDay(),ignoreColumn:[8]});"> <img src='<?php echo SITE_URL;?>images/xls.png' alt="PNG" style="width:24px"/> Excel</a></li>
                        <li><a href="#" onclick="$('#table').tableExport({type: 'pdf',jspdf: {orientation: 'bestfit',format: 'a4',margins: {right: 10, left: 10, top: 30, bottom: 10},autotable: {tableWidth: 'auto'}},fileName: 'Investigation_List_'+ToDay(),ignoreColumn:[8]});"> <img src="<?php echo SITE_URL;?>images/pdf.png" width="24px" /> PDF(P)</a></li>
                        <li><a href="#" onclick="$('#table').tableExport({type: 'pdf',jspdf: {orientation: 'l',margins: {right: 10, left: 10, top: 30, bottom: 10},autotable: {tableWidth: 'auto'}},fileName: 'Investigation_List_'+ToDay(),ignoreColumn:[8]});"> <img src="<?php echo SITE_URL;?>images/pdf.png" width="24px" /> PDF(L)</a></li>
                        <li><a href="#" onclick="$('#table').tableExport({type: 'doc',fileName: 'Investigation_List_'+ToDay(),ignoreColumn:[8]});"> <img src='<?php echo SITE_URL;?>images/word.png' alt="PNG" style="width:24px"/> Word</a></li>
                        <li><a href="#" onclick="$('#table').tableExport({type: 'csv',fileName: 'Investigation_List_'+ToDay(),ignoreColumn:[8]});"> <img src='<?php echo SITE_URL;?>images/csv.png' alt="PNG" style="width:24px"/> CVC</a></li>
                        <li><a href="#" onclick="$('#table').tableExport({type: 'png',fileName: 'Investigation_List_'+ToDay(),ignoreColumn:[8]});"> <img src='<?php echo SITE_URL;?>images/png.png' alt="PNG" style="width:24px"/> PNG</a></li> 
                    </ul>
                <?php } ?>  
                </div>
            </div>
            <!-- /.panel-heading -->
            
            <div class="panel-body">    
                <!-- Custom Search Field For Print View Starts -->
            <div class="printView">
                <?php             
                    $methodValue=''; 
                    $subTitle='';
                    $lastsubTitle='';
                    echo common_print_header('Investigation List',$subTitle,$methodValue,$lastsubTitle);
                ?>
            </div>
            <!-- Custom Search Field For Print View Ends -->  
                <!-- Search Field Starts -->               
                <form action="" method="GET">
                    <input type="hidden" name="page" value="investigations" />
                    <div class="row avoid">
                        <div class="searchFieldWrapper clearfix">
                            <div class="col-md-2 col-sm-6">
            					<div class="form-group">
            						<select class="form-control" name="ac_headID" onclick="Ajax_Query(this.value,'ac_subhead','get_ac_subhead')"> 
                                        <option value="3" <?php if(isset($_GET['ac_headID']) && $_GET['ac_headID']==3) echo "SELECTED" ?>>Pathology</option>
                                        <option value="4" <?php if(isset($_GET['ac_headID']) && $_GET['ac_headID']==4) echo "SELECTED" ?>>Imaging </option>
            						</select>
            					</div>
                            </div>
                            <div class="col-md-3 col-sm-6">
            					<div class="form-group">
            						<select class="form-control" id="ac_subhead" name="ac_subheadID"> 
            							<?php echo isset($_GET['ac_subheadID']) ?  '<option value='.$_GET['ac_subheadID'].'>'.return_shead($_GET['ac_subheadID']).'</option>'  : ''; ?>
            						</select>
            					</div>
                            </div>
                            <div class="col-md-3 col-sm-6">
            					<div class="form-group">
            						<input type="text" name="item_name" class="form-control" value="<?php echo isset($_GET['item_name']) ? $_GET['item_name']  : NULL ?>" placeholder="Account's Item Name" />
                                    <span data-clear-input>&times;</span>
            					</div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                 <select class="form-control" name="status">
                                    <option value="" <?php if(isset($_GET['status'])&&($_GET['status']=='')){ echo "SELECTED"; } ?>>All Test</option>                             
                                    <option value="1" <?php if(isset($_GET['status'])&&($_GET['status']==1)){ echo "SELECTED"; } ?>>Active Test</option>
                                    <option value="0" <?php if(isset($_GET['status'])&&($_GET['status']=='0')){ echo "SELECTED"; } ?>>Inactive Test</option>
                                 </select>
                              </div>
                           </div>
                            <div class="col-md-1 col-sm-4">
            					<div class="form-group">
            						<input type="submit" name="search_item" class="btn-search avoid" value="Search" />
            					</div>
                            </div>                        
                        </div>                        
                    </div> 
                <!-- Search Field Ends -->                 
            <div class="quickInfoSectionWrapper">
                <!-- Search Quick Info Section Starts -->
                <?php if(isset($ac_items)) { ?>  
                <div class="row">  
                    <div class="searchQuickInfoSectionWrapper">  
                        <div class="col-md-4 col-lg-4">
                            <div class="searchQuickInfoSection">
                                <div class="sectionLeft">
                                    <i class="fas fa-flask"></i> Total Investigations Setup
                                </div>
                                <div class="sectionRight">
                                    <?php echo $total_item; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>  
                <!-- Search Quick Info Section Ends -->

        <?php if(isset($ac_items)) { ?>                       
            <div class="">
                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th class="text-left">Test/Parameter Name</th>
                                <th>Group</th>
                                <th>Unit</th>
                                <th title="Default Result">D.Result</th>
                                <th>D.Range</th>
                                <th>M.Range</th>
                                <th>F.Range</th>
                                <th>Order</th>
                                <th class="avoid">Status</th>
                                <th class="avoid">Edit</th>
                            </tr>
                        </thead>
                        <?php foreach($ac_items as $ac_item) { $sn1=0;                                 
                            
                            $test_lists = QB::table('inv_test')->findAll('itemID',$ac_item->itemID);

                           if(!empty($test_lists)){            
                        ?>  
                            <tr>
                                <td></td>
                                <td class="text-left"><b><i><?php echo $ac_item->iname; ?> (<?php echo return_shead($ac_item->shead); ?>)</i></b></td>                                  
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class="avoid"></td>
                                <td class="avoid"></td>
                            </tr>   
                        <?php foreach($test_lists as $test) { $sn1=$sn1+1; ?>         
                        <tbody>
                            <tr class="gradeA">
                                <td><?php echo $sn1; ?></td>
                                <td class="text-left"><?php echo $test->tname; ?></td>
                                <td><?php echo !empty($test->groupID) ? Lab::group_return($test->groupID) : ""; ?></td>
                                <td><?php echo !empty($test->unitID) ? Lab::unit_return($test->unitID) : ""; ?></td>
                                <td><?php echo $test->default_value; ?></td>
                                <td><?php echo $test->drang; ?></td>
                                <td><?php echo $test->mrang; ?></td>
                                <td><?php echo $test->frang; ?></td>
                                <td><?php echo $test->torder; ?></td>
                                <td class="avoid"><?php echo convert_status($test->status); ?></td>
                                <td class="avoid"><a class="btn-updates" href="investigation-details.php?testid=<?php echo $test->id; ?>" onclick="popupwindow('investigation-details.php?testid=<?php echo $test->id; ?>',
                             'Details', 1100, 480); return false;"><i class="fas fa-edit"></i></a></td>
                            </tr>
                        <?php } } } ?>
                        </tbody>
                    </table>
                </div>
                <?php echo '<div class="row avoid-this">'; 
					echo '<div class="col-md-10 col-xs-12 col-md-offset-2">'; 
					echo  $db->pagination($statement,$per_page,$page,curPageURL());   ?>
					</div>
				</div>                             
			</div>
            <?php } ?>
			</form>
		</div>
        </div>

	</div>
</div>

</div>
</div>
</div>
<!-- /#page-wrapper -->
 <?php 
$footer_link ='
<link rel="stylesheet" type="text/css" href="'.SITE_URL.'includes/datatables/css/jquery.dataTables2.css">
<script type="text/javascript" language="javascript" src="'.SITE_URL.'includes/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="'.SITE_URL.'includes/exportTable.js"></script>	
';	
?>