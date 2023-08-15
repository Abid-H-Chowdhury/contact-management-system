 <?php
$sn = 0;
$sn1 = 0;
$userID = $_SESSION['user_id'];
global $date, $time;

require_once("pagination.php");
$currentPage = "index.php?page=result-lists";



if (isset($_GET["search_item"])) {

    $item_Name = isset($_GET["item_name"]) ? ($_GET["item_name"]) : NULL;
    $type = isset($_GET["type"]) ? ($_GET["type"]) : NULL;
    // $status = ($_GET["status"]);
    // $labID = ($_GET["labID"]);
    $from_date = isset($_GET["from_date"]) ? convert_date(($_GET["from_date"])) : NULL;
    $to_date = isset($_GET["to_date"]) ? convert_date(($_GET["to_date"])) : NULL;

    if (!empty($from_date) && !empty($to_date)) {
        $from_date = $from_date;
        $to_date = $to_date;
    } elseif (!empty($from_date) && empty($to_date)) {
        $from_date = $from_date;
        $to_date = date("Y-m-d");
    }


    $whereArr = array();
    if ($status != "") {
        $whereArr[] = "t1.status = '{$status}' ";
    } else {
        $whereArr[] = "t1.status IN(3,4) ";
    }
    if ($labID != "") $whereArr[] = " t1.labID = '{$labID}' ";

    if (!empty($item_Name)) {
        if ($item_Name != "") $whereArr[] = "t3.iname LIKE'%$item_Name%' ";
    }

    if ($from_date != "") {
        if ($type == 3) { // Result ready date
            $whereArr[] = "  (DATE(t2.rtime) BETWEEN '$from_date' AND '$to_date') ";
        } elseif ($type == 4) { // result delivery date
            $whereArr[] = "  (DATE(t2.ddate) BETWEEN '$from_date' AND '$to_date') ";
        }
    }

    $whereStr = implode(" AND ", $whereArr);

    $statement = " investigation t1 INNER JOIN inv_details t2 ON t1.id=t2.invID INNER JOIN ac_item t3 ON t1.itemID=t3.id WHERE {$whereStr} ";
    $statement .= " ORDER BY t1.id DESC ";
    $result_listes = $db->result_all("SELECT t1.id AS id,t1.itemID,t1.labID,t1.status,t2.rtime,t2.ddate,t3.iname  FROM {$statement} LIMIT {$startpoint} , {$per_page} ");


    $total_item = $db->result_one("SELECT COUNT(t1.id) AS total_item FROM  $statement")->total_item;
} else {
    $type = 3;
    $status = 3;
    $statement = " investigation t1 INNER JOIN inv_details t2 ON t1.id=t2.invID INNER JOIN ac_item t3 ON t1.itemID=t3.id WHERE t1.status = '$status' ";
    $statement .= " ORDER BY t1.id DESC ";
    $result_listes = $db->result_all("SELECT t1.id AS id,t1.itemID,t1.labID,t1.status,t2.rtime,t2.ddate,t3.iname  FROM {$statement} LIMIT {$startpoint} , {$per_page} ");


    $total_item = $db->result_one("SELECT COUNT(t1.id) AS total_item FROM  $statement")->total_item;
}


