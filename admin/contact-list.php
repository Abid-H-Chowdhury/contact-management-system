<?php
$sn = 0;
$userID = $_SESSION['user_id'];
global $date, $time;
require_once("pagination.php");
$currentPage = "index.php?page=contact-list";
if (isset($_GET["search_item"])) {
    $name = isset($_GET["name"]) ? ($_GET["name"]) : NULL;
    $type = isset($_GET["type"]) ? ($_GET["type"]) : NULL;
    $district = isset($_GET["district"]) ? ($_GET["district"]) : NULL;
    $thana = isset($_GET["thana"]) ? ($_GET["thana"]) : NULL;
    $union = isset($_GET["union"]) ? ($_GET["union"]) : NULL;
    $status = isset($_GET["status"]) ? ($_GET["status"]) : NULL;
    $whereArr = array();
    if ($district != "" || $name != "" || $type != "" || $status != "") {
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
        if (!empty($district)) {
            $whereArr[] = "c.district = $district1";
        }
        if (!empty($thana)) {
            $whereArr[] = "c.thana=$thana2";
        }
        if (!empty($union)) {
            $whereArr[] = "c.union = $union3";
        }
        if (!empty($name)) {
            $whereArr[] = "c.name LIKE  '%$name%'";
        }
        if (!empty($type)) {
            $whereArr[] = "c.type = $type";
        }
        if (!empty($status)) {
            $whereArr[] = "c.status = $status";
        }
        $whereStr = implode(" AND ", $whereArr);
        $whereStr = implode(" AND ", $whereArr);
        $statement = " contact c  WHERE c.id<> 0 AND  {$whereStr} ";
        $statement .= " ORDER BY c.name ASC  ";
        $result_listes = QB::query("SELECT c.status as cstatus ,c.name as cn,c.id as cid ,c.mobile as cmobile,c.type as ctype ,c.district as cdis,c.thana as cthana ,c.union as cunion FROM {$statement} LIMIT {$startpoint} , {$per_page} ")->get();
        $total_item = QB::query("SELECT COUNT(c.id) AS total_item FROM  $statement")->first();
        $total_item = $total_item->total_item;
    } else {
        $statement = " contact c ";
        $statement .= " ORDER BY c.name ASC  ";
        $result_listes = QB::query("SELECT c.status as cstatus ,c.name as cn,c.id as cid ,c.mobile as cmobile,c.type as ctype ,c.district as cdis,c.thana as cthana ,c.union as cunion FROM {$statement} LIMIT {$startpoint} , {$per_page} ")->get();
        $total_item = QB::query("SELECT COUNT(c.id) AS total_item FROM  $statement")->first();
        $total_item = $total_item->total_item;
        $statement2 = " thana t  ";
        $statement2 .= " ORDER BY t.name ASC ";
    }
} else {
    $statement = " contact c ";
    $statement .= " ORDER BY c.name ASC  ";
    $result_listes = QB::query("SELECT c.status as cstatus ,c.name as cn,c.id as cid ,c.mobile as cmobile,c.type as ctype ,c.district as cdis,c.thana as cthana ,c.union as cunion FROM {$statement} LIMIT {$startpoint} , {$per_page} ")->get();
    $total_item = QB::query("SELECT COUNT(c.id) AS total_item FROM  $statement")->first();
    $total_item = $total_item->total_item;
    $statement2 = " thana t  ";
    $statement2 .= " ORDER BY t.name ASC ";
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
          <i class="fas fa-th-list fa-fw"></i> List Of Contacts
        </div>

        <div class="panel-body">

          <form method="">
            <div class="row avoid">
              <input type="hidden" name="page" value="contact-list" />
              <div class="searchFieldWrapper clearfix">
                <div class="col-lg-4 col-md-4 col-sm-6">
                  <div class="form-group">
                    <label>Contact Name:</label>
                    <input class="form-control" type="text" name="name" value="<?php echo isset($_GET['name']) ? $_GET['name'] : ''; ?>" placeholder="Contact Name">
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                  <div class="form-group">
                    <label>Responsibility:</label>
                    <select id="type" class="form-control" name="type">
                      <option value="">All Responsibilities</option>
                      <?php foreach (type_list() as $type) {
                                                echo '<option value="' . $type->id . '"';
                                                if (isset($_GET['type']) && $type->id == $_GET['type']) {
                                                    echo "SELECTED";
                                                }
                                                echo '>' . $type->name . '</option>';
                                            }
                                            ?>
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
                <div class="col-lg-3 col-md-3 col-sm-6  <?php if (empty($_GET['union'])) echo "avoid"; ?>">
                  <div class="form-group">
                    <label>Union Name:</label>
                    <select id="union" class="form-control" name="union">
                      <?php if (isset($_GET["union"]) ? ($_GET["union"]) : '') { ?>
                      <option value="<?php echo $_GET["union"]; ?>" selected>
                        <?php echo return_union($_GET["union"]); ?></option>
                      <?php } ?>
                      <option value="">All Union</option>
                    </select>
                    <span data-clear-input>&times;</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6  <?php if (empty($_GET['status'])) echo "avoid"; ?>">
                  <div class="form-group">
                    <label>Status:</label>
                    <select id="status" class="form-control" name="status">
                      <option value="">All status</option>
                      <?php if (isset($_GET["status"]) ? ($_GET["status"]) : '') { ?>
                      <option value="<?php echo $_GET["status"]; ?>" selected>
                        <?php if ($_GET["status"] == 1) echo "Active";
                                                    if ($_GET["status"] == 2) {
                                                        echo "Inactive";
                                                    } ?>
                      </option>
                      <?php } ?>
                      <option value="1">Active</option>
                      <option value="2">Inactive</option>
                    </select>
                    <span data-clear-input>&times;</span>
                  </div>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-4">
                  <br>
                  <div class="form-group" style="margin-top: 5px;">
                    <input type="submit" name="search_item" class="btn-search avoid" value="Search" />
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
            </div>
          </form>
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
            <?php if (isset($result_listes)) { ?>
            <div class="row">
              <div class="searchQuickInfoSectionWrapper">
                <div class="col-md-6 col-lg-5">
                  <div class="searchQuickInfoSection">
                    <div class="sectionLeft">
                      <i class="fas fa-users"></i> Total Number Of Contacts
                    </div>
                    <div class="sectionRight">
                      <?php echo $total_item; ?>
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
                            <th class="text-left" width="15%" class="text-left">Name</th>
                            <th class="text-left" width="15%">Mobile</th>
                            <th class="text-left" width="14%">Responsibility</th>
                            <th width="14%">District</th>
                            <th width="14%">Thana Name</th>
                            <th width="14%">Union Name</th>
                            <th class="text-left" width="4%">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $sn = ($per_page * $page) - $per_page;
                                                        foreach ($result_listes as $test) {
                                                            $sn = $sn + 1;
                                                            $tyid = $test->ctype;
                                                            $dis = $test->cdis;
                                                            $tha = $test->cthana;
                                                            $uni = $test->cunion;
                                                            $csta = $test->cstatus;
                                                            $res = QB::query("SELECT t1.*  FROM type t1 WHERE id = $tyid")->first();
                                                            $res1 = QB::query("SELECT t1.*  FROM district t1 WHERE id =  $dis")->first();
                                                            $res2 = QB::query("SELECT t1.*  FROM thana t1 WHERE id = $tha")->first();
                                                            $res3 = QB::query("SELECT t1.*  FROM unions t1 WHERE id = $uni")->first();
                                                        ?>
                          <tr class="gradeA">
                            <td class="text-left"><?php echo $sn; ?></td>
                            <td class="text-left"><?php echo $test->cn; ?></td>
                            <td class="text-left"><?php echo $test->cmobile; ?></td>
                            <td class="text-left"><?php if (!empty($res)) {
                                                                                            echo  $res->name;
                                                                                        }  ?></td>
                            <td><?php if (!empty($res1)) {
                                                                        echo  $res1->name;
                                                                    } else {
                                                                        echo "-";
                                                                    } ?></td>
                            <td><?php if (!empty($res2)) {
                                                                        echo $res2->name;
                                                                    } else {
                                                                        echo "-";
                                                                    } ?></td>
                            <td><?php if (!empty($res3)) {
                                                                        echo $res3->name;
                                                                    } else {
                                                                        echo "-";
                                                                    } ?></td>
                            <td class="text-left"><?php if (!empty($csta == 1)) {
                                                                                            echo "Active";
                                                                                        } else {
                                                                                            echo "inactive";
                                                                                        } ?>
                            </td>
                            <?php
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