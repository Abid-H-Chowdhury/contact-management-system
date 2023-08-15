<?php
defined('SUBDOMAIN')   ? null : define("SUBDOMAIN", "arch");
// Database Constants
defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
defined('DB_USER')   ? null : define("DB_USER", "root");
defined('DB_PASS')   ? null : define("DB_PASS", ""); //Esteem+Lis2023
defined('DB_NAME')   ? null : define("DB_NAME", "bangladeshG"); //ahms_royal

// Setup primary info
defined('MAIN_TITLE')   ? null : define("MAIN_TITLE", "Bangladesh Geo Location");
defined('COMPANY')   ? null : define("COMPANY", "Bangladesh Geo Location");
defined('COMPANY_PHONE')   ? null : define("COMPANY_PHONE", "01844004900");
defined('COMPANY_MAIL')   ? null : define("COMPANY_MAIL", "support@esteemsoftbd.com");
defined('COMPANY_ADDRESS')   ? null : define("COMPANY_ADDRESS", "Block A, Bashundhara, Dhaka");

defined('ZILLA')   ? null : define("ZILLA", 27);
defined('THANA')   ? null : define("THANA", 410);
defined('CURRENCY')   ? null : define("CURRENCY", " BDT");
defined('CURRENCY_SIGN')   ? null : define("CURRENCY_SIGN", " &#2547");
defined('SITE_URL')   ? null : define("SITE_URL", ""); //http://localhost 192.168.0.2

defined('SMS_ENDING')   ? null : define("SMS_ENDING", "My Company");
defined('PHARMA_COUNTER')   ? null : define("PHARMA_COUNTER", 4);
defined('CASH_IN_TYPE')   ? null : define("CASH_IN_TYPE", 1);

// some important info settings
date_default_timezone_set("Asia/Dhaka");
$FormAction = htmlspecialchars($_SERVER['PHP_SELF']);
$SUPER_ADMIN = array("A1611001", "a");
$date = date("Y-m-d");
$time = date("Y-m-d H:i:s"); // H= 24 , h =12
$END_FISCAL_YEAR = date("Y") - 1;

class Config
{
}

//HRM 
class Printing
{
}
