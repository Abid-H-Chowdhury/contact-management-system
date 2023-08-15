<?php
Rbac::isAthorized(1299);
$sn=0;
global $date,$userID,$time;
require_once("pagination.php");
if(isset($_GET["fmr-invoice_list"]) || isset($_GET["uhid"]) || isset($_GET["from_date"])) {
    
    $uhid = isset($_GET["uhid"]) ? escape($_GET["uhid"]) : NULL;
    $passport = isset($_GET["passport"]) ? escape($_GET["passport"]) : NULL; 
    $agentID = isset($_GET["agent"]) ? escape($_GET["agent"]) : NULL; 
    $itemID = isset($_GET["itemID"]) ? escape($_GET["itemID"]) : NULL;
    $labstatus = isset($_GET["labstatus"]) ? escape($_GET["labstatus"]) : NULL;
    $agentcopy = isset($_GET["patientcopy"]) ? escape($_GET["patientcopy"]) : NULL;
    $from_date = isset($_GET["from_date"]) ? convert_date($_GET["from_date"]) : NULL;
    $to_date = isset($_GET["from_date"]) ? $from_date : NULL;
    $per_page = isset($_GET['per_page'])? escape($_GET["per_page"]): 25;   
    $page = (int)(!isset($_GET["pages"]) ? 1 : $_GET["pages"]);
    if ($page <= 0) $page = 1;
    $startpoint = ($page * $per_page) - $per_page;
    
    $operation = isset($_GET["operation"]) ? $_GET["operation"] : NULL;

    if(!empty($from_date) && empty($to_date)) { 
    
        $from_date = $from_date;    
        $to_date = date("Y-m-d");
    }
    
    if (!empty($uhid) && is_numeric($uhid)) 
    {  
        $patient_Info = $db->result_one("SELECT * FROM patients WHERE id = '$uhid' ");
        if(empty($patient_Info)){
                $flash->error("Given Patient UHID is not correct!!!");
        }
    }
    if(empty($uhid) && empty($itemID) && empty($from_date) && empty($passport) && empty($agentID) && empty($labstatus)){
        $flash->error("Select any field to search invoice","index.php?app=fmr-invoice-list");
    }   
    $whereArr = array();
    if($uhid != "") $whereArr[] = " t1.uhid = '{$uhid}' ";
    if($itemID != "") $whereArr[] = " t1.itemID = '{$itemID}' ";
    /*if($passport != "") $whereArr[] = " t2.passport = '{$passport}' ";*/
  /*  if($agentID != "") $whereArr[] = " t2.agentID = '{$agentID}' ";*/
    if($from_date != "") $whereArr[] = " (t1.date BETWEEN '$from_date' AND '$to_date') ";   
    if($labstatus != "") $whereArr[] = " t6.status = '{$labstatus}' ";
    
    if($operation == "1"){ $whereArr[] = " t1.due='0' "; }
    elseif($operation == "2"){ $whereArr[] = " t1.paid='0' "; }
    elseif($operation == "3"){ $whereArr[] = " t1.due<>'0' AND t1.paid<>'0' "; }
    elseif($operation == "4"){ $whereArr[] = " t1.due<>'0' "; }
    
    if($agentcopy==1){ $whereArr[] = " t4.amount IS NULL ";  }

    $whereStr = implode(" AND ", $whereArr);
    $agentSql = "";
    $agentSelectSql = "";
    if(empty($agent)){
        $agentSql = " LEFT JOIN agents t7 ON t2.agentID=t7.id LEFT JOIN `customer` t8 ON t7.cid=t8.cid ";
        $agentSelectSql = ", t8.name AS agentName";
        }
        $statement = " `ac_invoice` t1 LEFT JOIN `patients` t3 ON t1.uhid=t3.id 
                        LEFT JOIN `patients_info` t2 ON t3.id=t2.uhid 
                        LEFT JOIN `ph_refund` t4 ON t1.id=t4.invoiceID  
                        LEFT JOIN `ac_invoice_set` t5 ON t1.invNo=t5.invNo 
                        LEFT JOIN `investigation` t6 ON t1.id=t6.invoiceID                         
                        {$agentSql}
                        WHERE {$whereStr}  ";
                       
        $statement .= " AND t1.uhid REGEXP '^[0-9]+$' ORDER BY t1.id DESC ";
        $invoices_lists = $db->result_all("SELECT t1.id,t1.uhid,t1.invNo,t1.due,t1.paid,t2.passport,t3.name, t3.dob, t3.sex,  t4.amount refundAmount,t5.date,t6.status labStatus, t5.id invoice_setID, t5.note passportStatus, t1.note remarks, t6.id investigatonID {$agentSelectSql} FROM {$statement} LIMIT {$startpoint} , {$per_page} ");
    
        $sum_invoice = $db->result_one("SELECT COUNT(t1.id) AS totalCount  FROM {$statement} ");
        /*$billed_amount = $sum_invoice->total-$sum_invoice->discount;  */ 



        $statement1 = " `ac_invoice` t1 LEFT JOIN `patients` t3 ON t1.uhid=t3.id 
                        LEFT JOIN `patients_info` t2 ON t3.id=t2.uhid 
                        LEFT JOIN `ph_refund` t4 ON t1.id=t4.invoiceID  
                        LEFT JOIN `ac_invoice_set` t5 ON t1.invNo=t5.invNo 
                        LEFT JOIN `investigation` t6 ON t1.id=t6.invoiceID 
                        LEFT JOIN inv_result t9 ON t9.invID=t6.id                       
                        {$agentSql}
                        WHERE {$whereStr} AND t1.uhid REGEXP '^[0-9]+$' ";  

        $unFitPassport = $db->result_one("SELECT COUNT(t9.id) AS totalUnFit  FROM {$statement1}  AND t9.result = 'UNFIT'");
        $fitPassport = $db->result_one("SELECT COUNT(t9.id) AS totalFit  FROM {$statement1}  AND t9.result = 'FIT'");

        $test_group_listes = QB::table('inv_test')->where('itemID', '=',$itemID)->groupBy('groupID')->get();        

  
  
}

