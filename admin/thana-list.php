<?php
$sn = 0;
$userID = $_SESSION['user_id'];
global $date, $time;
require_once("pagination.php");
$currentPage = "index.php?page=thana-list";
if (isset($_GET["search_item"])) {
    $district = isset($_GET["district"]) ? ($_GET["district"]) : NULL;
    $division = isset($_GET["division"]) ? ($_GET["division"]) : NULL;
    $whereArr = array();
    if ($division != "") {
        if (!empty($division)) {
            $whereArr[] = "d.divId = $division";
        }
        if (!empty($district)) {
            $whereArr[] = "d.id = $district";
        }
        $whereStr = implode(" AND ", $whereArr);
        $statement = "divisions v JOIN  district d ON (v.id = d.divId) JOIN thana t ON (d.id = t.district_id) WHERE t.id<> 0 AND {$whereStr} ";
        $statement .= " ORDER BY t.name ASC ";
        $result_listes = QB::query(" SELECT d.id as did, t.id as tid,d.name as n ,t.name as tn ,t.bname as tbname ,t.url as turl ,t.district_id as tdid,d.divId as divid,v.id as vid ,v.name as vname FROM {$statement} LIMIT {$startpoint} , {$per_page}")->get();
        $total_item = QB::query("SELECT COUNT(DISTINCT(t.id)) AS total_item FROM  $statement")->first();
        $total_item = $total_item->total_item;
        $statement2 = " unions u JOIN thana t ON (u.psID = t.id) JOIN district d ON (d.id = t.district_id) WHERE t.id<> 0 AND {$whereStr}";
        $statement2 .= " ORDER BY u.name ASC  ";
        $total_item2 = QB::query("SELECT COUNT(u.id) AS total_item2 FROM  $statement2")->first();
        $total_item2 = $total_item2->total_item2;
    } else {
        $statement = " divisions v JOIN  district d ON (v.id = d.divId) JOIN thana t ON (d.id = t.district_id)  ";
        $statement .= " ORDER BY t.name ASC ";
        $result_listes = QB::query(" SELECT d.id as did,t.id as tid,d.name as n ,t.name as tn, t.bname as tbname,t.url as turl ,t.district_id as tdid, d.divId as divid,v.id as vid FROM {$statement} LIMIT {$startpoint} , {$per_page}")->get();
        $total_item = QB::query("SELECT COUNT(t.id) AS total_item FROM  $statement")->first();
        $total_item = $total_item->total_item;
        $statement2 = " unions u JOIN thana t ON (u.psID = t.id) JOIN district d ON (d.id = t.district_id) ";
        $statement2 .= " ORDER BY u.name ASC  ";
        $total_item2 = QB::query("SELECT COUNT(u.id) AS total_item2 FROM  $statement2")->first();
        $total_item2 = $total_item2->total_item2;
    }
} else {
    $statement = " divisions v JOIN  district d ON (v.id = d.divId) JOIN thana t ON (d.id = t.district_id) ";
    $statement .= " ORDER BY t.name ASC ";
    $result_listes = QB::query(" SELECT d.id as did,t.id as tid,d.name as n ,t.name as tn,t.bname as tbname ,t.url as turl ,t.district_id as tdid ,d.divId as divid,v.id as vid FROM {$statement} LIMIT {$startpoint} , {$per_page}")->get();
    $total_item = QB::query("SELECT COUNT(t.id) AS total_item FROM  $statement")->first();
    $total_item = $total_item->total_item;
    $statement2 = " unions u JOIN thana t ON (u.psID = t.id) JOIN district d ON (d.id = t.district_id) ";
    $statement2 .= " ORDER BY t.name ASC  ";
    $total_item2 = QB::query("SELECT COUNT(u.id) AS total_item2 FROM  $statement2")->first();
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
          <i class="fas fa-th-list fa-fw"></i> List Of Thana
          <?php if (isset($result_listes)) { ?>
          <div class="pull-right avoid">
            <a href="index.php?page=add-thana" name="add-thana" id="add" type="submit" style="text-decoration: none;">
              <i class=""></i> <button class="btn-white btn-print avoid">Add Thana</button>
            </a>
          </div>
          <?php } ?>
        </div>
        <div class="panel-body">
          <form method="">
            <div class="row avoid">
              <input type="hidden" name="page" value="thana-list" />
              <div class="searchFieldWrapper clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['division'])) echo "avoid"; ?>">
                  <div class="form-group">
                    <label>Division Name:</label>
                    <select id="division" class="form-control" name="division">
                      <option value="">All Division</option>
                      <?php if (isset($_GET["division"]) ? ($_GET["division"]) : '') { ?>
                      <option value="<?php echo $_GET["division"]; ?>" selected>
                        <?php echo return_division($_GET["division"]); ?></option>
                      <?php } ?>

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
                      <option value=""></option>
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
          });
          </script>
          <div class="quickInfoSectionWrapper">
            <?php if (isset($result_listes)) { ?>
            <div class="row">
              <div class="searchQuickInfoSectionWrapper">
                <div class="col-md-6 col-lg-5">
                  <div class="searchQuickInfoSection">
                    <div class="sectionLeft">
                      <i class=""></i> Total Number Of Thana
                    </div>
                    <div class="sectionRight">
                      <?php echo $total_item; ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="searchQuickInfoSectionWrapper">
                <div class="col-md-6 col-lg-5">
                  <div class="searchQuickInfoSection">
                    <div class="sectionLeft">
                      <i class=""></i> Total Number Of Union
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
                        <th class="text-left" width="10%">SN</th>
                        <th class="text-left" width="25%">Thana Name</th>
                        <th class="text-left" width="15%">Bangla Name</th>
                        <th width="25%">Union</th>
                        <th class="text-left" width="20%">District</th>
                        <th class="avoid" width="5%">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $sn = ($per_page * $page) - $per_page;
                                                foreach ($result_listes as $test) {
                                                    $sn = $sn + 1;
                                                    $info = QB::query("SELECT COUNT((u.id)) as un FROM unions u WHERE psID='$test->tid'")->first();
                                                ?>
                      <tr class="gradeA">
                        <td class="text-left"><?php echo $sn; ?></td>
                        <td class="text-left"><?php echo $test->tn; ?></td>
                        <td class="text-left"><?php echo $test->tbname; ?></td>
                        <td><a
                            href="index.php?page=union-list&division=<?php echo $test->divid; ?>&district=<?php echo $test->did; ?>&thana=<?php echo $test->tid; ?>&search_item=Search"
                            target="_blank"><?php echo $info->un; ?></a></td>
                        <td class="text-left"><?php echo $test->n; ?></td>
                        <td class="avoid">
                          <div class="dropdown action-menu" id="result-list">
                            <button class="btn btn-default dropdown-toggle" type="button" id="optionmenu"
                              data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i
                                class="fa fa-angle-double-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu" aria-labelledby="optionmenu">
                              <li><a href="" name="edit" data-toggle="modal" type="button"
                                  data-target="#editmodal<?php echo $test->tid ?>"><i class="fa fa-pen-square"
                                    aria-hidden="true">
                                  </i> Update</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <?php
                                                    include('thanaupdate.php');
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
<script src="js/jquery-3.2.1.min.js"></script>
<?php $footer_link = '
<link rel="stylesheet" type="text/css" href="' . SITE_URL . 'includes/datatables/css/jquery.dataTables2.css">
<script type="text/javascript" language="javascript" src="' . SITE_URL . 'includes/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="' . SITE_URL . 'includes/exportTable.js"></script>	
';
?>