<?php
$sn = 0;
$userID = $_SESSION['user_id'];
global $date, $time;
require_once("pagination.php");
$currentPage = "index.php?page=district-list";
if (isset($_GET["search_item"])) {
    $division = isset($_GET["division"]) ? ($_GET["division"]) : NULL;
    $whereArr = array();
    if ($division != "") {
        $whereArr[] = "WHERE t1.divId = $division";
        $whereStr = implode(" AND ", $whereArr);
        $statement = " district t1 {$whereStr} ";
        $statement .= " ORDER BY t1.name ASC ";
        $result_listes = QB::query("SELECT t1.*  FROM {$statement} LIMIT {$startpoint} , {$per_page} ")->get();
        $total_item = QB::query("SELECT COUNT(t1.id) AS total_item FROM  $statement")->first();
        $total_item = $total_item->total_item;
    } else {
        $statement = " district t1 ";
        $statement .= " ORDER BY t1.name ASC ";
        $result_listes = QB::query("SELECT t1.*  FROM {$statement} LIMIT {$startpoint} , {$per_page} ")->get();
        $total_item = QB::query("SELECT COUNT(t1.id) AS total_item FROM  $statement")->first();
        $total_item = $total_item->total_item;
    }
} else {
    $statement = " district t1 ";
    $statement .= " ORDER BY t1.name ASC ";
    $result_listes = QB::query("SELECT t1.*  FROM {$statement} LIMIT {$startpoint} , {$per_page} ")->get();
    $total_item = QB::query("SELECT COUNT(t1.id) AS total_item FROM  $statement")->first();
    $total_item = $total_item->total_item;
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
          <i class="fas fa-th-list fa-fw"></i> List Of District
          <?php if (isset($result_listes)) { ?>
          <?php } ?>
        </div>
        <div class="panel-body">
          <form action="">
            <div class="row avoid">
              <input type="hidden" name="page" value="district-list" />
              <div class="searchFieldWrapper clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 <?php if (empty($_GET['division'])) echo "avoid"; ?>">
                  <div class="form-group">
                    <label>Division Name:</label>
                    <select id="division" class="form-control" name="division">
                      <option value="">All division</option>
                      <?php foreach (division_list() as $division) {
                                                echo '<option value="' . $division->id . '"';
                                                if (isset($_GET['division']) && $division->id == $_GET['division']) {
                                                    echo "SELECTED";
                                                }
                                                echo '>' . $division->name . '</option>';
                                            } ?>
                    </select>
                    <span data-clear-input>&times;</span>
                  </div>
                </div><br>
                <div class="col-lg-1 col-md-2 col-sm-4">
                  <div class="form-group" style="margin-top: 5px;">
                    <input type="submit" name="search_item" class="btn-search avoid" value="Search" />
                  </div>
                </div>
              </div>
            </div>
          </form>
          <div class="quickInfoSectionWrapper">
            <?php if (isset($result_listes)) { ?>
            <div class="row">
              <div class="searchQuickInfoSectionWrapper">
                <div class="col-md-6 col-lg-5">
                  <div class="searchQuickInfoSection">
                    <div class="sectionLeft">
                      <i class=""></i> Total Number Of District
                    </div>
                    <div class="sectionRight">
                      <?php echo $total_item; ?>
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
                        <th class="text-left">District Name</th>
                        <th class="text-left">Bangla Name</th>
                        <th>Thana</th>
                        <th>Union</th>
                        <th class="avoid" width="5%">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $sn = ($per_page * $page) - $per_page;
                                                foreach ($result_listes as $test) {
                                                    $sn = $sn + 1;
                                                    $info = QB::query("SELECT COUNT(u.id) as un, COUNT(DISTINCT(u.psID)) as thana FROM unions u WHERE distID='$test->id'")->first();
                                                ?>
                      <tr class="gradeA">
                        <td><?php echo $sn; ?></td>
                        <td class="text-left"><?php echo $test->name; ?></td>
                        <td class="text-left"><?php echo $test->bname; ?></td>
                        <td><a
                            href="index.php?page=thana-list&division=<?php echo $test->divId; ?>&district=<?php echo $test->id; ?>&thana=&search_item=Search"
                            target="_blank"><?php echo $info->thana; ?></a></td>
                        <td><a
                            href="index.php?page=Union-list&division=<?php echo $test->divId; ?>&district=<?php echo $test->id; ?>&thana=&search_item=Search"
                            target="_blank"><?php echo $info->un; ?></a></td>
                        <td class="avoid">
                          <div class="dropdown action-menu" id="result-list">
                            <button class="btn btn-default dropdown-toggle" type="button" id="optionmenu"
                              data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i
                                class="fa fa-angle-double-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu" aria-labelledby="optionmenu">
                              <li><a href="" name="edit" data-toggle="modal" type="button"
                                  data-target="#editmodal<?php echo $test->id ?>"><i class="fa fa-pen-square"
                                    aria-hidden="true">
                                  </i> Update</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <?php
                                                    include('disupdate.php');
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