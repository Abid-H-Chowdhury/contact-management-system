<?php
include_once("headlink.php");
if ($_POST['type'] == "") {
    $query = QB::query("SELECT * FROM divisions ORDER BY name ASC")->get();
    $str = "";
    foreach ($query as $row) {
        $str .= "<option value='{$row->id}'>{$row->name}</option>";
    }
} else if ($_POST['type'] == "districtData") {
    $qu = QB::query("SELECT * FROM district  WHERE divId = {$_POST['id']} ORDER BY name ASC")->get();
    $str = "";
    $str .= "<option value=''>Select District</option>";
    foreach ($qu as $row) {
        $str .= "<option value='{$row->id}'>{$row->name}</option>";
    }
} else if ($_POST['type'] == "thanaData") {
    $quer = QB::query("SELECT * FROM thana WHERE district_id = {$_POST['id']} ORDER BY name ASC")->get();
    $str = "";
    $str .= "<option value=''>Select Thana</option>";
    foreach ($quer as $row) {
        $str .= "<option value='{$row->id}'>{$row->name}</option>";
    }
}
echo $str;
