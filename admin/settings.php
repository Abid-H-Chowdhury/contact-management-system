<?php
require_once("class/initialize.php");
require_once(LIB_PATH.DS.'image-resize.php');
$time = date("Y-m-d");
$image_resize = new image_resizer();
  if(!is_esladmin()){
		$flash->error("Access Denied.","index.php?page=dashboard");
	}

$sn = 0;
$sn1=0;
$sn2=0;
$sn3 = 0;
$currentPage ="index.php?page=settings";

$all_investigations = $db->result_all("SELECT * FROM ac_item ORDER BY iname ASC");
$all_investigation_parameters = $db->result_all("SELECT t1.*, t2.iname FROM inv_test t1 INNER JOIN ac_item t2 ON t1.itemID= t2.id ORDER BY t1.id ASC");



if(isset($_POST["company_save"])) {
 
    update_setting(escape($_POST["COMPANY_NAME"]),"COMPANY_NAME");
    update_setting(escape($_POST["PRIMARY_API_SERVER_REQUEST_URL_1"]),"PRIMARY_API_SERVER_REQUEST_URL_1");
    update_setting(escape($_POST["PRIMARY_API_SERVER_REQUEST_URL_2"]),"PRIMARY_API_SERVER_REQUEST_URL_2");
    update_setting(escape($_POST["PRIMARY_API_SERVER_REQUEST_URL_3"]),"PRIMARY_API_SERVER_REQUEST_URL_3");
    update_setting(escape($_POST["PRIMARY_API_SERVER_REQUEST_URL_4"]),"PRIMARY_API_SERVER_REQUEST_URL_4");
    $flash->success("Saved Successfully.","index.php?page=settings");
}

?>
<style>
	.tab-pane {
		margin-top: 20px;
	}
	.nav-tabs>li>a{
		background-color: #eee;
	}
	.nav>li>a:focus, .nav>li>a:hover,.nav>li>a:active {
		text-decoration: none;
		background-color: #428bca;
		color: #fff;
	}
	.table thead tr th:nth-child(2), .table tbody tr td:nth-child(2) {
		text-align: left!important;
	}
