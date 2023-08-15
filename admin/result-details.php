<?php
/**
 * @author Sharif Ahmed
 * @Email winsharif@gmail.com
 * @http://esteemsoftbd.com
 * @copyright 2018
 * 
 */
include_once("headlink.php");
$userID = $_SESSION['user_id'];
global $date,$time;
$sn=0;


if(isset($_GET["invID"]) && !empty($_GET["invID"])){
    $invID = escape($_GET["invID"]);
    
    $test_details = QB::query("SELECT t1.id id,t1.uhid,t1.itemID,t1.invoiceID,t1.labID,t1.dID,t1.status,t2.*,t3.iname FROM investigation t1 
                               INNER JOIN inv_details t2 ON t1.id=t2.invID 
                               INNER JOIN ac_item t3 ON t1.itemID=t3.id 
                               INNER JOIN ac_invoice t4 ON t1.invoiceID=t4.id WHERE t1.id=? ", array($invID))->first();
    $uhid = $test_details->uhid;
    $InvItemID = $test_details->itemID;
    $patientInfo =  Patients::personal_info($uhid);   
    $invoiceInfo = Account::invoice_info($test_details->invoiceID);
    $setup_details = QB::table('inv_setup')->find($InvItemID,'itemID');                        
    $currentPage="result-details.php?invID={$invID}";
                            
}

if(isset($_POST["update_item"])) {

    $status = escape($_POST["status"]);
    
    if($test_details->status==3 && $status==4){ // update from result date to delivery date
        $data = array(
        'ddate'=>$time,
        'time'=>$time,
        'userID'=>$userID
        );
    }elseif($test_details->status==4 && $status==3){ // update from delivery date to ready date
        $data = array(
        'rtime'=>$time,
        'time'=>$time,
        'userID'=>$userID
        );
    }else{
        $data = array(
        'time'=>$time,
        'userID'=>$userID
        );
    }
    
    QB::table('inv_details')->where('invID', $invID)->update($data);
    
    $data = array('status'=>$status);
        
    if(QB::table('investigation')->where('id', $invID)->update($data)){
        $flash->success("Investigation Result Status Updated Successfully!!!",$currentPage);
        exit();
    }else{
        $flash->error("Fail. This Investigation is not updated!!!");
    }        
} 

if(isset($_POST["send_sms"])) { 
	
    $SMS_FOOTER = get_setting("SMS_FOOTER");
    $SMSTemplate = "REPORT_READY_SMS";
    
/*    $token = array(  
           'PATIENT_NAME' => $patientInfo->name,
           'CONTACT_NAME' => $patientInfo->fname,
           'UHID' => $uhid,
           'FOOTER'=>  $SMS_FOOTER 
         );*/

	$invoice_setInfo = $db->result_one("SELECT t2.* FROM ac_invoice t1 
		INNER JOIN ac_invoice_set t2 ON t2.invNo=t1.invNo 
		WHERE t1.id='$test_details->invoiceID'");
      
      $billAmount = $invoice_setInfo->amount;
      $discountAmount = $invoice_setInfo->discount;
      $paidAmount = $invoice_setInfo->paid;
      $dueAmount = $invoice_setInfo->due;

      $token = array(  
           'PATIENT_NAME' => $patientInfo->name,
           'CONTACT_NAME' => $patientInfo->fname,
           'UHID' => $uhid,
           'BILLAMOUNT' => $billAmount,
           'DISCOUNT' => $discountAmount,
           'NETBILL' => $billAmount-$discountAmount,
           'PAID' => $paidAmount,
           'DUE' => $dueAmount,
           'FOOTER'=>  $SMS_FOOTER 
         );


    if(!empty($patientInfo->cell2)){
         if(send_sms_auto($token,'3',$patientInfo->cell2,$SMSTemplate,null,$uhid)){
            $flash->success("SMS Sent Successfully.",curPageURL());
         }
     }	
}
?>
<style>
	#page-wrapper {
	    padding: 20px 30px;
	}
	h3.title {
	    text-align: center;
	    margin: 0 0 15px;
	    text-transform: uppercase;
	}
	h3.title>span {
	    border-bottom: 1px solid #555;
	}
	.panel-left, .panel-right{
		min-height: 20em;
	}
	.panel-left .item-list {
	    margin-bottom: 14px;
	}
	.panel-left .item-list:last-child {
	    margin: 0;
	}
	.money-information .table {
	    margin: 0;
	}
	.panel-right .table>tbody>tr>td {
	    padding: 13px 8px;
	}
	.panel-custom-table tbody tr>td {
	    text-align: left!important;
	}
	.panel-custom-table .table tbody>tr>td>span {
	    font-weight: 700;
	}
	.panel-custom-table tbody tr:first-child td {
	    border-top: none;
	}
	.panel-custom-table .table>tbody>tr>td>select {
	    height: 30px;
	}
	.panel-custom-table div.pull-right>span.btn-white {
	    font-weight: 700;
	}	
	.panel.panel-success .table tbody>tr>td{
		border-top: 1px solid #ddd!important;
	}
</style>


<div id="page-wrapper" style="margin: 0;">
    <div class="row">
    	<center><b><?php $flash->display(); ?></b></center>
        <div class="col-md-12">
        	<form action="" method="POST">
	            <div class="panel panel-info popUpWindow">
	                <div class="panel-heading">
	                    Test Result Details
	                </div>
	                <div class="panel-body">
	                    <!-- popupPanelLeft Starts -->
	                    <div class="col-md-6 col-sm-6 col-xs-12">
	                        <div class="popupPanelLeft">
	                            <div class="item-list">
	                                <h5 class="text-center patientName"><span><?php echo $patientInfo->name; ?></span></h5>
	                                <h5 class="text-center patientUHID"><span class="colorBlue">UHID: <?php echo $uhid; ?></span></h5>
	                            </div> 
	                            <div class="item-list">
				                    <label>Age/Sex</label>
				                    <h5><?php echo return_age($patientInfo->dob); ?> / <?php echo return_sex($patientInfo->sex); ?></h5>
				                </div>
	                            <div class="row">
	                                <div class="item-list col-xs-6" style="padding-right: 0;">
	                                    <label>Contact</label>
	                                    <h5><?php echo $patientInfo->fname; ?></h5>
	                                </div> 
	                                <div class="item-list col-xs-6" style="padding-right: 0;">
	                                    <label>Mobile</label>
	                                    <h5><?php if(!empty($patientInfo->cell2)) {echo $patientInfo->cell2; }else{echo "N/A";} ?></h5>
	                                </div> 
	                            </div> 
	                            <div class="item-list">
	                                <label>Address</label>
	                                <h5><?php echo return_address($uhid); ?></h5>
	                            </div> 
	                        </div>
	                    </div>
	                    <!-- popupPanelLeft Ends -->

	                    <!-- popupPanelRight Starts -->
	                    <div class="col-md-6 col-sm-6 col-xs-12">
	                        <div class="popupPanelRight">
	                        	<div class="money-information">
	                        		<h5 class="text-center"><span class="bgPurple">Invoice Info</span></h5>
			                        <table class="table">
			                            <tbody>
			                                <tr>
			                                    <td>
			                                        <label>Invoice</label>
			                                        <p><?php echo currency($invoiceInfo["amount"],2); ?></p>
			                                    </td>
			                                    <td>
			                                        <label>Discount</label>
			                                        <p><?php echo currency($invoiceInfo["discount"],2); ?></p>
			                                    </td>                                    
			                                </tr>
			                                <tr>
			                                	<td>
			                                        <label>Paid</label>
			                                        <p><?php echo currency($invoiceInfo["paid"],2); ?></p>
			                                    </td>
			                                    <td>
			                                        <label>Due</label>
			                                        <p><strong><span class="colorRed"><?php echo currency($invoiceInfo["due"],2);?></span></strong></p>
			                                    </td>
			                                </tr>
			                                <tr>    
			                                	<td colspan="2"><p class="text-center" style="padding-top: 10px;">INVOICE DATE: <strong><?php echo return_date($invoiceInfo["date"]); ?></strong> | INVOICE BY: <strong><?php echo $invoiceInfo["user"]; ?></strong></p></td>
			                                </tr>
			                            </tbody>
			                        </table>
			                    </div>
	                        </div>
	                    </div>
	                    <!-- popupPanelRight Ends -->

	                    <!-- popupPanelCommon Starts -->
	                    <div class="col-md-12 col-sm-12 col-xs-12">
	                        <div class="popupPanelCommon clearfix">
	                        	<h5 class="ipdTitle"><strong><span class="colorGreen"><?php echo $test_details->iname;?></span></strong> <span class="fgGreen pull-right">(LAB ID: <?php  echo $test_details->labID; ?>)</span></h5>
	                        	<table class="table noStripe">
									<tbody>
										<tr>
											<td class="text-left">Refered By: <span><?php echo $test_details->dID!=0 ? Customer::return_customer($test_details->dID,"name") : "Self"; ?></span></td>
											<td class="text-left">
												<p class="col-sm-3" style="padding: 0; margin: 0;line-height: 2.5em;">Status:</p>
												<select class="col-sm-9"  name="status"> 
													<option value="3" <?php if($test_details->status==3) { echo "SELECTED"; } ?>>Result Ready</option>
													<option value="4" <?php if($test_details->status==4) { echo "SELECTED"; } ?>>Delivered</option>
													<option value="5" <?php if($test_details->status==5) { echo "SELECTED"; } ?>>Cancel</option>
												</select>
											</td>
										</tr>
										<tr>
											<td class="text-left">Sample: <span><?php if(!empty($setup_details)){ echo $setup_details->sample; } ?></span></td>
											<td class="text-left">Source: <span><?php echo Lab::source_return($test_details->source); ?></span></td>
										</tr>
										<tr>
											<td class="text-left">Sample Collected By: <span><?php echo User::user_return($test_details->cby)["fname"]; ?></span> Time: <span><?php  echo return_time($test_details->ctime); ?></span></td>
											<td class="text-left">Result By: <span><?php echo User::user_return($test_details->rby)["fname"]; ?></span> Time: <span><?php  echo return_time($test_details->rtime); ?></span></td>
										</tr>
										<tr>
											<td class="text-left">Last Updated By: <span><?php echo User::user_return($test_details->userID)["fname"]; ?></span> Time: <span><?php  echo return_time($test_details->time);  ?></span></td>
											<td class="text-left">Report Delivered On: <span><?php  echo return_time($test_details->ddate); ?></span></td>
										</tr>
									</tbody>											
								</table>
								
								<div class="pull-right">
									<input type="submit" name="update_item" class="btn-save" onclick="if (! confirm('Are you sure you want to Update?')) 
							{ return false; }" value="Save" />
									<?php if($test_details->status==3){ ?>
									    <form action="" method="POST">
									        <input type="submit" class="btn-submits" onclick="if (! confirm('Are you sure you want to Send SMS?')) 
												{ return false; }" name="send_sms" value="Send SMS"  />
									    </form>
								   	<?php } ?> 
							   </div>

	                        </div>
	                    </div>
	                </div>
	            </div>
	        </form>
	    </div>
	</div>
</div>

<style type="text/css">
	h5.ipdTitle {
	    border-bottom: 2px solid #efefef;
	    padding-bottom: 12px;
	}
	.bgGrey{
		border-radius: 15px;    
    	font-size: 16px;
	}
	.colorBlue {
	    color: #3886E0;
	}
	.bgGreen {
	    border-radius: 15px;
	}
	.noStripe tbody>tr>td input:read-only, .noStripe tbody>tr>td select:read-only{
		border: 1px solid #ccc!important;
		border-radius: 10px;
		height: 30px;
	}
	.popupPanelCommon .table tbody>tr>td{
		font-weight: 600;
	}
	.popupPanelCommon .table tbody>tr>td>span{
		font-weight: normal;
	}
	.panel.panel-info.popUpWindow .popupPanelLeft, .panel.panel-info.popUpWindow .popupPanelRight {
	    min-height: 16em;
	}
</style>

<!-- /#page-wrapper -->
<?php include("../footer.php"); ?>