if (isset($_GET["labID"]) && !empty($_GET["labID"])) {
    $labID = $_GET["labID"];
    if (!empty($labID)) {
        $investigationInfo =   $db->result_one("SELECT t1.*, t2.rtime,t2.ddate, t3.iname,t3.server_ac_itemID FROM investigation t1 INNER JOIN inv_details t2 ON t1.id=t2.invID INNER JOIN ac_item t3 ON t1.itemID=t3.id WHERE t1.labID='{$labID}' ");
        $investigation_results = $db->result_all("SELECT t1.labID, t2.server_ac_itemID,t5.server_inv_testID,t4.result FROM `investigation` t1 LEFT JOIN ac_item t2 ON t1.itemID=t2.id LEFT JOIN inv_details t3 ON t1.id=t3.invID LEFT JOIN inv_result t4 ON t1.id=t4.invID LEFT JOIN inv_test t5 ON t4.testID=t5.id WHERE t1.labID='{$labID}'");

        if (!empty($investigation_results)) {

            $array_data = serialize($investigation_results);
            $server_url = trim(get_setting("PRIMARY_API_SERVER_REQUEST_URL_1"));
            //The URL with parameters / query string.

            $url = "{$server_url}?lisdata={$array_data}";
            $url = preg_replace("/ /", "%20", $url);
            //Once again, we use file_get_contents to GET the URL in question.
            $contents = file_get_contents($url);
            //If $contents is not a boolean FALSE value.


            if ($contents == '1') {

                $db->query("UPDATE investigation SET  status='4' WHERE id = '{$investigationInfo->id}' ");
                $db->query("UPDATE inv_details SET  ddate='$time' WHERE invID = '{$investigationInfo->id}' ");
                $flash->success(" LAB ID {$labID} Test Result Sent To Online Server Successfully!!!", "index.php?page=result-lists");
            } else {
                $flash->error($contents, "index.php?page=result-lists");
            }
        }
    }
}