if(isset($_POST["bulk_update"])){
if(!empty($_POST['checkbox'])){

 

       $invIDs = $_POST['checkbox']; //from name="checkbox[]"
    
        $countCheck = count($invIDs);
            for($i=0;$i<$countCheck;$i++){

               $invoiceID  = $invIDs[$i];           
              
              foreach ($_POST['data'] as $key => $parameter) {
                if($invoiceID==$key){
                $p=0;
  
              foreach ($parameter['result'] as  $testResult) {

                       $testID= $parameter['testID'][$p];
                       $investigationID= $parameter['invID'][$p];
                       $invResultId= $parameter['invResultId'][$p];
                       $labStatus= $parameter['labStatus'][$p];

                
               if(!empty($invResultId)){
                  $checkResult = $db->result_one("SELECT * FROM inv_result WHERE id='$invResultId' AND invID='$investigationID' AND testID='$testID' ");
                    
              if(!empty($checkResult)){
                  $paraData  = array('result'=>escape($testResult));
                
                   $investigationData = array('time' => $time, 'userID' => $userID );
                  QB::table('inv_result')->where('id', $invResultId)->update($paraData);
                if($labStatus <3){
                $dataInv = array('status'=>3 );
                QB::table('investigation')->where('id', $investigationID)->update($dataInv);
                    }

                  $checkResult1 = $db->result_one("SELECT * FROM inv_result WHERE id='$invResultId'");
                      
                  QB::table('inv_details')->where('invID', $investigationID)->update($investigationData);
                  $success=1;
                }
               }else{

                $paraData  = array('invID'=>escape($investigationID),'testID'=>escape($testID),'result'=> escape($testResult));

               $checkResult = $db->result_one("SELECT * FROM inv_result WHERE invID='$investigationID' AND testID='$testID' ");
                    
              if(empty($checkResult)){
                $resultinsert = QB::table('inv_result')->insert($paraData);
                  $checkInvStatus = $db->result_one("SELECT * FROM investigation WHERE id='$investigationID'");
                 
                  if($checkInvStatus->status < 4 ){ //delivered
                    $dataInv = array('status'=>3 );
                    QB::table('investigation')->where('id', $investigationID)->update($dataInv);
                 }

                $investigationData = array('rtime'=>$time,'rby'=>$userID,'time' => $time, 'userID' => $userID );
                QB::table('inv_details')->where('invID', $investigationID)->update($investigationData);
                $success=1;
            }
                 }
               $p++;
              }
            }
        }

     }
          
        
        if($success==1){
            $flash->success("Save Successfully!!!","index.php?page=bulk-result-manage&uhid={$uhid}&operation={$operation}&labstatus={$labstatus}&agent={$agentID}&itemID={$itemID}&from_date={$_GET['from_date']}&per_page={$per_page}&patient_acount=SEARCH");
        }else{
            $flash->error("Fail. This Test is not updated!!!");
        }       
    }else
    {                   
        echo " <script type='text/javascript'>      
            alert('Click on check box to collect sample.');
            </script> ";
    }
}
?>

