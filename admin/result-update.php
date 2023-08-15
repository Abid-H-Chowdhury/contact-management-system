<?php
/**
 * @author Sharif Ahmed
 * @Email winsharif@gmail.com
 * @http://esteemsoftbd.com
 * @copyright 2018
 * 
 */

$userID = $_SESSION['user_id'];

global $date,$time;
$sn=0;

if(isset($_GET["invID"]) && !empty($_GET["invID"])){
    $invID = escape($_GET["invID"]);
    
    $test_details = QB::query("SELECT t1.id id,t1.itemID,t1.labID,t1.status,t2.*,t3.iname FROM investigation t1 
                               INNER JOIN inv_details t2 ON t1.id=t2.invID 
                               INNER JOIN ac_item t3 ON t1.itemID=t3.id 
                                WHERE t1.id=? ", array($invID))->first();
   
    $InvItemID = $test_details->itemID;       

    $test_list = QB::table('inv_test')->find($InvItemID,'itemID');
    $currentPage="index.php?page=result-update&invID={$invID}";
   
}

 $template =1;

if(isset($_POST["save_result"])) {
        foreach ($_POST['data'] as $row) 
        { 
            $testID = $row['testID'];
            $result = $row['result'];
            
            /// Update result
            if(isset($row['resultID']) && !empty($row['resultID'])){
                $resultID = $row['resultID'];
                $dataUpdate = array('result'=>$result);
                $resultReady = QB::table('inv_result')->where('id', $resultID)->update($dataUpdate);
            }elseif(!empty($result)){ // insert result
                $dataTest = array('invID'=>$invID,'testID'=>$testID,'result'=>$result);
                $resultReady = QB::table('inv_result')->insert($dataTest);
            }
            
        }
  
    $data = array('time'=>$time,'rby'=>$userID, 'userID' =>$userID);
    
    if((QB::table('inv_details')->where('invID', $invID)->update($data)) && isset($resultReady)) {  
          
       $flash->success("Investigation Result Updated Successfully!!!",$currentPage);
	   exit();
    }      
} 
if(isset($_GET["invID"]) && !empty($_GET["invID"]) && !empty($test_details)){ 
?>

<div id="page-wrapper">
<div class="row " id='print-div1'>
<div class="col-lg-12">
	<div class="panel panel-info">
		<div class="panel-heading">
		    Investigation Result Entry
		</div>
        <center><b><?php $flash->display(); ?></b></center>
		<!-- /.panel-heading -->
		<div class="panel-body">
			<form action="" method="POST">
                <!-- Patient Info For Screen View Only Starts -->
                <div class="row avoid">                    
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="table-responsive payment-info table-right">
                            <table class="table table-striped">
                                <thead class="stitle">
                                    <tr>
                                        <th colspan="3">Investigation Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong><span class="colorGreen"><?php echo $test_details->iname;?> (LabID: <?php  echo $test_details->labID; ?>)</span></strong></td>
                                        <td><strong>Result Entry Time:</strong> <span> <?php  echo return_time($test_details->rtime);  ?></span></td>
                                        <td><strong>Result Sent To Server Time:</strong> <span><?php  echo return_time($test_details->ddate);  ?></span></td>
                                    </tr> 
                                </tbody>                                            
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Patient Info For Screen View Only Ends -->

            <div class="row" style="margin-top: 1em;">
    <?php if($template==1){
            
           // $test_group_listes = $query = QB::table('inv_test')->where('itemID', '=',$InvItemID)->get();
    ?>     
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="table-responsive">
                    <table class="table table-striped investigationResultTable">
						<thead>
						  <th class="text-left" width="80%">Test/Parameter</th>
                          <th width="20%" class="text-center">Result</th>                      
						</thead>
						<tbody>
                    <?php 
                            $test_listes =  $db->result_all("SELECT t1.*, t2.id as resultID, t2.testID, t2.result FROM inv_test t1 LEFT JOIN inv_result t2 ON t1.id=t2.testID AND t2.invID='$invID'");
                        foreach ($test_listes as $test) {
                         ?>     			
                            <tr class="gradeA">
                                <td class="text-left"><span style="padding-left: 15px"><?php echo $test->tname;?></span></td>
                                <td class="text-center"><input type="text" name="data[<?php echo $test->id ; ?>][result]" value="<?php echo !empty($test->result)? clean_escape($test->result) : "";?>" class="form-control text-center" /></td>
                                <input type="hidden" name="data[<?php echo $test->id ; ?>][testID]" value="<?php echo $test->id ; ?>" />
                               <?php    if(!empty($test->result)){ ?> 
                                <input type="hidden" name="data[<?php echo $test->id ; ?>][resultID]" value="<?php echo $test->resultID ; ?>" />
                                <?php } ?>
                                
                            </tr>
                    <?php }  ?>               	
						</tbody>											
					</table>
                  
				</div>
			</div>
    <?php } ?> 
        
            <div class="clearfix">
                
                <div class="pull-right clearfix" style="margin: 10px 20px 10px;"><input type="submit" name="save_result" class="btn-save" onclick="if (! confirm('Are you sure you want to Save?')) 
                         { return false; }" value="Save" /></div>
          </div>
          </div>
            
			</form>
		</div>
	</div>
</div>
</div>
</div>

<style type="text/css">
.customer-info.table-left .table, .payment-info.table-right .table {
    min-height: 6em;
}
textarea.form-control {
    resize: both;
}
.form-control {
    margin-bottom: 0px;
}
.payment-info.table-right .stitle span.pull-right {
    background: #fff;
    color: #555;
    padding: 1px 10px;
    border-radius: 10px;
}
.panel.panel-info .panel-body .row.avoid:first-child, .panel.panel-info .panel-body>form:first-child div.row.avoid {
    background: transparent;
}
.investigationResultTable input.form-control {
    max-width: 100px;
    display: inherit;
}
</style>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>
<!-- /#page-wrapper -->
<?php } 
$footer_link ='<script src="'.SITE_URL.'includes/tinymce/tinymce.min.js"></script>
<script type="text/javascript"> 
    tinymce.init({ selector:"textarea" });
</script>';	?>