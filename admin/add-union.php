<?php
$userID = $_SESSION['user_id'];
global $date, $time;
require_once("pagination.php");
$currentPage = "index.php?page=add-union";
?>
<?php
if (isset($_POST["add_item"])) {
    $district = isset($_POST["district"]) ? ($_POST["district"]) : NULL;
    $thana = isset($_POST["thana"]) ? ($_POST["thana"]) : NULL;
    $uname = isset($_POST['uname']) ? ($_POST['uname']) : NULL;
    $ubname = isset($_POST['ubname']) ? ($_POST['ubname']) : NULL;
    $uurl = isset($_POST['uurl']) ? ($_POST['uurl']) : NULL;
    $data = array(
        'distID' => $district,
        'psID' => $thana,
        'name' =>  $uname,
        'bname' => $ubname,
        'url' => $uurl
    );
    $insertId = QB::table('unions')->insert($data);
    if ($insertId) {
        echo "<script>
        window.location.href='http://localhost/bd/admin/index.php?page=union-list';
        </script>";
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
          <i class=""></i> Add New Unions

        </div>
        <div class="panel-body">

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
          });
          </script>
          <div class="quickInfoSectionWrapper">
          </div>

          <form action="" method="POST">
            <div class="">
              <div class="row">
                <input type="hidden" name="page" value="add-union" />
                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['district'])) echo "avoid"; ?>">
                  <div class="form-group">
                    <label>District Name:</label>
                    <select id="district" class="form-control" name="district" required>
                      <?php if (isset($_GET["district"])) { ?>
                      <option value="<?php echo $_GET["district"]; ?>" selected>
                        <?php echo return_zila($_GET["district"]); ?></option>
                      <?php } ?>
                      <option value="">Select District</option>
                    </select>
                    <span data-clear-input>&times;</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['thana'])) echo "avoid"; ?>">
                  <div class="form-group">
                    <label>Thana Name:</label>
                    <select id="thana" class="form-control" name="thana" required>
                      <?php if (isset($_GET["thana"])) { ?>
                      <option value="<?php echo $_GET["thana"]; ?>" selected>
                        <?php echo return_thana($_GET["thana"]); ?></option>
                      <?php } ?>
                      <option value="">Select thana</option>
                    </select>
                    <span data-clear-input>&times;</span>
                  </div>
                </div>
                <div class="">
                  <div class="col-lg-3 col-md-3 col-sm-6">
                    <div class="form-group">
                      <label>Union Name:</label>
                      <input class="form-control" type="text" name="uname" value="" placeholder="Union Name" required>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 ">
                  <div class="form-group">
                    <label>Bangla Name:</label>
                    <input class="form-control" type="text" name="ubname" value="" placeholder="Bangla Name">
                  </div>
                </div>


                <div class="col">
                  <div class="col-lg-3 col-md-3 col-sm-6 ">
                    <div class="form-group">
                      <label> Add URL:</label><br>
                      <input class="form-control" type="url" name="uurl" value="" placeholder="URL">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-1 col-md-2 col-sm-4">

                  <div class="form-group">
                    <input type="submit" name="add_item" class="btn-search avoid" value="Add Union" />
                  </div>
                </div>
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