</style>
<!----- Needed to show tab active after page reload ----->
<script src="<?php echo SITE_URL;?>js/jquery.min.js"></script>
<div id="page-wrapper">
<div class="row">
    <div class="col-lg-12">
    <center><b><?php $flash->display(); ?></b></center>
        <div class="panel panel-info">
            <div class="panel-heading">LIS Settings</div>
            <!-- /.panel-heading -->
			<div class="panel-body">
				<!-- Nav tabs -->
				<div class="bs-example">
					<ul class="nav nav-tabs" id="labSettings">
						<li class="active"><a data-toggle="tab" href="#lab-settings">LIS Settings</a></li>
						<li><a data-toggle="tab" href="#investigation_setup">Investigation</a></li>
						<li><a data-toggle="tab" href="#investigation_parameter">Investigation Parameters</a></li>
						
            			
					</ul>
					<div class="tab-content">
						<!----- Lab Settings Tab Starts ----->
						<div class="tab-pane fade in active" id="lab-settings">
							<form class="" action="" method="POST">
                    		<div class="form-horizontal">
                    			<div class="row">
                    				<div class="col-lg-12">
                    					<div class="bgGreySection clearfix">
                    						<div class="col-lg-3 col-md-6">
												<div class="form-group settingsItem">
													<h5 class="settingsItemTitle">Company Name</h5>
													<input type="text" name="COMPANY_NAME" class="form-control" value="<?php echo get_setting("COMPANY_NAME"); ?>" placeholder="Enter Company Name"/>
													<h6 class="settingsItemLabel">Comapny Name</h6>
												</div>
											</div>

											<div class="col-lg-3 col-md-6">
												<div class="form-group settingsItem">
													<h5 class="settingsItemTitle">Primary API Server Request URL 1</h5>
													<input type="text" name="PRIMARY_API_SERVER_REQUEST_URL_1"  class="form-control" value="<?php echo get_setting("PRIMARY_API_SERVER_REQUEST_URL_1"); ?>" placeholder="Primary API Server Request URL 1"/>
													<h6 class="settingsItemLabel">Enter Primary API Server Request URL 1</h6>
												</div>
											</div>
											<div class="col-lg-3 col-md-6">
												<div class="form-group settingsItem">
													<h5 class="settingsItemTitle">Primary API Server Request URL 2</h5>
													<input type="text" name="PRIMARY_API_SERVER_REQUEST_URL_2"  class="form-control" value="<?php echo get_setting("PRIMARY_API_SERVER_REQUEST_URL_2"); ?>" placeholder="Primary API Server Request URL 2"/>
													<h6 class="settingsItemLabel">Enter Primary API Server Request URL 2</h6>
												</div>
											</div>
											<div class="col-lg-3 col-md-6">
												<div class="form-group settingsItem">
													<h5 class="settingsItemTitle">Primary API Server Request URL 3</h5>
													<input type="text" name="PRIMARY_API_SERVER_REQUEST_URL_3"  class="form-control" value="<?php echo get_setting("PRIMARY_API_SERVER_REQUEST_URL_3"); ?>" placeholder="Primary API Server Request URL 3"/>
													<h6 class="settingsItemLabel">Enter Primary API Server Request URL 3</h6>
												</div>
											</div>
											<div class="col-lg-3 col-md-6">
												<div class="form-group settingsItem">
													<h5 class="settingsItemTitle">Primary API Server Request URL 4</h5>
													<input type="text" name="PRIMARY_API_SERVER_REQUEST_URL_4"  class="form-control" value="<?php echo get_setting("PRIMARY_API_SERVER_REQUEST_URL_4"); ?>" placeholder="Primary API Server Request URL 4"/>
													<h6 class="settingsItemLabel">Enter Primary API Server Request URL 4</h6>
												</div>
											</div>										

											
																						
										</div>
                    				</div>
	                    			<div class="col-md-2 pull-right">
										<center><input type="submit" name="company_save" class="btn-adds" onclick="if (! confirm('Are you sure you want to Save?')) { return false; }" value="Save Investigations" /></center><br />
									</div>
                    			</div>
                    			



					</div>
				</form>
				</div>

				<?php if(isset($_POST['server_invId_save'])){
					if(!empty($_POST["itemServiceID"])){

				  foreach ($_POST['itemServiceID'] as $key => $value) {					                
						     
						           $server_invID = !empty($value['server_invID']) ? trim($value['server_invID']) : 0;		           
						        
						          $db->query("UPDATE ac_item SET  server_ac_itemID='$server_invID' WHERE id = '$key' ");				        
						      
						     }
					}		

					 $flash->success("Saved Successfully.","index.php?page=settings");
				} ?>


				<!----- Sample Source Tab Starts ----->
						<div class="tab-pane fade" id="investigation_setup">
							<form action="" method="POST">
								<h4>Update Investigation Server ID</h4>
								<?php if(!empty($all_investigations)){  ?>


							<div class="table-responsive">
	                            <table class="table table-striped table-bordered" id="table">
	                                <thead>
	                                    <tr>
	                                        <th class="5%">SN</th>
	                                        <th width="80%" class="text-left">Investigation Name</th>
	                                        <th width="15%">Server Investigation ID</th>                                    
	                                    </tr>
	                                </thead>
	                                <tbody>                                  
	                                   	<?php 
	                                   		$sn=1;
	                                   		foreach ($all_investigations as $inv_history) {
	                                    ?>
	                                    <tr class="gradeA">
	                                        <td><?php echo $sn; ?></td>
	                                        <td><?php echo $inv_history->iname; ?></td>
	                                 		<td><input type="text" name="itemServiceID[<?php echo $inv_history->id; ?>][server_invID]" value="<?php echo $inv_history->server_ac_itemID; ?>" class="text-center"></td>
	                                    </tr>
	                                    <?php $sn++; } ?>
	                                </tbody>
	                            </table>
	                        </div>
								<div class="col-md-2 pull-right">
									<center><input type="submit" name="server_invId_save" class="btn-adds" onclick="if (! confirm('Are you sure you want to Save?')) { return false; }" value="Save Investigations" /></center><br />
								</div>
							<?php } ?>								
							</form>
						</div>
						<!----- Sample Source Tab Ends ----->


				<?php if(isset($_POST['server_testId_save'])){

					if(!empty($_POST["itemtestID"])){

				  		foreach ($_POST['itemtestID'] as $key => $testvalue) {					                
						     
						           $server_testID = !empty($testvalue['server_testID']) ? trim($testvalue['server_testID']) : 0;		           
						        
						          $db->query("UPDATE inv_test SET  server_inv_testID='$server_testID' WHERE id = '$key' ");				        
						      
						     }

						}		

					 $flash->success("Saved Successfully.","index.php?page=settings");
				} 
				?>


				<!----- Sample Source Tab Starts ----->
						<div class="tab-pane fade" id="investigation_parameter">
							<form action="" method="POST">
								<h4>Update Investigation Parameter Server ID</h4>
								<?php if(!empty($all_investigation_parameters)){  ?>


							<div class="table-responsive">
	                            <table class="table table-striped table-bordered" id="table">
	                                <thead>
	                                    <tr>
	                                        <th class="5%">SN</th>
	                                        <th width="50%" class="text-left">Investigation Name</th>
	                                        <th width="30%" class="text-left">Parameter Name</th>
	                                        <th width="15%">Server Parameter ID</th>
	                                    
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                  
	                                   <?php 
	                                   $sn=1;
	                                   	foreach ($all_investigation_parameters as $inv_test) {
	                                    ?>
	                                    <tr class="gradeA">
	                                        <td><?php echo $sn; ?></td>
	                                        <td class="text-left"><?php echo $inv_test->iname; ?></td>
	                                        <td class="text-left"><?php echo $inv_test->tname; ?></td>
	                                 		<td><input type="text" name="itemtestID[<?php echo $inv_test->id; ?>][server_testID]" value="<?php echo $inv_test->server_inv_testID; ?>" placeholder="Server Parameter/Test ID" class="text-center"></td>
	                                    </tr>
	                                    <?php $sn++; } ?>
	                                </tbody>
	                            </table>
	                        </div>
								<div class="col-md-2 pull-right">
									<center><input type="submit" name="server_testId_save" class="btn-adds" onclick="if (! confirm('Are you sure you want to Save?')) { return false; }" value="Save Parameters" /></center><br />
								</div>
							<?php } ?>
								
							</form>
						</div>
						<!----- Sample Source Tab Ends ----->
			<!-- /.panel-body -->
			</div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6 -->
