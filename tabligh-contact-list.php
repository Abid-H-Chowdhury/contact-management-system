<?php
require_once "class/initialize.php";
if (isset($_GET['zila'])) {
    $zila = isset($_GET['zila']) ? $_GET['zila'] : null;
    $statement = " divisions t1 JOIN  district d ON (t1.id = d.divID) WHERE d.name LIKE '%$zila%'";
    $statement .= " ORDER BY t1.name ASC ";
    $result_listes = QB::query("SELECT t1.*  FROM {$statement} ")->get();
} else {
    die("Page not found ");
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>বাংলাদেশের তাবলীগ জামাতের সাথীদের কন্টাক্ট লিস্ট</title>
    <link href="class/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <style>
        table {
            margin-top: 20px !important;
            animation-duration: 0.9s;
            animation-name: fadein;
            animation-timing-function: ease-in;
        }

        @keyframes fadein {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <img src="image/header.jpg">
            <div class="col text-center" style=" margin-top:30px; margin-bottom: 20px;">
                <h1 class="">বাংলাদেশের তাবলীগ জামাতের সাথীদের কন্টাক্ট লিস্ট</h1>
            </div>
        </div>
        <div class="row" style="margin-top:15px;">
            <div class="col">
                <div>
                    <?php $sn = 0;
                    foreach ($result_listes as $test) {
                        $division = $test->id;
                        $whereStr = "";
                        if (!empty($zila)) {
                            $whereStr = " AND t1.name LIKE '%$zila%'";
                        }
                        $district_lists = QB::query("SELECT t1.* FROM district t1 WHERE t1.divId = $division {$whereStr} ORDER BY t1.name ASC ")->get(); ?>
                        <h1 class="text-center" style="font-size:26px; color:#0d3d56;">
                            <?php echo "<br>" . '<b>বিভাগ: </b>' . $test->bn_name . " (" . $test->name . ")"; ?></h1>
                        <br> <br>
                        <?php
                        $url = htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
                        echo "<a type='button' class='btn btn-outline-danger btn-sm' href='$url'>Go back</a>";
                        ?>
                        <?php foreach ($district_lists as $dis) {
                            $district = $dis->id;
                            $thana_lists = QB::query("SELECT t1.* FROM thana t1 WHERE t1.district_id = $district ORDER BY t1.name ASC")->get(); ?>
                            <?php
                            $statement2 = " contact t1  WHERE t1.id <> 0 AND t1.district= $district  AND t1.status=1 ";
                            $statement2 .= " ORDER BY t1.name ASC  ";
                            $result_listes2 = QB::query("SELECT t1.* FROM {$statement2}  ")->get(); ?>
                            <h5 style="color:white;">
                                <?php echo "<br>" . "<span style='font-size: 20px; background-color: #008446; border-radius:9px; padding:4px;'>" . "<b>জেলা: </b>" . $dis->bname . " (" . $dis->name . ")" . "</span>"; ?>
                            </h5>
                            <?php if (!empty($result_listes2)) { ?>
                                <table class="table table-striped table-light table-bordered ">
                                    <thead>
                                        <tr>
                                            <th scope="col">SN</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Mobile</th>
                                            <th scope="col">Responsibility</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result_listes2 as $test2) {
                                            $sn = $sn + 1;
                                            $tyid = $test2->type;
                                            $res = QB::query("SELECT t1.*  FROM type t1 WHERE t1.id<> 0 AND t1.id = $tyid")->first();
                                        ?>
                                            <tr>
                                                <th scope="row"> <?php echo $sn ?></th>
                                                <td><?php echo $test2->name ?></td>
                                                <td><?php echo $test2->mobile ?></td>
                                                <td><?php echo $res->name ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                            <?php $sn2 = 0;
                            foreach ($thana_lists as $thana) {
                                $district = $dis->id;
                                $tha = $thana->id;
                                $union_lists = QB::query("SELECT t1.* FROM unions t1 WHERE t1.psID = $tha ORDER BY t1.name ASC")->get(); ?>
                                <?php
                                $statement3 = " contact t1  WHERE t1.id<> 0 AND t1.thana= $tha AND t1.status=1";
                                $statement3 .= " ORDER BY t1.name ASC  ";
                                $result_listes3 = QB::query("SELECT t1.* FROM {$statement3}")->get(); ?>
                                <h6 style="font-size: 18px; color:#008446;">
                                    <?php echo "<br>" . "<b>থানা: </b>" . $thana->bname . " (" . $thana->name . ")"; ?> </h6>
                                <?php if (!empty($result_listes3)) { ?>
                                    <table class="table table-striped table-light table-bordered ">
                                        <thead>
                                            <tr>
                                                <th scope="col">SN</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Mobile</th>
                                                <th scope="col">Responsibility</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($result_listes3 as $test3) {
                                                $tyid2 = $test3->type;
                                                $sn2 = $sn2 + 1;
                                                $res2 = QB::query("SELECT t1.*  FROM type t1 WHERE t1.id<> 0 AND t1.id = $tyid2")->first();
                                            ?>
                                                <tr>
                                                    <th scope="row"><?php echo $sn2 ?></th>
                                                    <td><?php echo $test3->name ?></td>
                                                    <td><?php echo $test3->mobile ?></td>
                                                    <td><?php echo $res2->name ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>
                                <?php $sn3 = 0;
                                foreach ($union_lists as $union) {
                                    $district = $dis->id;
                                    $tha = $thana->id;
                                    $uni = $union->id; ?>
                                    <?php
                                    $statement4 = " contact t1  WHERE t1.id<> 0 AND t1.union= $uni  AND t1.status=1";
                                    $statement4 .= " ORDER BY t1.name ASC  ";
                                    $result_listes4 = QB::query("SELECT t1.* FROM {$statement4}")->get(); ?>
                                    <?php if (!empty($result_listes4)) { ?>
                                        <p style="font-size: 15px; color: #086e3e;">
                                            <?php echo "<br>" . "<b>ইউনিয়ন: </b>" . $union->bname . " (" . $union->name . ")"; ?> </p>
                                        <table class="table table-striped table-light table-bordered">
                                            <thead>
                                                <tr>
                                                    <th scope="col">SN</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Mobile</th>
                                                    <th scope="col">Responsibility</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($result_listes4 as $test4) {
                                                    $tyid3 = $test4->type;
                                                    $sn3 = $sn3 + 1;
                                                    $res3 = QB::query("SELECT t1.*  FROM type t1 WHERE t1.id<> 0 AND t1.id = $tyid3")->first();
                                                ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $sn3 ?></th>
                                                        <td><?php echo $test4->name ?></td>
                                                        <td><?php echo $test4->mobile ?></td>
                                                        <td><?php echo $res3->name ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    <br>
    <footer>
        <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
            <p style="color:#5C6BC0 ;">Copyright © <?php echo date("Y"); ?> &amp; Developed
                By <a class="text-decoration-none" target="_blank" href="http://esteemsoftbd.com"><span style="color: #F38631;">Esteem Soft
                        Limited.</span></a></p>
        </div>
    </footer>
</body>
<script src="js/jquery-3.2.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
</script>

</html>