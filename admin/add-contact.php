<?php
$userID = $_SESSION['user_id'];
global $date, $time;
require_once("pagination.php");
$currentPage = "index.php?page=add-contact";
?>
<?php
if (isset($_GET['contactid'])) {
    $contactid = isset($_GET['contactid']) ? ($_GET['contactid']) : NULL;
    $whereArr = array();
    $whereArr[] = "WHERE t1.id = '$contactid'";
    $whereStr = implode(" AND ", $whereArr);
    $statement = " contact t1 ";
    $result_one = QB::query("SELECT t1.*  FROM {$statement} {$whereStr}")->first();
}
if (isset($_POST["add_item"])) {
    $district = isset($_POST["district"]) ? ($_POST["district"]) : 0;
    $thana = isset($_POST["thana"]) ? ($_POST["thana"]) : 0;
    $union = isset($_POST["union"]) ? ($_POST["union"]) : 0;
    if ($district != "") {
        $district1 = 0;
        $thana2 = 0;
        $union3 = 0;
        if (!empty($district)) {
            $district1 = "$district";
        }
        if (!empty($district) && !empty($thana)) {
            $district1 = 0;
            $thana2 = "$thana";
            $union3 = 0;
        }
        if (!empty($district) && !empty($thana) && !empty($union)) {
            $district1 = 0;
            $thana2 = 0;
            $union3 = "$union";
        }
        $name = isset($_POST['name']) ? ($_POST['name']) : NULL;
        $mobile = isset($_POST['mobile']) ? ($_POST['mobile']) : NULL;
        $type = isset($_POST['type']) ? ($_POST['type']) : NULL;
        $contact_id = isset($_POST['contact_id']) ? ($_POST['contact_id']) : NULL;
        $status = isset($_POST['status']) ? ($_POST['status']) : NULL;
        $data = array(
            'name' => $name,
            'mobile' => $mobile,
            'type' =>  $type,
            'district' => $district1,
            'thana' =>  $thana2,
            'union' => $union3,
            'status' => $status
        );
        if (!empty($contact_id)) {
            QB::table('contact')->where('id', $contact_id)->update($data);
            $insertId = $contact_id;
        } else {
            $insertId = QB::table('contact')->insert($data);
        }
        if ($insertId) {
            echo "<script>
         window.location.href='http://localhost/bd/admin/index.php?page=add-contact&contactid=$insertId';
        </script>";
        }
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
          <i class="fa fa-address-book"></i> Add New Contact
          <?php if (isset($result_listes)) { ?>
          <?php } ?>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
          <!-- Search Field Starts -->
          <script type="text/javascript" src="js/jquery.js"></script>
          <script type="text/javascript">
          $(document).ready(function() {
            function loadData(type, category_id) {
              $.ajax({
                url: "load-cs2.php",
                type: "POST",
                data: {
                  type: type,
                  id: category_id
                },
                success: function(data) {
                  if (type == "thanaData") {
                    $("#thana").html(data);
                  } else {
                    $("#district").append(data);
                  }
                }
              });
            }
            loadData();
            $("#district").on("change", function() {
              var district = $("#district").val();
              if (district != "") {
                loadData("thanaData", district);
              } else {
                $("#thana").html("");
              }
            })

            function loadData2(type2, category_id2) {
              $.ajax({
                url: "load-cs2.php",
                type: "POST",
                data: {
                  type: type2,
                  id: category_id2
                },
                success: function(data2) {
                  if (type2 == "unionData") {
                    $("#union").html(data2);
                  } else {
                    // $("#thana").append(data2);
                  }
                }
              });
            }
            loadData2();
            $("#thana").on("change", function() {
              var thana = $("#thana").val();
              if (thana != "") {
                loadData2("unionData", thana);
              } else {
                $("#union").html("");
              }
            })
          });
          </script>
          <div class="quickInfoSectionWrapper">
          </div>
          <form action="" method="POST">
            <div class="row">
              <input type="hidden" name="page" value="add-contact" />
              <div class="col-md-4 col-lg-4">
                <input class="form-control" type="hidden" name="contact_id" value="<?php if (!empty($result_one)) {
                                                                                                        echo $result_one->id;
                                                                                                    } ?>">
                <div class="form-group">
                  <label>Contact Name:</label>
                  <input class="form-control" type="text" name="name" value="<?php if (!empty($result_one)) {
                                                                                                    echo $result_one->name;
                                                                                                } ?>"
                    placeholder="Name" required>
                </div>
              </div>
              <div class="col-md-4 col-lg-4">
                <div class="form-group">
                  <label>Mobile Number:</label>
                  <input class="form-control" type="number" name="mobile" value="<?php if (!empty($result_one)) {
                                                                                                        echo $result_one->mobile;
                                                                                                    } ?>"
                    placeholder="Mobile Number" required>
                </div>
              </div>
              <div class="col-md-4 col-lg-4">
                <div class="form-group">
                  <label>Responsibility:</label>
                  <select id="type" class="form-control" name="type" required>
                    <option value="">Select Responsibilities</option>
                    <?php foreach (type_list() as $type) {
                                            echo '<option value="' . $type->id . '"';
                                            if (!empty($result_one) && $type->id == $result_one->type) {
                                                echo "SELECTED";
                                            }
                                            echo '>' . $type->name . '</option>';
                                        }
                                        ?>
                  </select>
                  <span data-clear-input>&times;</span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 col-lg-4 <?php if (empty($_GET['district'])) echo "avoid"; ?>">
                <div class="form-group">
                  <label>District Name:</label>
                  <select id="district" class="form-control" name="district" required>
                    <?php if (isset($_GET["district"])) { ?>
                    <option value="<?php if (!empty($result_one) && ($_GET["district"]) == $result_one->district)?> "<?php { echo "SELECTED";} ?>>
                      <?php echo return_zila($_GET["district"]); ?></option>
                      
                    <?php  } ?>
                

                    <option value="">Select District</option>
                  </select>
                  <span data-clear-input>&times;</span>
                </div>
              </div>
              <div class="col-md-4 col-lg-4 <?php if (empty($_GET['thana'])) echo "avoid"; ?>">
                <div class="form-group">
                  <label>Thana Name:</label>
                  <select id="thana" class="form-control" name="thana">
                    <?php if (isset($_GET["thana"])) { ?>
                    <option value="<?php echo $_GET["thana"]; ?>" selected>
                      <?php echo return_thana($_GET["thana"]); ?></option>
                    <?php } ?>
                    <option value="">Select thana</option>
                  </select>
                  <span data-clear-input>&times;</span>
                </div>
              </div>
              <div class="col-md-4 col-lg-4  <?php if (empty($_GET['union'])) echo "avoid"; ?>">
                <div class="form-group">
                  <label>Union Name:</label>
                  <select id="union" class="form-control" name="union">
                    <?php if (isset($_GET["union"])) { ?>
                    <option value="<?php echo $_GET["union"]; ?>" selected>
                      <?php echo return_union($_GET["union"]); ?></option>
                    <?php } ?>
                    <option value="">Select Union</option>
                  </select>
                  <span data-clear-input>&times;</span>
                </div>
              </div>
              <div class="col-md-2 col-lg-2 ">
                <label>Status:</label>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status" value="1" id="flexRadioDefault1" checked>
                  <label class="form-check-label" for="flexRadioDefault1">
                    Active
                  </label>
                  <input class="form-check-input" type="radio" name="status" value="2" id="flexRadioDefault2">
                  <label class="form-check-label" for="flexRadioDefault2">
                    Inactive
                  </label>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-1 col-md-2 col-sm-4">
                <br>
                <div class="form-group">
                  <input type="submit" name="add_item" class="btn-search avoid" value="save">
                </div>
              </div>
            </div>


        </div>
      </div>
    </div>
    </form>
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