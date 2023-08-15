<?php

/**
 * @author Sharif Ahmed
 * @Email winsharif@gmail.com
 * @http://esteemsoftbd.com
 * @copyright 2014
 * 
 */

include("class/initialize.php");
require_once("page_title.php");

/**
if ($_SESSION['access_group']!="1") {
		header("location: login.php");
        exit();
}
 **/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- <title><?php// echo $Page_Title . " | (LIS) | " . is_setting_session("COMPANY_SHORT_NAME"); ?></title> -->
     <title><?php echo $Page_Title  ?></title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Arch Complete And Easy to use Hospital Management Software by esteemsoftbd.com" />
    <meta name="author" content="winsharif@gmail.com" />
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo SITE_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
    <!-- MetisMenu CSS -->
    <link href="<?php echo SITE_URL; ?>css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet" />
    <!-- template CSS -->
    <link href="<?php echo SITE_URL; ?>css/template.css" rel="stylesheet" />
    <!-- Custom Fonts -->
    <link href="<?php echo SITE_URL; ?>includes/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Datepicker CSS -->
    <link rel="stylesheet" media="screen" href="<?php echo SITE_URL; ?>js/plugins/datepicker/css/datepicker.css" type="text/css" />
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>css/custom.css" rel="stylesheet" />
    <link href="css/lis.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo SITE_URL; ?>css/print.css" rel="stylesheet" type="text/css" media="print" />
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" href="css/style.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->