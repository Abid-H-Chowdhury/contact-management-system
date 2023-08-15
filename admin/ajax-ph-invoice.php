<?php
require_once("class/initialize.php");
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_error()) echo "Failed To Connect To MySQL: " . mysqli_connect_error();
if (!empty($_POST['type'])) {
    $type = $_POST['type'];
    $name = $_POST['name_startsWith'];
    $query = "SELECT * FROM ph_med_items WHERE name LIKE '%" . strtoupper($name) . "%' ORDER BY name ASC LIMIT 15";
    $result = mysqli_query($con, $query);
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $total_Stock = Pharma::Current_Stock($row['id']);
        if (!empty($total_Stock)) {
            $name = $row['id'] . '|' . $row['name'] . " " . Pharma::return_category($row['cate'], "sname") . '|' . $row['sell'] . '|' . $total_Stock;
        }
        array_push($data, $name);
    }
    echo json_encode($data);
    exit;
}
// Update Invoice  Item
if (isset($_POST["invoiceID"]) && isset($_POST["discount"]) && isset($_POST["due"])) {
    $invoiceID = $_POST["invoiceID"];
    $discount = $_POST["discount"];
    $due = $_POST["due"];
    $userID = $_SESSION['user_id'];
    global $time;
    $data = $db->result_one("SELECT * FROM ph_invoice WHERE id = '" . $invoiceID . "'");
    $invoice_date = new DateTime($data->time);
    if (($due + $discount + $data->paid) == $data->total) {
        $sql = "Update ph_invoice SET userID='$userID', discount='$discount', due='$due', time='$time' WHERE id = '" . $invoiceID . "'";
        $db->query($sql);
    } else {
        echo "<div class='alert alert-danger'> Your post is not submitted.Don't try to do illegal work. </div>";
    }
}
