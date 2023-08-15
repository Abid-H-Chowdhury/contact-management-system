<?php

/**
 * @author MD. Mohiuddin
 * @Email mdmohiuddin.diu@gmail.com
 * @http://esteemsoftbd.com
 * @copyright 2023
 * 
 */

require_once("class/initialize.php");

global $date;
$today = date("d-m-Y");
$days30 = date('Y-m-d', strtotime('today - 30 days'));


?>
<!-- start  -->
<?php
$statement = " divisions t1 ";
$statement .= " ORDER BY t1.name ASC ";
$result_listes = QB::query("SELECT t1.*  FROM {$statement} ")->get();




// $result_listes = QB::query("SELECT d.id as did,d.name as dn, t.id, u.name as n,u.bname as ubn  , t.name as tn ,u.id as uid ,u.url as uurl , div.id as divid , div.name as divname FROM unions u JOIN thana t ON (u.psID = t.id) JOIN district d ON (d.id = t.district_id) JOIN  AND  district d JOIN divisions div ON (div.id = d.divId)")->get();



?>
<!-- End -->

<div id="page-wrapper">
  <!-- <? php // echo page_header("Welcome To " . is_setting_session("COMPANY_NAME") . " Dashboard"); 
				?> -->
  <?php echo page_header("Welcome To " . "Contact Management System" . " Dashboard"); ?>
  <div class="row lab-db">

    <!----- Todays Investigation starts ----->
    <div class="col-md-12 col-lg-12">
      <?php $flash->display(); ?>
      <div class="card-wrappers todays-investigations">
        <div class="row">
          <!-- --- Panel Primary Starts ----->
          <!-- <div class="col-lg-3 col-md-6 col-sm-12 panel-item">
						<div class="panel panel-primary" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Today's result that are ready to be delivered" data-original-title="" title="">
							<div class="panel-heading">
								<a href="index.php?page=result-lists&item_name=&labID=&type=3&from_date=&status=&search_item=Search">
									<div class="item-title">
										<h4>Dhaka</h4>
									</div>
									<div class="item-info">
										<div class="pull-left">
											<i class="fas fa-file-medical-alt fa-2x"></i>
										</div>
										<div class="pull-right">
											<p><?php //echo $todaysReadyResultTotal; 
													?></p>
											<p></p>
										</div>
									</div>
								</a>
							</div>
						</div>
					</div> -->
          <!----- Panel Primary Ends ----->

          <!----- Panel Yellow Starts ----->
          <!-- <div class="col-lg-3 col-md-6 col-sm-12 panel-item">
						<div class="panel panel-green" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Result that has been sent to server" data-original-title="" title="">
							<div class="panel-heading">
								<a href="index.php?page=result-lists&ac_headID=&item_name=&status=4&labID=&type=4&uhid=&from_date=&search_item=Search">
									<div class="item-title">
										<h4>Chattagram</h4>
									</div>

									<div class="item-info">
										<div class="pull-left">
											<i class="fas fa-laptop-medical fa-2x"></i>
										</div>
										<div class="pull-right">
											<p><?php //echo $result_delivered; 
													?></p>
										</div>
									</div>
								</a>
							</div>
						</div>
					</div> -->
          <!----- Panel Yellow Ends ----->

          <!----- Panel Purple Starts ----->
          <!-- <div class="col-lg-3 col-md-6 col-sm-12 panel-item">
						<div class="panel panel-yellow" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Today's result pending to sent to server" data-original-title="" title="">
							<div class="panel-heading">
								<a href="index.php?page=result-lists&item_name=&labID=&type=3&from_date=&status=3&search_item=Search">
									<div class="item-title">
										<h4>Rajshahi</h4>
									</div>

									<div class="item-info">
										<div class="pull-left">
											<i class="fas fa-pause fa-2x"></i>
										</div>
										<div class="pull-right">
											<p><?php //echo $todays_pending_result_ready; 
													?></p>
										</div>
									</div>
								</a>
							</div>
						</div>
					</div> -->
          <!----- Panel Purple Ends ----->

          <!----- Panel Green Starts ----->
          <!-- <div class="col-lg-3 col-md-6 col-sm-12 panel-item">
						<div class="panel panel-warning" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Total result pending to sent to server" data-original-title="" title="">
							<div class="panel-heading">
								<a href="index.php?page=result-lists&item_name=&labID=&type=3&from_date=&to_date=&status=3&search_item=Search">
									<div class="item-title">
										<h4>Khulna</h4>
									</div>
									<div class="item-info">
										<div class="pull-left">
											<i class="fas fa-pause fa-2x"></i>
										</div>
										<div class="pull-right">
											<p><?php //echo $pending_total; 
													?></p>
										</div>
									</div>
								</a>
							</div>
						</div>
					</div> -->
          <!----- Panel Green Ends ----->

          <!----- Panel Yellow Starts ----->
          <!-- <div class="col-lg-3 col-md-6 col-sm-12 panel-item">
						<div class="panel panel-warning" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Result that has been sent to server" data-original-title="" title="">
							<div class="panel-heading">
								<a href="index.php?page=result-lists&ac_headID=&item_name=&status=4&labID=&type=4&uhid=&from_date=&search_item=Search">
									<div class="item-title">
										<h4>Barisal</h4>
									</div>

									<div class="item-info">
										<div class="pull-left">
											<i class="fas fa-laptop-medical fa-2x"></i>
										</div>
										<div class="pull-right">
											<p><?php //echo $result_delivered; 
													?></p>
										</div>
									</div>
								</a>
							</div>
						</div>
					</div> -->
          <!----- Panel Yellow Ends ----->

          <!----- Panel Purple Starts ----->
          <!-- <div class="col-lg-3 col-md-6 col-sm-12 panel-item">
						<div class="panel panel-yellow" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Today's result pending to sent to server" data-original-title="" title="">
							<div class="panel-heading">
								<a href="index.php?page=result-lists&item_name=&labID=&type=3&from_date=&status=3&search_item=Search">
									<div class="item-title">
										<h4>Sylhet</h4>
									</div>

									<div class="item-info">
										<div class="pull-left">
											<i class="fas fa-pause fa-2x"></i>
										</div>
										<div class="pull-right">
											<p><?php //echo $todays_pending_result_ready; 
													?></p>
										</div>
									</div>
								</a>
							</div>
						</div>
					</div> -->
          <!----- Panel Purple Ends ----->
          <!----- Panel Primary Starts ----->
          <!-- <div class="col-lg-3 col-md-6 col-sm-12 panel-item">
						<div class="panel panel-primary" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Today's result that are ready to be delivered" data-original-title="" title="">
							<div class="panel-heading">
								<a href="index.php?page=result-lists&item_name=&labID=&type=3&from_date=&status=&search_item=Search">
									<div class="item-title">
										<h4>Rangpur</h4>
									</div>
									<div class="item-info">
										<div class="pull-left">
											<i class="fas fa-file-medical-alt fa-2x"></i>
										</div>
										<div class="pull-right">
											<p><?php //echo $todaysReadyResultTotal; 
													?></p>
											<p></p>
										</div>
									</div>
								</a>
							</div>
						</div>
					</div> -->
          <!----- Panel Primary Ends ----->

          <!----- Panel Green Starts ----->
          <!-- <div class="col-lg-3 col-md-6 col-sm-12 panel-item">
						<div class="panel panel-green" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Total result pending to sent to server" data-original-title="" title="">
							<div class="panel-heading">
								<a href="index.php?page=result-lists&item_name=&labID=&type=3&from_date=&to_date=&status=3&search_item=Search">
									<div class="item-title">
										<h4>Mymensingh</h4>
									</div>
									<div class="item-info">
										<div class="pull-left">
											<i class="fas fa-pause fa-2x"></i>
										</div>
										<div class="pull-right">
											<p><?php //echo $pending_total; 
													?></p>
										</div>
									</div>
								</a>
							</div>
						</div>
					</div> -->
          <!----- Panel Green Ends --- -->
          <!-- </div>

			</div>
		</div> -->
          <!----- Todays Investigation Ends ----->

          <!----- User Activity Starts----->
          <div class="col">
            <div class="card-wrapper userActivity">

              <div class="user-activity-table">
                <table class="user-table table" style="margin-bottom: 0px;">
                  <thead>
                    <tr>
                      <th width="15%">SN</th>
                      <th width="22%" class="text-left">Division Name</th>
                      <th width="21%"><a href="index.php?page=district-list">Total District</a></th>
                      <th width="21%"><a href="index.php?page=thana-list">Total Thana</a></th>
                      <th width="21%"><a href="index.php?page=union-list">Total Union</a></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $sn = 0;
										foreach ($result_listes as $test) {
											$sn = $sn + 1;
											$info = QB::query("SELECT COUNT((d.id)) as did FROM district d WHERE divId='$test->id'")->first();

											$info2 = QB::query("SELECT COUNT(t.id) AS tid 
											FROM district d JOIN thana t ON (d.id = t.district_id)
											 WHERE divId='$test->id'")->first();

											$info3 = QB::query("SELECT COUNT(u.id) AS uid FROM  unions u JOIN thana t ON (u.psID = t.id) JOIN district d ON (d.id = t.district_id)  WHERE divId='$test->id'")->first();

											$test3 = QB::query("SELECT t1.*  FROM district t1 WHERE divId='$test->id' ")->get();

										?>
                    <tr>
                      <td><?php echo $sn; ?></td>
                      <td class="text-left"><?php echo $test->name; ?></td>


                      <td><a href="index.php?page=district-list&division=<?php echo $test->id; ?>&search_item=Search"
                          target="_blank"><?php echo $info->did; ?></a></td>

                      <td><a
                          href="index.php?page=thana-list&division=<?php echo $test->id; ?>&district=&search_item=Search"
                          target="_blank"><?php echo $info2->tid; ?></a></td>

                      <!-- <td><? php // echo $info2->tid;
																	?></td> -->

                      <td><a
                          href="index.php?page=union-list&division=<?php echo $test->id; ?>&district=&thana=&search_item=Search"
                          target="_blank"><?php echo $info3->uid; ?></a></td>
                      <!-- <td><?php //echo $info3->uid; 
																	?></td> -->


                    </tr>
                    <?php }
										?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!----- User Activity Ends----->

        </div>
      </div>
      <br><br><br><br><br>

      <!-- /#page-wrapper -->
      <script>
      function ShowCID(str) {
        if (str == "patient-details") {
          $("#patientID").val(10);
        } else {
          $("#patientID").val('');
        }
      }
      </script>