</div>
</div>
</div>
<!-- /#page-wrapper -->

<style type="text/css">
/*Help Content Editor Minimum Height*/
	.note-editor.note-frame .note-editing-area {
	    min-height: 15em;
	}
@media (min-width: 1366px){
	a.btn-reset.deleteButton.pull-right {
	    margin-top: 25px;
	}
}
/*Signature*/
.docSignatureWrapper .fileinput-new.img-thumbnail, .fileinput-preview.fileinput-exists.img-thumbnail {
    width: 160px;
    height: 40px;
    line-height: unset!important;
    margin-bottom: 5px;
}
.docSignatureWrapper  .fileinput-new.img-thumbnail>img, .fileinput-preview.fileinput-exists.img-thumbnail>img {
    width: 150px;
    height: 30px;
}

</style>

<script type="text/javascript">
/*Show active tab after page reload*/
$(document).ready(function(){
	$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
		localStorage.setItem('activeTab', $(e.target).attr('href'));
	});
	var activeTab = localStorage.getItem('activeTab');
	if(activeTab){
		$('#labSettings a[href="' + activeTab + '"]').tab('show');
	}
});
</script>
<?php
$footer_link ='
<script src="'.SITE_URL.'includes/summernote/summernote.min.js"></script>
<link rel="stylesheet" type="text/css" href="'.SITE_URL.'includes/datatables/css/jquery.dataTables2.css">
<script type="text/javascript" language="javascript" src="'.SITE_URL.'includes/datatables/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="'.SITE_URL.'includes/jasny-bootstrap/css/jasny-bootstrap.min.css">
<script type="text/javascript" language="javascript" src="'.SITE_URL.'includes/jasny-bootstrap/js/jasny-bootstrap.min.js"></script>

<link href="'.SITE_URL.'css/select2.min.css" type="text/css"/>
<script src="'.SITE_URL.'js/select2.min.js" type="text/javascript"></script>
<script>
// In your Javascript (external .js resource or <script> tag)
    $(document).ready(function() {
        $(document).ready(function() {
		    $(".selectMultiple").select2({
		    	placeholder: "Select Item",
              	allowClear: true
	    	});
		});

    });
</script>

<script type="text/javascript">
$(document).ready(function() {
		$("#table2").dataTable(
        {"iDisplayLength": 25}
        );
} );
$(document).ready(function() {
		$("#table3").dataTable(
        {"iDisplayLength": 25}
        );
} );
$(document).ready(function() {
		$("#table4").dataTable(
        {"iDisplayLength": 25}
        );
} );
</script>';
?>

<script type="text/javascript">
// Summernote Text Editor
$(document).ready(function() {
  $('.summernote').summernote();
});
</script>