<div id="page-wrapper">
<div class="row print_div" id='print-div1'>
<div class="col-lg-12">
 <center><b><?php $flash->display(); ?></b></center>
<div class="panel panel-info">
    <div class="panel-heading avoid">
        <i class="fa fa-flask"></i> Pathology Bulk Result Manage <small>Here Pathology test result insert and update options, which result already inserted its checked </small>                    
        <?php if(isset($invoices_lists)) { ?> 
        <!-- <div class="pull-right avoid">
            <button class="btn-white btn-print avoid" id="print" type="button">
                <i class="fa fa-print"></i> Print
            </button>
        </div> -->
        <?php } ?> 
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <!-- Search Field Starts -->
        <form action="" method="GET">
            <div class="row avoid">
                <input type="hidden" name="page" value="bulk-result-manage" />
                <div class="searchFieldWrapper clearfix">
                    <div class="col-lg-4 col-md-4 col-sm-6 <?php if(empty($_GET['itemID'])) echo "avoid"; ?>">
                        <div class="form-group">
                            <div class="chosen-focus">
                                <select name="itemID" required="" class="form-control chosen-select" title="Account Item">
                                    <option value="">Select Test Item *</option>
                                    <?php foreach(Account::item_list(1,3) as $item) {
                                        echo '<option value="'.$item->id.'"';  if(isset($_GET['itemID']) AND $_GET['itemID']==$item->id) echo 'selected="selected"'; echo '>'.$item->iname.'</option>';          
                                    }  ?>                                         
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 <?php if(empty($_GET['uhid'])) echo "avoid"; ?>">
                        <div class="form-group">
                            <input title="Patient Name Or Cell" type="text" data-type="patient_name" class="autocomplete_Patient form-control " id="patientsName" placeholder="Patient Name Or Cell" />
                            <span data-clear-input>&times;</span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-6 <?php if(empty($_GET['uhid'])) echo "avoid"; ?>">
                        <div class="form-group">
                            <input title="Patient ID" type="text" data-type="patient_code" class="autocomplete_Patient form-control " id="patient_id" name="uhid" value="<?php if(isset($_GET['uhid'])) echo $_GET['uhid']; ?>" placeholder="Patient UHID" />    
                                <span data-clear-input>&times;</span> 
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-6 <?php if(empty($_GET['operation'])) echo "avoid"; ?>">
                        <div class="form-group">
                            <select name="operation" class="form-control">
                                <option value="">All AC Status</option> 
                                <option value="1" <?php if(isset($_GET['operation']) AND $_GET['operation']==1) echo 'selected="selected"'; ?>>Paid Only</option> 
                                <option value="2" <?php if(isset($_GET['operation']) AND $_GET['operation']==2) echo 'selected="selected"'; ?>>Unpaid Only</option>
                                <option value="3" <?php if(isset($_GET['operation']) AND $_GET['operation']==3) echo 'selected="selected"'; ?>>Partial Only</option>  
                                <option value="4" <?php if(isset($_GET['operation']) AND $_GET['operation']==4) echo 'selected="selected"'; ?>>Unpaid+Partial</option>  
                            </select>
                            <span data-clear-input>&times;</span>
                        </div>
                    </div>                    
                    <div class="col-lg-2 col-md-2 col-sm-6 <?php if(empty($_GET['labstatus'])) echo "avoid"; ?>">
                        <div class="form-group">
                            <select class="form-control" name="labstatus"> 
                                <option value="" <?php if(isset($_GET['labstatus']) && $_GET['labstatus']=='') echo "SELECTED" ?>>All Report Status</option>
                                <option value="1" <?php if(isset($_GET['labstatus']) && $_GET['labstatus']==1) echo "SELECTED" ?>>Invoiced</option>
                                <option value="2" <?php if(isset($_GET['labstatus']) && $_GET['labstatus']==2) echo "SELECTED" ?>>Sample Collected</option>
                                <option value="3" <?php if(isset($_GET['labstatus']) && $_GET['labstatus']==3) echo "SELECTED" ?>>Result Ready</option>
                                <option value="4" <?php if(isset($_GET['labstatus']) && $_GET['labstatus']==4) echo "SELECTED" ?>>Delivered</option>
                                <option value="5" <?php if(isset($_GET['labstatus']) && $_GET['labstatus']==5) echo "SELECTED" ?>>Cancel</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 <?php if(empty($_GET['from_date'])) echo "avoid"; ?>"> 
                        <div class="form-group">
                            <input title="Start Date *" type="text" required="" class="form-control" name="from_date" placeholder="Start Date *" id="date1" <?php if(isset($_GET['from_date']) && !empty($_GET['from_date'])) { echo 'value="'.$_GET['from_date']. '"'; }elseif(isset($_GET['from_date']) && empty($_GET['from_date'])) { echo 'value=""'; }else{ echo 'value="'.date("d-m-Y").'"'; }  ?>/>
                             <span data-clear-input>&times;</span>
                        </div>
                    </div>                    
                    <div class="col-lg-1 col-md-2 col-sm-6">
                        <div class="form-group">
                            <select class="form-control"  name="per_page" required="">
                                <option value="100" <?php if(isset($_GET['per_page'])) { if($_GET['per_page'] =='100') echo "SELECTED"; } ?>>100 Rows</option>
                                <option value="250" <?php if(isset($_GET['per_page'])) { if($_GET['per_page'] =='250') echo "SELECTED"; } ?>>250 Rows</option>
                                <option value="500" <?php if(isset($_GET['per_page'])) { if($_GET['per_page'] =='500') echo "SELECTED"; } ?>>500 Rows</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-4">
                        <div class="form-group">
                            <input type="submit" name="search_item" class="btn-search avoid" value="Search" />
                        </div>
                    </div>
                </div>
            </div>
        </form>  
        <!-- Search Field Ends -->

        <!-- Custom Search Field For Print View Starts -->
        <div class="row printView">
            <?php             
                $methodValue=''; 
                $subTitle='';
                $lastsubTitle='';
                echo common_print_header('Investigation Result List',$subTitle,$methodValue,$lastsubTitle);
            ?>
        </div>
        <div class="row printView">
            <div class="customSearchField">
                <?php if(!empty($_GET['ac_headID'])) { ?>
                <div class="col-xs-3">
                    <div class="SearchItem">
                        <h5>Head</h5>
                        <h4><?php if(isset($_GET['ac_headID'])) { if($_GET['ac_headID'] =='3') echo "Pathology" ; } ?></h4>
                        <h4><?php if(isset($_GET['ac_headID'])) { if($_GET['ac_headID'] =='4') echo "Imaging" ; } ?></h4>
                    </div>
                </div>
                <?php } ?>

                <?php if(!empty($_GET['ac_subheadID'])) { ?>
                <div class="col-xs-3">
                    <div class="SearchItem">
                        <h5>Sub Head</h5>
                        <h4><?php echo isset($_GET['ac_subheadID']) ? return_shead($_GET['ac_subheadID']) : ''; ?></h4>
                    </div>
                </div>
                <?php } ?>

                <?php if(!empty($_GET['status'])) { ?>
                <div class="col-xs-3">
                    <div class="SearchItem">
                        <h5>Status</h5>
                        <h4><?php if(isset($_GET['status'])) { if($_GET['status'] =='3') echo "Result Ready" ; } ?></h4>
                        <h4><?php if(isset($_GET['status'])) { if($_GET['status'] =='4') echo "Delivered" ; } ?></h4>
                    </div>
                </div>
                <?php } ?>

                <?php if(!empty($_GET['from_date'])) { ?>
                <div class="col-xs-3">
                    <div class="SearchItem">
                        <h5>From</h5>
                        <h4><?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?></h4>
                    </div>
                </div>
                <?php } ?>
                <?php if(!empty($_GET['to_date'])) { ?>
                <div class="col-xs-3">
                    <div class="SearchItem">
                        <h5>To</h5>
                        <h4><?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?></h4>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
        <!-- Custom Search Field For Print View Ends -->

        <!-- Search Quick Info Section Starts -->
        <?php if(isset($invoices_lists)) { ?>  
        <div class="row">  
            <div class="searchQuickInfoSectionWrapper">  
                <div class="col-md-6 col-lg-5">
                    <div class="searchQuickInfoSection">
                        <div class="sectionLeft">
                            <i class="fas fa-users"></i> Total Number Of Investigations
                        </div>
                        <div class="sectionRight">
                            <?php echo $sum_invoice->totalCount; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>  
        <!-- Search Quick Info Section Ends -->

        <?php if(isset($invoices_lists)) { ?>        
        <form action="" method="POST">                        
            <div class="">
                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th class="serialNo">SN</th>
                                <?php if(empty($agentID)){ ?>
                                <th class='text-left agentName'>Agent</th>
                                <?php } ?>       
                                <th class='text-left patientName'>Name</th>
                                <th>Age/Sex</th>   
                                <th class="statusInfo <?php if($agentcopy==1){ echo 'avoid'; } ?>">AC Status</th>
                                <th class="statusInfo <?php if($agentcopy==1){ echo 'avoid'; } ?>">Report Status</th>
                                <th class="dateInfo">Medical Date</th>
                                <?php foreach ($test_group_listes as $group) {
                                    $test_listes =  QB::table('inv_test')->where('itemID', '=',$itemID)->where('status', '=','1')->where('groupID', '=',$group->groupID)->get();                                            
                                    foreach ($test_listes as $test) { 
                                    if(!empty($test->tname)){ ?>
                                <th class="customColumn"><?php echo $test->tname; ?></th>
                                <?php  } } } ?>
                                <th class="avoid action-button-wrapper text-center actionWidth">Action</th>                                       
                                <th class="avoid">
                                    <label class="checkbox-wrapper">
                                        <input id="check_all" type="checkbox"/>
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $dck=0;
                                $sn = ($per_page*$page)-$per_page;
                                foreach($invoices_lists as $account) { 
                                    $sn=$sn+1; 
                                if($agentcopy==1 && !empty($account->refundAmount)){ continue; }                                
                            ?>
                            <tr class="gradeA" >                                        
                                <td class="serialNo"><?php echo $sn; ?></td>
                                <?php if(empty($agentID)){ ?>
                                <td class='text-left'><?php echo $account->agentName; ?></td>   
                                <?php } ?>     
                                <td class='text-left'><?php echo popup_window("../patient-details.php?uhid={$account->uhid}&searching=patient",$account->name);?></td>
                                <td><?php echo return_age($account->dob)."/ ".substr(return_sex($account->sex),0,1);?></td>
                                <td class="statusInfo <?php if($agentcopy==1){ echo 'avoid'; } ?>"><?php echo Account::return_payment_status($account->due,$account->paid); ?></td>
                                <td class="statusInfo <?php if($agentcopy==1){ echo 'avoid'; } ?>"><?php echo Lab::return_status($account->labStatus); ?></td>
                                <td class="dateInfo"><?php echo return_date($account->date); ?></td>
                                 <?php  foreach ($test_group_listes as $group) {
                                $test_listes =  QB::table('inv_test')->where('itemID', '=',$itemID)->where('status', '=','1')->where('groupID', '=',$group->groupID)->get();
                                
                                foreach ($test_listes as $test) { 
                                     $data_check= $db->result_one("SELECT * FROM inv_result WHERE invID='$account->investigatonID' AND testID='$test->id'");
                                     $commonResultList=array();
                                 if(!empty($test->tname)){
                                     if(!empty($test->default_value)){ $commonResultList= explode(",",$test->default_value); } ?>
                                        <td width="10%" class="customColumn">
                                            <div class="form-group">
                                                <input type="text" id="clearDataList" list="commonResultID_<?php echo $dck; ?>" name="data[<?php echo $account->id; ?>][result][]" value="<?php if(!empty($data_check)){ echo $data_check->result; }else{ if(!empty($commonResultList)){ echo $commonResultList[0]; } } ?>" class="form-control note-width" onfocus="this.value=''" onchange="this.blur();"/>
                                                    <datalist id="commonResultID_<?php echo $dck; ?>">
                                                        <?php if(!empty($commonResultList)){                             
                                                            foreach ($commonResultList as $key => $comn_result) {?>
                                                            <option value="<?php echo $comn_result; ?>"></option>
                                                        <?php  }} ?> 
                                                    </datalist>
                                            </div>
                                            <input type="hidden" name="data[<?php echo $account->id ; ?>][invResultId][]" class="avoid"  value="<?php if(!empty($data_check)){ echo $data_check->id; } ?>" />
                                            <input type="hidden" name="data[<?php echo $account->id ; ?>][testID][]" class="avoid"  value="<?php echo $test->id ; ?>" />
                                            <input type="hidden" name="data[<?php echo $account->id ; ?>][invID][]" class="avoid"  value="<?php echo $account->investigatonID ; ?>" />   
                                            <input type="hidden" name="data[<?php echo $account->id ; ?>][labStatus][]" class="avoid"  value="<?php echo $account->labStatus; ?>" />   
                                        </td>
                                <?php $dck++;  } } } ?>
                                <td class="avoid invoice-list text-center actionWidth">
                                    <div class="dropdown action-menu" id="result-list">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="optionmenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-angle-double-down" aria-hidden="true"></i></button>
                                        <ul class="dropdown-menu" aria-labelledby="optionmenu">

                                            <?php echo "<li>".popup_window("result-details.php?invID={$account->investigatonID}"," Details","dropdown-item")."</li>"; ?>
                                        <?php echo "<li>".alink("../index.php?page=patient-update&uhid={$account->uhid}&update=patient"," Profile","dropdown-item","_blank")."</li>"; ?>
                                        </ul>
                                    </div>
                                </td>
                                <td class="avoid">
                                    <label class="checkbox-wrapper">
                                        <input type="checkbox" class="case" name="checkbox[]" id="checkbox[]" <?php if(!empty($data_check->result)){echo "checked"; } ?> value="<?php echo $account->id; ?>"/>
                                        <span class="checkmark"></span>
                                    </label>                                
                                </td>
                            </tr>
                            <?php }  ?>
                        </tbody>
                    </table>
                
                    <br /><br />
                    <div class="row avoid" style="margin-top: 15px;">                        
                        <div class="col-lg-12 pull-right text-right"> 
                            <input type="submit" name="bulk_update" class="btn-submits pull-right" onclick = "if (! confirm('Do you want to Save?')) { return false; }" value="Save"/>
                        </div>
                    </div>
                 
               <!--  </div> -->
                <?php   echo '<div class="row avoid">'; 
                        echo '<div class="col-md-10 col-xs-12 col-md-offset-2">'; 
                        echo  $db->pagination($statement,$per_page,$page,curPageURL());   ?>
                        </div>
                    </div>
                      
            </form>
            <?php } ?> 
        </div>

    </div>
</div>

</div>
</div>
</div>
<!-- </div> -->

<style type="text/css">
    .table thead tr>th:first-child, .table tbody tr>td:first-child{
        min-width: 20px;
    }
    th.patientName {
        min-width: 12em;
    }
    th.dateInfo{
        min-width: 10em;
    }
    #clearDataList {
        min-width: 5em;
    }
    @media (min-width: 992px){
        #page-wrapper .panel .panel-body .table-responsive {
            overflow-x: auto;
        }    
    }    
    @media print{
        
    }
</style>


<?php 
$footer_link ='
<link rel="stylesheet" type="text/css" href="'.SITE_URL.'includes/datatables/css/jquery.dataTables2.css">
<script type="text/javascript" language="javascript" src="'.SITE_URL.'includes/datatables/js/jquery.dataTables.min.js"></script>
<script src="'.SITE_URL.'js/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript" src="'.SITE_URL.'includes/exportTable.js"></script> 
<link href="'.SITE_URL.'css/jquery-ui.min.css" rel="stylesheet" />
<script src="'.SITE_URL.'js/jquery-ui.min.js" type="text/javascript"></script>           

';  
?>