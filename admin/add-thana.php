<?php
$userID = $_SESSION['user_id'];
global $date, $time;
require_once("pagination.php");
$currentPage = "index.php?page=add-thana";
?>
<?php
if (isset($_POST["add_item"])) {
    $district = isset($_POST["district"]) ? ($_POST["district"]) : NULL;
    $tname = isset($_POST['tname']) ? ($_POST['tname']) : NULL;
    $tbname = isset($_POST['tbname']) ? ($_POST['tbname']) : NULL;
    $turl = isset($_POST['turl']) ? ($_POST['turl']) : NULL;
    $data = array(
        'district_id' => $district,
        'name' =>  $tname,
        'bname' => $tbname,
        'url' => $turl
    );
    $insertId = QB::table('thana')->insert($data);
    if ($insertId) {
        echo "<script>
        window.location.href='http://localhost/bd/admin/index.php?page=thana-list';
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
          <i class=""></i> Add New Thana

        </div>
        <div class="panel-body">
          <div class="quickInfoSectionWrapper">

            <form action="" method="POST">
              <div class="">
                <div class="row">
                  <input type="hidden" name="page" value="add-thana" />
                  <div class="col-lg-3 col-md-3 col-sm-6 ">
                    <div class="form-group">
                      <label>District Name:</label>
                      <select class="form-control" name="district" required>
                        <option value="">Select District</option>
                        <?php foreach (district_list() as $district) {
                                                    echo '<option value="' . $district->id . '"';
                                                    if (isset($_GET['district']) && $district->id == $_GET['district']) {
                                                        echo "SELECTED";
                                                    }
                                                    echo '>' . $district->name . '</option>';
                                                } ?>
                      </select>
                      <span data-clear-input>&times;</span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="col-lg-3 col-md-3 col-sm-6">
                      <div class="form-group">
                        <label>Thana Name:</label>
                        <input class="form-control" type="text" name="tname" value="" placeholder="Thana Name" required>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="col-lg-3 col-md-3 col-sm-6 ">
                      <div class="form-group">
                        <label>Bangla Name:</label>
                        <input class="form-control" type="text" name="tbname" value="" placeholder="Bangla Name">
                      </div>
                    </div>
                  </div>

                  <div class="col">
                    <div class="col-lg-3 col-md-3 col-sm-6 ">
                      <div class="form-group">
                        <label> Add URL:</label><br>
                        <input class="form-control" type="url" name="turl" value="" placeholder="URL">
                      </div>
                    </div>
                  </div>
                </div>


              </div>
              <div class="row">
                <div class="col-lg-1 col-md-2 col-sm-4 pull-left">

                  <div class="form-group">
                    <input type="submit" name="add_item" class="btn-search avoid" value="Add Thana" />
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