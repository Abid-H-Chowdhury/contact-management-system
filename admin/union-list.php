<?php
$sn = 0;
$userID = $_SESSION['user_id'];
global $date, $time;
require_once("pagination.php");
$currentPage = "index.php?page=union-list";
if (isset($_GET["search_item"])) {
    $division = isset($_GET["division"]) ? ($_GET["division"]) : NULL;
    $district = isset($_GET["district"]) ? ($_GET["district"]) : NULL;
    $thana = isset($_GET["thana"]) ? ($_GET["thana"]) : NULL;
    $whereArr = array();
    if ($division != "") {
        if (!empty($division)) {
            $whereArr[] = "d.divId = $division";
        }
        if (!empty($district)) {
            $whereArr[] = "d.id = $district";
        }
        if (!empty($thana)) {
            $whereArr[] = "t.id=$thana";
        }
        $whereStr = implode(" AND ", $whereArr);
        $statement = " unions u JOIN thana t ON (u.psID = t.id) JOIN district d ON (d.id = t.district_id) JOIN  divisions v ON (v.id = d.divId) WHERE u.id<> 0 AND  {$whereStr} ";
        $statement .= " ORDER BY t.name ASC ";
        $result_listes = QB::query("SELECT d.id as did,d.name as dn, t.id, u.name as n,u.bname as ubn  , t.name as tn ,u.id as uid ,u.url as uurl, d.divId as divid,v.id as vid ,v.name as vname FROM {$statement} LIMIT {$startpoint} , {$per_page} ")->get();
        $total_item = QB::query("SELECT COUNT(t.id) AS total_item FROM  $statement")->first();
        $total_item = $total_item->total_item;
        $total_item2 = QB::query("SELECT COUNT(DISTINCT(t.id)) AS total_item2 FROM  $statement")->first();
        $total_item2 = $total_item2->total_item2;
    } else {
        $statement = " unions u JOIN thana t ON (u.psID = t.id) JOIN district d ON (d.id = t.district_id) ";
        $statement .= " ORDER BY u.name ASC  ";
        $result_listes = QB::query("SELECT d.name as dn, t.id, u.name as n,u.bname as ubn  , t.name as tn ,u.id as uid ,u.url as uurl FROM {$statement} LIMIT {$startpoint} , {$per_page} ")->get();
        $total_item = QB::query("SELECT COUNT(u.id) AS total_item FROM  $statement")->first();
        $total_item = $total_item->total_item;
        $statement2 = " thana t  ";
        $statement2 .= " ORDER BY t.name ASC ";
        $total_item2 = QB::query("SELECT COUNT(t.id) AS total_item2 FROM  $statement2")->first();
        $total_item2 = $total_item2->total_item2;
    }
} else {
    $statement = " unions u JOIN thana t ON (u.psID = t.id) JOIN district d ON (d.id = t.district_id) ";
    $statement .= " ORDER BY u.name ASC  ";
    $result_listes = QB::query("SELECT d.name as dn, t.id, u.name as n,u.bname as ubn , t.name as tn ,u.id as uid ,u.url as uurl FROM {$statement} LIMIT {$startpoint} , {$per_page} ")->get();
    $total_item = QB::query("SELECT COUNT(u.id) AS total_item FROM  $statement")->first();
    $total_item = $total_item->total_item;
    $statement2 = " thana t  ";
    $statement2 .= " ORDER BY t.name ASC ";
    $total_item2 = QB::query("SELECT COUNT(t.id) AS total_item2 FROM  $statement2")->first();
    $total_item2 = $total_item2->total_item2;
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
          <i class="fas fa-th-list fa-fw"></i> List Of Unions
          <?php if (isset($result_listes)) { ?>
          <div class="pull-right avoid">
            <a href="index.php?page=add-union" name="add-union" id="add" type="submit" style="text-decoration: none;">
              <i class=""></i> <button class="btn-white btn-print avoid">Add Union</button>
            </a>
          </div>
          <?php } ?>
        </div>
        <div class="panel-body">
          <form method="">
            <div class="row avoid">
              <input type="hidden" name="page" value="union-list" />
              <div class="searchFieldWrapper clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['division'])) echo "avoid"; ?>">
                  <div class="form-group">
                    <label>Division Name:</label>
                    <select id="division" class="form-control" name="division">
                      <?php if (isset($_GET["division"]) ? ($_GET["division"]) : '') { ?>
                      <option value="<?php echo $_GET["division"]; ?>" selected>
                        <?php echo return_division($_GET["division"]); ?></option>
                      <?php } ?>
                      <option value="">All Division</option>
                    </select>
                    <span data-clear-input>&times;</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['district'])) echo "avoid"; ?>">
                  <div class="form-group">
                    <label>District Name:</label>
                    <select id="district" class="form-control" name="district">
                      <option value="">All District</option>
                      <?php if (isset($_GET["district"]) ? ($_GET["district"]) : '') { ?>
                      <option value="<?php echo $_GET["district"]; ?>" selected>
                        <?php echo return_zila($_GET["district"]); ?></option>
                      <?php } ?>
                    </select>
                    <span data-clear-input>&times;</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['thana'])) echo "avoid"; ?>">
                  <div class="form-group">
                    <label>Thana Name:</label>
                    <select id="thana" class="form-control" name="thana">
                      <option value="">All Thana</option>
                      <?php if (isset($_GET["thana"]) ? ($_GET["thana"]) : '') { ?>
                      <option value="<?php echo $_GET["thana"]; ?>" selected>
                        <?php echo return_thana($_GET["thana"]); ?></option>
                      <?php } ?>
                    </select>
                    <span data-clear-input>&times;</span>
                  </div>
                </div>
                <br>
                <div class="col-lg-1 col-md-2 col-sm-4">
                  <div class="form-group" style="margin-top: 5px;">
                    <input type="submit" name="search_item" class="btn-search avoid" value="Search" />
                  </div>
                </div>
              </div>
            </div>
          </form>
          <script type="text/javascript" src="js/jquery.js"></script>
          <script type="text/javascript">
          $(document).ready(function() {
            function loadData(type, category_id) {
              $.ajax({
                url: "load-cs.php",
                type: "POST",
                data: {
                  type: type,
                  id: category_id
                },
                success: function(data) {
                  if (type == "districtData") {
                    $("#district").html(data);
                  } else {
                    $("#division").append(data);
                  }
                }
              });
            }
            loadData();
            $("#division").on("change", function() {
              var division = $("#division").val();
              if (division != "") {
                loadData("districtData", division);
              } else {
                $("#district").html("");
              }
            })

            function loadData2(type2, category_id2) {
              $.ajax({
                url: "load-cs.php",
                type: "POST",
                data: {
                  type: type2,
                  id: category_id2
                },
                success: function(data2) {
                  if (type2 == "thanaData") {
                    $("#thana").html(data2);
                  } else {
                    // $("#district").append(data2);
                  }
                }
              });
            }
            loadData2();
            $("#district").on("change", function() {
              var district = $("#district").val();
              if (district != "") {
                loadData2("thanaData", district);
              } else {
                $("#thana").html("");
              }
            })
          });
          </script>
          <div class="quickInfoSectionWrapper">
            <?php if (isset($result_listes)) { ?>
            <div class="row">
              <div class="searchQuickInfoSectionWrapper">
                <div class="col-md-6 col-lg-5">
                  <div class="searchQuickInfoSection">
                    <div class="sectionLeft">
                      <i class=""></i> Total Number Of Union
                    </div>
                    <div class="sectionRight">
                      <?php echo $total_item; ?>
                    </div>
                  </div>
                </div>
                <div class="searchQuickInfoSectionWrapper">
                  <div class="col-md-6 col-lg-5">
                    <div class="searchQuickInfoSection">
                      <div class="sectionLeft">
                        <i class=""></i> Total Number Of thana
                      </div>
                      <div class="sectionRight">
                        <?php echo $total_item2; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
              <?php if (isset($result_listes)) { ?>
              <form action="" method="POST">
                <div class="">
                  <div class="table-responsive">
                    <table class="table table-striped" id="table">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th class="text-left">Union Name</th>
                          <th class="text-left">Bangla Name</th>
                          <th class="text-left">Thana Name</th>
                          <th class="text-left">District</th>
                          <th class="avoid" width="5%">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $sn = ($per_page * $page) - $per_page;
                                                    foreach ($result_listes as $test) {
                                                        if (!empty($test->n)) {
                                                            $sn = $sn + 1;
                                                    ?>
                        <tr class="gradeA">
                          <td><?php echo $sn; ?></td>
                          <td class="text-left"><?php echo $test->n ?></td>
                          <td class="text-left"><?php echo $test->ubn ?></td>
                          <td class="text-left"><?php echo $test->tn ?></td>
                          <td class="text-left"><?php echo $test->dn ?></td>
                          <td class="avoid">
                            <div class="dropdown action-menu" id="result-list">
                              <button class="btn btn-default dropdown-toggle" type="button" id="optionmenu"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i
                                  class="fa fa-angle-double-down" aria-hidden="true"></i></button>
                              <ul class="dropdown-menu" aria-labelledby="optionmenu">
                                <li><a href="" name="edit" data-toggle="modal" type="button"
                                    data-target="#editmodal<?php echo $test->uid ?>"><i class="fa fa-pen-square"
                                      aria-hidden="true">
                                    </i> Update</a></li>
                              </ul>
                            </div>
                          </td>
                          <?php    } ?>
                        </tr>
                        <?php
                                                        include('uniupdate.php');
                                                    } ?>
                      </tbody>
                    </table>
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