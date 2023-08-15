<?php



$page = (int)(!isset($_GET["pages"]) ? 1 : $_GET["pages"]);
if ($page <= 0) $page = 1;

$per_page = 25; //get_setting("PAGINATION_LIS"); // Set how many records do you want to display per page.

$startpoint = ($page * $per_page) - $per_page;