if(isset($_POST["bulk_update"])) {
	
	//!empty($_POST['checkbox']) ? 
	if(!empty($_POST['checkbox']))
	{	$invIDs = $_POST['checkbox']; //from name="checkbox[]" 
        $status = 4;//4=deliver
		$countCheck = count($invIDs);
			for($i=0;$i<$countCheck;$i++)
			{
				$invID  = $invIDs[$i];
                $data = array('ddate'=>$time,'time'=>$time,'userID'=>$userID);
    
                if((QB::table('inv_details')->where('invID', $invID)->update($data))) {  
                    
                    $dataInv = array(
                    'status'=>$status
                    );
            
                    QB::table('investigation')->where('id', $invID)->update($dataInv);
    
                    $success = 1;
                }else{
                    $success = 0;
                } 
    
			}
        if($success==1){
            $flash->success("Result Ready Status Updated Successfully!!!",curPageURL());
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
<style>
    .table tbody>tr>td b.btn-xs {
        width: 8em;
        display: inline-block;
    }
</style>
<div id="page-wrapper">
    <div class="row print_div" id='print-div1'>
        <div class="col-lg-12">
            <center><b><?php $flash->display(); ?></b></center>
            <div class="panel panel-info">
                <div class="panel-heading avoid">
                    <i class="fa fa-flask"></i> List Of Investigation (Only Result Ready & Delivered)
                    <?php if (isset($result_listes)) { ?>
                        <div class="pull-right avoid">
                            <button class="btn-white btn-print avoid" id="print" type="button">
                                <i class="fa fa-print"></i> Print
                            </button>
                        </div>
                    <?php } ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <!-- Search Field Starts -->
                    <form action="" method="GET">
                  <div class="row avoid">
                            <input type="hidden" name="page" value="result-lists" />
                            <div class="searchFieldWrapper clearfix">

                                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['item_name'])) echo "avoid"; ?>">
                                    <div class="form-group">
                                        <input type="text" name="item_name" class="form-control" value="<?php echo isset($_GET['item_name']) ? $_GET['item_name']  : NULL ?>" placeholder="Investigation Name" />
                                        <span data-clear-input>&times;</span>
                                    </div> 
                                </div>

                                <div class="col-lg-1 col-md-1 col-sm-6 <?php if (empty($_GET['labID'])) echo "avoid"; ?>">
                                    <div class="form-group">
                                        <input type="number" name="labID" class="form-control" value="<?php echo isset($_GET['labID']) ? $_GET['labID']  : NULL ?>" placeholder="Lab ID" />
                                        <span data-clear-input>&times;</span>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-6 <?php if (empty($_GET['type'])) echo "avoid"; ?>">
                                    <div class="form-group">
                                        <select class="form-control" name="type">
                                            <option value="3" <?php if (isset($_GET['type']) && $_GET['type'] == 3) echo "SELECTED" ?>>Result Entry Date</option>
                                            <option value="4" <?php if (isset($_GET['type']) && $_GET['type'] == 4) echo "SELECTED" ?>>Sent To Server Date</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['from_date'])) echo "avoid"; ?>">
                                    <div class="form-group">
                                        <input title="From Date" type="text" class="form-control" placeholder="From Date" name="from_date" id="date1" <?php if (isset($_GET['from_date']) && !empty($_GET['from_date'])) {
                                                                                                                                                            echo 'value="' . $_GET['from_date'] . '"';
                                                                                                                                                        } elseif (isset($_GET['from_date']) && empty($_GET['from_date'])) {
                                                                                                                                                            echo 'value=""';
                                                                                                                                                        }  ?> />
                                        <span data-clear-input>&times;</span>
                                     </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['to_date'])) echo "avoid"; ?>">
                                    <div class="form-group">
                                        <input title="To Date" type="text" class="form-control" placeholder="To Date" name="to_date" id="date2" <?php if (isset($_GET['to_date']) && !empty($_GET['to_date'])) {
                                                                                                                                                    echo 'value="' . $_GET['to_date'] . '"';
                                                                                                                                                } elseif (isset($_GET['to_date']) && empty($_GET['to_date'])) {
                                                                                                                                                    echo 'value=""';
                                                                                                                                                } ?> />
                                        <span data-clear-input>&times;</span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['status'])) echo "avoid"; ?>">
                                    <div class="form-group">
                                        <select class="form-control" name="status">
                                            <option value="" <?php if (isset($_GET['status']) && $_GET['status'] == '') echo "SELECTED" ?>>All</option>
                                            <option value="3" <?php if (isset($_GET['status']) && $_GET['status'] == 3) echo "SELECTED" ?>>Result Ready</option>
                                            <option value="4" <?php if (isset($_GET['status']) && $_GET['status'] == 4) echo "SELECTED" ?>>Sent To Server</option>
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
                    <div class="printView">
                        <?php
                        // $methodValue = '';
                        // $subTitle = '';
                        // $lastsubTitle = '';
                        // echo common_print_header('Investigation Result List', $subTitle, $methodValue, $lastsubTitle);
                        ?>
                    </div>
                 <div class="row printView">
                        <div class="customSearchField"> 
                            <?php if (!empty($_GET['ac_headID'])) { ?>
                                <div class="col-xs-3">
                                    <div class="SearchItem">
                                        <h5>Head</h5> -->
                                        <!-- <h4><?php if (isset($_GET['ac_headID'])) {
                                                if ($_GET['ac_headID'] == '3') echo "Pathology";
                                            } ?></h4>
                                        <h4><?php if (isset($_GET['ac_headID'])) {
                                                if ($_GET['ac_headID'] == '4') echo "Imaging";
                                            } ?></h4> -->
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (!empty($_GET['ac_subheadID'])) { ?>
                                <div class="col-xs-3">
                                    <div class="SearchItem">
                                        <h5>Sub Head</h5>
                                        <h4><?php echo isset($_GET['ac_subheadID']) ? return_shead($_GET['ac_subheadID']) : ''; ?></h4>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (!empty($_GET['status'])) { ?>
                                <div class="col-xs-3">
                                    <div class="SearchItem">
                                        <h5>Status</h5>
                                        <h4><?php if (isset($_GET['status'])) {
                                                if ($_GET['status'] == '3') echo "Result Ready";
                                            } ?></h4>
                                        <h4><?php if (isset($_GET['status'])) {
                                                if ($_GET['status'] == '4') echo "Sent To Server";
                                            } ?></h4>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (!empty($_GET['from_date'])) { ?>
                                <div class="col-xs-3">
                                    <div class="SearchItem">
                                        <h5>From</h5>
                                        <h4><?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?></h4>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($_GET['to_date'])) { ?>
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
                    <div class="quickInfoSectionWrapper">
                        <!-- Search Quick Info Section Starts -->
                        <?php if (isset($result_listes)) { ?>
                            <div class="row">
                          <div class="searchQuickInfoSectionWrapper">
                                    <div class="col-md-6 col-lg-5">
                                        <div class="searchQuickInfoSection">
                                            <div class="sectionLeft">
                                                <i class="fas fa-users"></i> Total Number Of Investigations
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

                     <?php if (isset($result_listes)) { ?>
                            <form action="" method="POST">
                                <div class="">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="table">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th class="text-left">Test Name</th>
                                                    <th>Lab ID</th>
                                                    <th>Status</th>
                                                    <th>Ready Date</th>
                                                    <th>Sent To Server Date</th>
                                                    <th>Send</th>
                                                    <th class="avoid" width="5%">Action</th> 
                                                <th class="avoid" width="5%">
									<label class="checkbox-wrapper">
										<input id="check_all" type="checkbox"/>
										<span class="checkmark"></span>
									</label>
								</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $sn = ($per_page * $page) - $per_page;
                                                foreach ($result_listes as $test) {
                                                    $sn = $sn + 1; ?>
                                                    <tr class="gradeA">
                                                        <td><?php echo $sn; ?></td>
                                                        <td class="text-left"><?php echo $test->iname; ?></td>
                                                        <td><?php echo $test->labID; ?></td>
                                                        <td><?php echo Lab::return_status($test->status); ?></td>
                                                        <td><?php echo return_time($test->rtime); ?></td>
                                                        <td><?php echo !empty($test->ddate) ? return_time($test->ddate) : "";  ?></td>
                                                        <td><a href="send-result.php?labID=<?php echo $test->labID; ?>"> Send To Server</a></td>
                                                        <td class="avoid">
                                                            <div class="dropdown action-menu" id="result-list">
                                                                <button class="btn btn-default dropdown-toggle" type="button" id="optionmenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-angle-double-down" aria-hidden="true"></i></button>
                                                                <ul class="dropdown-menu" aria-labelledby="optionmenu">
                                                                    <li><a href="index.php?page=result-update&amp;invID=<?php echo $test->id; ?>" target="_blank"><i class="fa fa-pen-square" aria-hidden="true"></i> Result Update</a></li>
                                                                </ul>
                                                            </div>
                                                        </td> 
                                                     <td class="avoid">
									<label class="checkbox-wrapper">
										<input type="checkbox" class="case" name="checkbox[]" id="checkbox[]" value="<php echo $test->id;?>"/>
										<span class="checkmark"></span>
									</label>								
								</td> 
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                               < ?php if ($status == 3) { ?>
                                            <br /><br />
                                            <div class="row avoid" style="margin-top: 15px;">
                                                <div class="col-md-offset-3 col-md-5 col-lg-6 col-lg-offset-3">
                                                </div> 
                                          
                        <div class="col-md-4 col-lg-3"> 
                            <input type="submit" name="bulk_update" class="btn-submits pull-right" onclick = "if (! confirm('Do you want to update status as Sent to Server?')) { return false; }" value="Result Send"/>
                        </div> -->
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php echo '<div class="row avoid">';
                                    echo '<div class="col-md-10 col-xs-12 col-md-offset-2">';
                                   echo  $db->pagination($statement, $per_page, $page, curPageURL());   ?>
                                </div>
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
</div>

<style type="text/css">
    @media print {
        .table tbody>tr>td>span {
            width: 9em !important;
            color: #333 !important;
            padding: 3px;
        }
    }
</style>

<?php $footer_link = '
<link rel="stylesheet" type="text/css" href="' . SITE_URL . 'includes/datatables/css/jquery.dataTables2.css">
<script type="text/javascript" language="javascript" src="' . SITE_URL . 'includes/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="' . SITE_URL . 'includes/exportTable.js"></script>	
';
?> 