<?php
require_once('database.php');


/**
 *@param permission id from user_permission table
 *@param file_permission is logical value if has no permission but direct hit in browser so its worked from page title file.
 */
function isAthorized($mypermission){
        
                global $flash,$SUPER_ADMIN;

              if(!isset($_SESSION['PERMISSIONS'])){
              
                $roleID=$_SESSION['access_group'];
                $permissionsGlobal = QB::table('user_role_permissions')->where('roleId','=',$roleID)->first();  
                  
               
                 if(!empty($permissionsGlobal)){

                    $permissions=explode(",", $permissionsGlobal->perms);
                    $_SESSION['PERMISSIONS']=$permissions;

                 }else{

                    $permissions=array();
                    $_SESSION['PERMISSIONS']=$permissions;
                 }

                 }   

                 // if s.admin then sadmin has all access
                 if (in_array($_SESSION['eid'], $SUPER_ADMIN)) {     
               
                    return true;
              
                 }elseif(!in_array($mypermission,$_SESSION['PERMISSIONS'])) {

                    //its for prevent direct access 
                    $flash->error("You don't have permission to access.","index.php?page=404&access=denied");

                    }else{
                   
                     return true;
                    
                 }


}

function isMenuAthorized($permissions){
 global $SUPER_ADMIN;
    if(isset($_SESSION['PERMISSIONS'])){
         if (in_array($_SESSION['eid'], $SUPER_ADMIN)) {     
               
                    return true;
              
                 }elseif(!array_intersect($permissions,$_SESSION['PERMISSIONS'])){
            return false;
        }else{
            return true;
        }
    }

}


function is_sadmin(){
	
	global $SUPER_ADMIN;
	if (in_array($_SESSION['eid'], $SUPER_ADMIN)) {
		return true;
	}else {
		return false;
	}
}

function is_esladmin(){
	
	if ($_SESSION['user_id']==1) {
		return true;
	}else {
		return false;
	}
}

function is_agent(){
	
	if (isset($_SESSION['is_agent']) && ($_SESSION['is_agent']==1)) {
		return true;
	}else {
		return false;
	}
}

function is_admin(){
	
	if (isset($_SESSION['access_group']) && ($_SESSION['access_group']==1)) {
		return true;
	}else {
		return false;
	}
}

function is_account(){
	
	if (isset($_SESSION['access_group']) && ($_SESSION['access_group']==4)) {
		return true;
	}else {
		return false;
	}
}

/**
 * @param array of permission
 * @param optional redirect to dashboard
 * @return access control 
 */
function has_permission($permission,$redirect=NULL,$fail_url=NULL){
	global $flash;
    
	if(empty($permission)){
	   return true;
	}
    elseif($_SESSION['access_group']=="1"){ // if admin then admin has all access
	   return true;
	}
    elseif(in_array($_SESSION['access_group'], $permission)) {
		if($redirect==NULL){
		  return true ;
		}else{
		  redirect_to("{$redirect}");
		}
	}else{
	   if($fail_url==NULL && !isset($_SESSION['access_group'])){
		  redirect_to("login.php");
		}elseif(!empty($fail_url)){
		  $flash->error("You Don't Have Permission To Access!!!",$fail_url);
		}
        else{
		 $flash->error("You Don't Have Permission To Access!!!","index.php?page=404");
		}
		
	}
}
/**
* get the next transaction number these are maintained in table ac_trans_type - Transaction Types
* Also updates the transaction number
5 sales invoice / Invoice NO
6 Customer Payment/sales receipt No
7 Purchase Bill/Bill No
* @param int type from trans type
* @return int transaction Number
*/ 
   function NextTransNo($name,$has_prefix=null) {
       global $db;
       $type = escape($type);
       if($has_prefix==1){ // only current year
            $prefix = substr(date("Y"),2);
       }elseif($has_prefix==1){ // only current year
            $prefix = substr(date("Y"),2);
       }
       $db->query("SELECT value FROM `settings` WHERE name='" . $name ."' FOR UPDATE");
       $db->query("UPDATE `ac_trans_type` SET transno = transno + 1 WHERE id = '" . $type . "'");
       $transNo = QB::table('ac_trans_type')->find($type);
       return $transNo->transno;
   }
   
function load_page ($pagename) {
		
	global $db,$database,$session,$footer_link,$flash,$lang;
	$page = $pagename.".php";
	
	if (file_exists($page)) {
		
		validate_logon();	
		include $pagename.".php";
        	
	}elseif(file_exists(SITE_ROOT.DIRECTORY_SEPARATOR."hrm".DIRECTORY_SEPARATOR.$page)) {
		
		validate_logon();	
		include SITE_ROOT.DIRECTORY_SEPARATOR."hrm".DIRECTORY_SEPARATOR.$pagename.".php";
        	
	}elseif(file_exists(SITE_ROOT.DIRECTORY_SEPARATOR."doctor".DIRECTORY_SEPARATOR.$page)) {

		validate_logon();	
		include SITE_ROOT.DIRECTORY_SEPARATOR."doctor".DIRECTORY_SEPARATOR.$pagename.".php";
        	
	}elseif(file_exists(SITE_ROOT.DIRECTORY_SEPARATOR."ot".DIRECTORY_SEPARATOR.$page)) {
		
		validate_logon();	
		include SITE_ROOT.DIRECTORY_SEPARATOR."ot".DIRECTORY_SEPARATOR.$pagename.".php";
        	
	}elseif(file_exists(SITE_ROOT.DIRECTORY_SEPARATOR."ipd".DIRECTORY_SEPARATOR.$page)) {
		
		validate_logon();	
		include SITE_ROOT.DIRECTORY_SEPARATOR."ipd".DIRECTORY_SEPARATOR.$pagename.".php";
        	
	}else {		
		include SITE_ROOT.DIRECTORY_SEPARATOR."404.php";      
	}
	
}
function ID_encode($id,$salt=NULL){
    return base64_encode($id);
}

function ID_decode($encoded_id,$salt=NULL){
    return base64_decode($encoded_id);
}

function convert_status($value,$path=NULL){
    switch ($value) {
        case 1: 
        return  "<center><img src='".$path."images/active.png'></center>"; break;
        case 2: 
        return  "<center><img src='".$path."images/completed.png'></center>"; break;
        case 0: 
        return  "<center><img src='".$path."images/inactive.png'></center>"; break;      
    }
}
/**
 * @parm 1/0 boolean 
 */
function convert_boolean($value){
    switch ($value) {
        case 1: 
        return  "Yes";
        case 0: 
        return  "No";
    }
}
function validate_logon () {
		
	if (!isset($_SESSION["user_name"])) {	
		redirect_to("login.php");
	}	
}
//check if setting in session or direct call from db
function is_setting_session($setting_name){
    
    if(isset($_SESSION[$setting_name])){
        return $_SESSION[$setting_name];
    }else{
        return $_SESSION[$setting_name] = get_setting($setting_name);
    }
}

function get_consultant_receipt_template(){

  $templateID = is_setting_session("PRINT_CONSULT_RECEIPT_TEMPLATE");
        if($templateID==1){
            $template = "print/receipt-consultation-print.php";
        }else{
            $template = "print/receipt-consultation-pos-print.php";
        }
     return $template;   
}

function strip_zeros_from_date( $marked_string="" ) {
  // first remove the marked zeros
  $no_zeros = str_replace('*0', '', $marked_string);
  // then remove any remaining marks
  $cleaned_string = str_replace('*', '', $no_zeros);
  return $cleaned_string;
}

function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    //header("Location: {$location}");
    echo "<script> location.replace('$location'); </script>";
    exit;
  }
}
// Display any message
function output_message($message="") {
  if (!empty($message)) { 
    return $message;
  } else {
    return "";
  }
}
function messages($message = '') {
    if($_SESSION['success'] != '') {
        $message = '<div class="msg-ok">'.$_SESSION['success'].'</div>';
        $_SESSION['success'] = '';
    }
    if($_SESSION['error'] != '') {
        $message = '<div class="msg-error">'.$_SESSION['error'].'</div>';
        $_SESSION['error'] = '';
    }
    return "$message";
}
// Display javascript  message $link use if need redirect
function do_alert($msg,$link=NULL) 
    {
        echo '<script type="text/javascript">             
        function Redirect()
		{
			window.location="'.$link.'";
		}
        alert("' . $msg . '"); 
        </script>';
        
        if(!empty($link)) {
          echo '<script type="text/javascript">             
			window.location="'.$link.'";
        </script>';
        }
    }
function only_alert($msg) 
    {
        echo '<script type="text/javascript">             
        alert("' . $msg . '"); 
        </script>';
    }    
 /**  
  * Return pop up    
  * @param str url  
  * @param str title+value
  * @param class  
  * @param int width  
  * @param int height  
  * @return return popup center windows  
  */  
 function popup_window($url,$title,$class=NULL,$width=1000,$height=500,$fa_icon=NULL)   
   {  
     return '<a class="'.$class.'" href="'.$url.'" onclick="popupwindow(\''.$url."','".$title."', '".$width."','".$height."'); return false;\">"."<i class='{$fa_icon}'></i> ".$title.'</a>';  
   }
/**  
* Return href link <a>   
* @param str url  
* @param str title+value
* @param class  
* @param int target  
* @return return popup center windows  
*/  
 function alink($url,$title,$class=NULL,$target=NULL,$fa_icon=NULL)   
   {  
     return '<a class="'.$class.'" href="'.$url.'" target="'.$target.'" ><i class="'.$fa_icon.'"></i>'.$title.'</a>';  
   }
/*      
function __autoload($class_name) {
	$class_name = strtolower($class_name);
  $path = LIB_PATH.DS."{$class_name}.php";
  if(file_exists($path)) {
    require_once($path);
  } else {
		die("The file {$class_name}.php could not be found.");
	}
}
*/

// Write into log.txt file for user activity
function log_action($action, $message="") {
	$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
	$new = file_exists($logfile) ? false : true;
  if($handle = fopen($logfile, 'a')) { // append
    $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
		$content = "| {$timestamp} , {$action} : {$message}\n";
    fwrite($handle, $content);
    fclose($handle);
    if($new) { chmod($logfile, 0755); }
  } else {
    echo "Could not open log file for writing.";
  }
}

/**
 * @param operation 1= created, 2=update, 3= delete, 4=reversed, 5=return,  activity_operation()
 * @return return array value 
 */    
function log_insert($operation,$description){
        global $time,$userID;
        $data = array(
            'operation' => $operation,
            'datetime' => $time,
            'description' => $description,
            'userID' => $userID
        );
        
        QB::table('log_activity')->insert($data);
   }
   
function log_activity_type($type){
    
    switch ($type) {
    case "1": 
      return  "Created"; break;
    case "2": 
      return  "Updated"; break;
    case "3": 
      return  "Deleted"; break;
    case "4": 
      return  "Reversed"; break; 
    default :
        return "";      
    }
    
}    
function validDate($date, $format = 'd-m-Y')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
function return_date($date="") {
  if (!empty($date) && ($date!='0000-00-00')) {
    return date("d-m-Y",strtotime($date));
  } else {
    return  "";
  }  
}
function return_time($datetime="") {
  if (!empty($datetime)) {
    return date("d-m-Y h:i A",strtotime($datetime));
  } else {
    return  "";
  }  
}

function return_time_12($time="") {
  if (!empty($time) &&  $time!="00:00:00") {
    return date("h:i A",strtotime($time));
  } else {
    return  "";
  }  
}

function return_date_time_24($time="") {
  if (!empty($time) &&  $time!="00:00:00") {
    return date("d-m-Y H:i:s",strtotime($time));
  } else {
    return  "";
  }  
}

function return_dayName($date="") {
  if (!empty($date)) {
    return date("l",strtotime($date));
  } else {
    return  "";
  }  
}

function return_dayName_short($day="") {
  switch ($day) {
    case "Saturday": 
      return  "sat_shift"; break;
    case "Sunday": 
      return  "sun_shift"; break;
    case "Monday": 
      return  "mon_shift"; break;
    case "Tuesday": 
      return  "tue_shift"; break; 
   case "Wednesday": 
      return  "wed_shift"; break;
    case "Thursday": 
      return  "thu_shift"; break;
    case "Friday": 
      return  "fri_shift"; break;
        default :
            return "";      
    }
}

function convert_date($date="") {
  if (!empty($date)) {
    return date("Y-m-d",strtotime($date));
  } else {
    return  "";
  }  
}
function convert_time($datetime="") {
  if (!empty($datetime)) {
    return date("Y-m-d H:i:s",strtotime($datetime));
  } else {
    return  "";
  }  
}

function validateTime($date, $format = 'H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
/**
 *@param formate can be Y-m-d H:i:s / Y-m-d / H:i:s
 *@return true / false  
 */
function validateDate($date, $format)
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
/**
 *@param formate can be H:i:s 24 hour 
 *@return hours difference like 7:25 7 hours 25 min
 */
function get_time_difference($time1, $time2)
{
	$time1 = strtotime("$time1");
	$time2 = strtotime("$time2");
    
    if(!isset($time1) OR !isset($time2) OR empty($time1) OR empty($time2)){
        return "";
    }
    
    if ($time2 < $time1)
    {
    	$time2 = $time2 + 86400;
    }

    $remainder = ($time2 - $time1) % 3600;
     $hours = (($time2 - $time1) - $remainder) / 3600;
     $min = round($remainder/60);
     return $hours.":".$min;
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

// return age month/year base on DOB
function return_age($dob){
    
    $from = new DateTime($dob);
    $to   = new DateTime('today');
    if( $from->diff($to)->y!=0){
    	return $from->diff($to)->y." Y";
    }
    elseif( $from->diff($to)->m!=0){
        return $from->diff($to)->m." M";
    }else{
        return $from->diff($to)->d." D";
    }
}
/**
 * // '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
// '%y Year %m Month %d Day'  =>  1 Year 3 Month 14 Days  // '%m Month %d Day'   =>  3 Month 14 Day  // '%d Day %h Hours'   =>  14 Day 11 Hours
// '%d Day'  =>  14 Days // '%h Hours %i Minute %s Seconds' =>  11 Hours 49 Minute 36 Seconds // '%i Minute %s Seconds' =>  49 Minute 36 Seconds // '%h Hours =>  11 Hours // '%a Days  =>  468 Days
 * */
function date_Difference($date_1 , $date_2 , $differenceFormat = '%a days %h hours' ){
    $datetime1 = new DateTime($date_1);
    if(!empty($date_2)){
        $datetime2 = new DateTime($date_2);
    }else{
        $date_2 = date("Y-m-d H:i:s");
        $datetime2 = new DateTime($date_2);
    }
    
    $interval = $datetime1->diff($datetime2);
    return $interval->format($differenceFormat);
}

function sum_time_duration($times) {
    
    $minutes = 0; //declare minutes either it gives Notice: Undefined variable
    // loop throught all the times
    foreach ($times as $time) {
        list($hour, $minute) = explode(':', $time);
        $minutes += $hour * 60;
        $minutes += $minute;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    return sprintf('%02d:%02d', $hours, $minutes);
}

function get_setting($name){
        global $db;
        $value = $db->result_one("SELECT * FROM settings WHERE name='$name'");
        return $value->value;
}

function update_setting($value,$name){
        global $db,$time;
        $db->query("UPDATE `settings` SET `value` = '$value',`time` = '$time' WHERE `name` ='$name'");
   }

/**
 * get SMS/Email template from template table.
**/ 
function get_template($name){
        global $db;
        $value = $db->result_one("SELECT * FROM templates WHERE name='$name'");
        return $value->value;
} 

/**
 * update SMS/Email template to template table.
**/   
function update_template($value,$name){
        global $db,$time;
        $db->query("UPDATE `templates` SET `value` = '$value' WHERE `name` ='$name'");
   }   

// get current url.. this function made for pagination 
function curPageURL() {
	$targetpage1 = basename($_SERVER['REQUEST_URI']); 	//your file name  (the name of this file)
	//$targetpage2 = explode("&page", $targetpage1);
	list($targetpage) = explode("&pages", $targetpage1);
	return $targetpage=$targetpage."&";
}

// get current url.. this function made after save/update page
/**
 * get current url.. this function made after save/update page
**/

function CurrentPageURL() {
	$targetpage1 = basename($_SERVER['REQUEST_URI']); 	//your file name  (the name of this file)
	//$targetpage2 = explode("&page", $targetpage1);
	list($targetpage) = explode("&pages", $targetpage1);
	return $targetpage=$targetpage."&";
}

function active_class($page,$pagename){
    
    if(is_array($pagename)){
        
        if (in_array($page,$pagename))
        {     
            echo ' active';
        }    

    }else{
        if($page==$pagename) { echo ' active'; }
    }
	
} 

function is_select($param,$option){
	if($param==$option) { echo 'SELECTED'; }
} 

function page_header($value) {
    echo '<div class="row">
            <div class="col-lg-12">
                <div class="well">
                <h1 class="page-header">'.$value.'</h1>
                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div> <!-- /.row -->';
} 
//********************************************* Accounts Start ***************************************************// 	
function return_ac_type($typeID){
    switch ($typeID) {
       case 1: return "Income";
       case 2: return "Expense";
       case 3: return "Cost of Goods Sold";
       case 4: return "Asset";
       case 5: return "Liability";
       case 6: return "Bank";
       case 7: return "Equity";
    }
}
/**
 * @param AC ID
 * @return string Bank type cash=1,bank=2 
 */
function return_bank_type($bankID){
    switch ($bankID) {
       case 1: return "Cash";
       case 2: return "Bank";
    }
}
function return_head($headID){
    global $db;
    $result = $db->result_one("SELECT hname FROM ac_head WHERE id='$headID'");
    return !empty($result->hname) ? $result->hname : "";
}
function return_shead($sheadID){
    global $db;
    $result =$db->result_one("SELECT sname FROM ac_headsub WHERE id='$sheadID'");
    return !empty($result->sname) ? $result->sname : "";
}
function return_item($itemID){  
    global $db;
    $result = $db->result_one("SELECT iname FROM ac_item WHERE id='$itemID'");
    return !empty($result->iname) ? $result->iname : "";
}
/**
 * sign =1, BDT=2 Return number as currency format Ex in 123 out 123.00
 */
function currency($currency,$sign = NULL){
    if($sign==1){
      return number_format((float)$currency, Config::$currency_decimal, '.', ','). " ".CURRENCY_SIGN;
    }elseif($sign==2){
      return number_format((float)$currency, Config::$currency_decimal, '.', ','). " ".CURRENCY;
    }else{
      return number_format((float)$currency, Config::$currency_decimal, '.', ',');
    }  
    
}
//********************************************* Accounts End ***************************************************//

//////////////////////////////////////////////////////////////////////// Payroll Start ////////////////////////////////////////////////////////////////////////////

function payroll_type($type){
    switch ($type) {
       case 1: return "Gross";
       case 2: return "Bonus/Optional";
       case 3: return "Deduction";
    }
}

/**
 * @param leave type ID
 * @param column name
 * @return string type of leave
 */
 function return_leave_type($id,$name){
        
    global $db;
    return $db->result_one("SELECT * FROM `hr_leave` WHERE id='$id'")->$name;  
 }

//////////////////////////////////////////////////////////////////////// Payroll End ////////////////////////////////////////////////////////////////////////////

//********************************************* Others Start ***************************************************//
// Month List
function month_list(){  
    global $db;
    return $db->result_all("SELECT id,name FROM months ");
}
// Return Month name
function return_month($monthID){  
    global $db;
    $result = $db->result_one("SELECT name FROM months WHERE id='$monthID'");
    return $result->name;
}
// Return Zila name
function return_zila($zilaID){  
    global $db;
    $result = $db->result_one("SELECT name FROM district WHERE id='$zilaID'");
    if(!empty($result->name)){
        return $result->name;
    }else {
        return "";
    }
}
// Return Division name
function return_division($divID){  
    global $db;
    $result =$db->result_one("SELECT name FROM divisions WHERE id='$divID'");
    if(!empty($result->name)){
        return $result->name;
    }else {
        return "";
    }
}

// Return Thana name
function return_thana($thanaID){  
    global $db;
    $result =$db->result_one("SELECT name FROM thana WHERE id='$thanaID'");
    if(!empty($result->name)){
        return $result->name;
    }else {
        return "";
    }
}
function return_union($unionID){  
    global $db;
    $result =$db->result_one("SELECT name FROM unions WHERE id='$unionID'");
    if(!empty($result->name)){
        return $result->name;
    }else {
        return "";
    }
}

function return_type($typeID){  
    global $db;
    $result =$db->result_one("SELECT name FROM type WHERE id='$typeID'");
    if(!empty($result->name)){
        return $result->name;
    }else {
        return "";
    }
}
 /**  
  * Return patient's full address    
  * @param uhid  
  * @return Return patient's full address  
  */
function return_address($UHID,$short=NULL){  
    global $db;
    $result = $db->result_one("SELECT t1.address, t2.name AS uion, t3.name AS thana, t4.name AS zila FROM patients t1 
                              LEFT JOIN unions t2 ON t1.uion=t2.id
                              LEFT JOIN thana t3 ON t1.thana=t3.id
                              LEFT JOIN district t4 ON t1.zila=t4.id  WHERE  t1.id='$UHID'");
    if(!empty($short)){
        return $result->uion.", ".($result->thana).", ".($result->zila);
    }else{
        $string = !empty($result->address) ? $result->address."," : "";
       return $string .= " ".($result->uion).", <span>Thana: </span>".($result->thana).", <span>District:</span> ".($result->zila);
    }                          
    
}
 /**  
  * Return IPD type / ward/cabin name   
  * @param typeID   
  */
function return_ipd_type($typeID){
    global $db;
    return $db->result_one("SELECT name FROM ipd_type WHERE id='$typeID'")->name;
}
 /**  
  * Return IPD bed/cabin name   
  * @param bedID   
  */
function return_ipd_bed($bedID){
    global $db;
    return $db->result_one("SELECT name FROM ipd_bed WHERE id='$bedID'")->name;
}
 /**  
  * Return Disease name   
  * @param disease ID   
  */
function return_disease($diseaseID){
    global $db;
    $data = $db->result_one("SELECT name FROM disease WHERE id='$diseaseID'");
    if(!empty($data)){
        return $data->name;
    }else{
        return "";
    }
}
function return_bed_status($statusID){
    switch ($statusID) {
       case 0: return "<span class='bgRed returnStatus'>Booked</span>";
       case 1: return "<span class='bgGreen returnStatus'>Available</span>";
    }
}
function return_religion($ID){
    switch ($ID) {
       case 1: return "Islam";
       case 2: return "Hindu";
       case 3: return "Christian";
       case 4: return "Buddhism";
       case 5: return "Other";
    }
}
function return_edu($ID){
    switch ($ID) {
       case 1: return "N/A";
       case 2: return "High School";
       case 3: return "College";
       case 4: return "Graduate";
       case 5: return "Primary";
       default: return "N/A";
    }
}
function return_marital($ID){
    switch ($ID) {
       case 1: return "N/A";
       case 2: return "Single";
       case 3: return "Married";
       case 4: return "Divorced";
       case 5: return "Widowed";
       default: return "N/A";
    }
}
function return_sex($ID){
    switch ($ID) {
       case 1: return "Male";
       case 2: return "Female";
    }
}
// Return Patient Case Type name
function return_case_type($typeID){  
    global $db;
    $type=$db->result_one("SELECT name FROM case_type WHERE id='$typeID'");
    if(!empty($type)){
        return $type->name;
    }
}
// Escape htmlentities
function escape($string){
    global $db;
    $string = trim($string);
    return $db->escape_value($string);  
}


function clean_escape_br($string){
    
   $string = stripslashes($string);
   return str_replace(array("\r\n","\\r\\n","\r","\n","\\r","\\n")," <br/> ",$string);
}
 /**  
  * @Retun replacing by <br/> from string \r\n mysql escaping   
  */
function clean_escape($string){
   
   $string = stripslashes($string);
   return str_replace(array("\r\n","\\r\\n","\r","\n","\\r","\\n")," ",$string);
}


//********************************************* Pharmacy Start ***************************************************//

function po_status($status){
    switch ($status) {
       case 1: return "<span class='btn-warning'>Pending</span>";
       case 2: return "<span class='btn-success'>Approved</span>";
       case 3: return "<span class='btn-delivered'>Received</span>";
       case 4: return "<span class='btn-danger'>Cancelled</span>";
    }
}
function get_percentage($total,$discount){
    return ($discount*100)/$total;
}

function convert_purchase_item_pharma($itemID){
    switch ($status) {
       case 1: return ;
    }
}

////////////////////////////////////////// Browser And IP ///////////////////////////////////////////////////////////////    
function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $ub = 'Unknown';
    $platform = 'Unknown';

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'Linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'Mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
    }
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $ub = "Internet Explorer";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $ub = "Netscape";
    }

    return array(
        'name'      => $ub,
        'os'  => $platform
    );
}
// Function to get the website visitor IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

//Blood Bank Sex - added by mohiuddin
function return_sex_letter($ID){
    switch ($ID) {
       case 1: return "M";
       case 2: return "F";
    }
}

//followup status - added by mohiuddin
function return_followup_status($followupdate=''){
  $today=date('Y-m-d');
   $IPD_FOLLOWUP_WARNING_DAYS_START = get_setting("IPD_FOLLOWUP_WARNING_DAYS");
  $followupdate= date('Y-m-d', strtotime($followupdate. ' + '.$IPD_FOLLOWUP_WARNING_DAYS_START.' days'));

  if($followupdate<=$today){
   return "bgRed returnStatus";
  }else{
    return "bgOrange returnStatus";
  }

}

//Pcr Settings function by Mohiuddin
function get_pcr_settings($name=NULL, $doctor_id=NULL){
         global $db;  
		 if(empty($doctor_id)){
        if(!empty($_SESSION["doctor_dashboard_setDoctor"])){
              $doctor_id=$_SESSION["doctor_dashboard_setDoctor"]; 
        }else{
              $doctor_id=0;
        }
        }
		
		if(isset($_SESSION[$name]) && empty($doctor_id)){
			return $_SESSION[$name];
		}else{
			 $value = $db->result_one("SELECT * FROM `pcr_pressetup_item` LEFT JOIN pcr_pressetup ON pcr_pressetup_item.id=pcr_pressetup.itemID WHERE name='$name' AND doc_id='$doctor_id'"); 
			 if(!empty($value->data)){
			  return $_SESSION[$name] = $value->data;
			}
		}	
 }

// if pcr setting already exists then return true or no setup then return false
 function exist_pcr_settings($name){
         global $db;     
        if(!empty($_SESSION["doctor_dashboard_setDoctor"])){
              $doctor_id=$_SESSION["doctor_dashboard_setDoctor"]; 
        }else{
              $doctor_id=0;
        }
    
       $value = $db->result_one("SELECT COUNT(pcr_pressetup_item.id) AS num FROM `pcr_pressetup_item` LEFT JOIN pcr_pressetup ON pcr_pressetup_item.id=pcr_pressetup.itemID WHERE name='$name' AND doc_id='$doctor_id'"); 
       if(!empty($value->num)){
        return true;
      }else{ return false; }
 }
 
 function pcr_setting_update($PCR_NAME, $value, $condition, $doctor_id){
     global $db;  
    $pcr_oe_item = $db->result_one("SELECT * FROM `pcr_pressetup_item` WHERE name = '{$PCR_NAME}' "); 
    
    $pcr_oeItemID = $pcr_oe_item->id;                        
 
    if(empty($condition)){
        
        $new_pcr= $db->query("INSERT INTO pcr_pressetup (itemID, data, doc_id) VALUES ('$pcr_oeItemID', '$value', '$doctor_id')");
        
    }else{
        
        $new_pcr = $db->query("UPDATE pcr_pressetup SET  data='$value' WHERE itemID = '$pcr_oeItemID' AND doc_id='$doctor_id'  ");
        unset($_SESSION[$PCR_NAME]);
    }
 }

 function  return_payrol_item_name($id){
  if(!empty($id)){
      global $db;
      return $db->result_one("SELECT name FROM hr_payroll_item WHERE id='$id'")->name; 
  }
 }

function convert_month($date="") {
  if (!empty($date)) {
    $date = date_create_from_format('m-Y', $date);
    return date_format($date, 'Y-m');
  } else {
    return  "";
  }  
}

function documents_status(){
    return $array=array(
   "1"=>"Draft",
   "2"=>"Sent",
   "3"=>"Delivered",
   "4"=>"Waiting",
   "5"=>"Needs to Sign",
   "6"=>"Needs to View",
   "7"=>"Correcting",
   "8"=>"Voided",
   "9"=>"Declined",
   "10"=>"Completed",
   "11"=>"Expired",
   "12"=>"Recipient email bounced",
   "13"=>"Authentication Failed"
   );
}

function return_documents_status($id){
     switch ($id) {
       case 1:
         echo "Draft";
         break;
       case 2:
         echo "Sent";
         break;       

        case 3:
         echo "Delivered";
         break;

       case 4:
         echo "Waiting";
         break;      

         case 5:
         echo "Needs to Sign";
         break;

       case 6:
         echo "Needs to View";
         break;       

        case 7:
         echo "Correcting";
         break;

       case 8:
         echo "Voided";
         break;       

        case 9:
         echo "Declined";
         break;

       case 10:
         echo "Completed";
         break;

      case 11:
         echo "Expired";
         break;  

      case 12:
         echo "Recipient email bounced";
         break;

      case 13:
         echo "Authentication Failed";
         break;
         
       case 14:
         echo "Active";
         break;
         
         case 15:
         echo "Inactive";
         break;
         
       default:
         # code...
         break;
     }
 }
 
 function district_list(){
  global $db;
  return $db->result_all("SELECT * FROM `district` ORDER BY name ASC");
 }

 function division_list(){
  global $db;
  return $db->result_all("SELECT * FROM `divisions` ORDER BY name ASC");
 }

 function thana_list(){
  global $db; 
  return $db->result_all("SELECT * FROM `thana` WHERE district_id=(SELECT value FROM settings WHERE name='DEFAULT_SELECTED_DISTRICT') ORDER BY name ASC");
 }

  function union_list(){
    global $db;         
    return $db->result_all("SELECT * FROM `unions` WHERE psID=(SELECT value FROM settings WHERE name='DEFAULT_SELECTED_THANA') ORDER BY name ASC");
 }

 function type_list(){
  global $db;
  return $db->result_all("SELECT * FROM `type` ORDER BY name ASC");
 }

//Prescription/medicine slip/investigation slip page function by Mohiuddin
 function return_pcr_print_page($check){
  
  if($check=="1"){ //prescription print file 
    $pcr_setup_template =get_pcr_settings('PCR_PRESCRIPTION_TEMPLATE');

      if($pcr_setup_template==1){
        return  $page="eprescription-print1.php";
      }elseif($pcr_setup_template==2){
        return  $page="eprescription-print2.php";
      }elseif($pcr_setup_template==3){
         return $page="eprescription-print3.php";
      }elseif($pcr_setup_template==4){
        return  $page="eprescription-print4.php";
      }elseif($pcr_setup_template==5){
        return  $page="eprescription-print5.php";
      }else{
        return $page="eprescription-print1.php";
      } 

    }elseif($check=="2"){ //medicine slip print file 
      $medicine_Slip_template =get_pcr_settings('PCR_MEDICINESLIP_TEMPLATE');
     if($medicine_Slip_template==1){
         return $page="eprescription-slip1.php";
      }elseif($medicine_Slip_template==2){
         return $page="eprescription-slip2.php";
      }elseif($medicine_Slip_template==3){
         return $page="eprescription-slip3.php";
      }else{
         return $page="eprescription-slip1.php";
      } 

    }elseif($check=='3'){  //Investigation print file  
    $inv_Slip_template =get_pcr_settings('PCR_INV_SLIP_TEMPLATE');
    if($inv_Slip_template==1){
         return $page="investigation-slip1.php";
      }elseif($inv_Slip_template==2){
         return $page="investigation-slip2.php";
      }elseif($inv_Slip_template==3){
         return $page="investigation-slip3.php";
      }else{
        return  $page="investigation-slip1.php";
      } 
  }

 }


function data_list_for_prescription($type){
    global $db; 
    $typeSQL = array();
    if(!empty($type)){
        $typeSQL[] = "type={$type} ";
    }else{
        $typeSQL[] = " id<>0 ";
    }

    if(!empty($_SESSION["doctor_dashboard_setDoctor"])){
        $doctor_id=$_SESSION["doctor_dashboard_setDoctor"];
        $typeSQL[]="doc_id IN ({$doctor_id},0) ";
    }else{
         $typeSQL[]=" doc_id= 0 ";
    }

    $whereStr = implode(" AND ", $typeSQL);
   
    $query = "SELECT * FROM pcr_presdata WHERE {$whereStr} ORDER BY data ASC ";
    return $result = $db->result_all($query);   
        
}

function set_help_contents($data){  

   $status=isset($data["status"]) ? $data["status"] :0;

        $dataArray = array(
            'title' => $data["title"],
            'page' => $data["pageName"],
            'content' => $data["content"],
            'status' =>$status
        );

        if(isset($data["id"])){
            $id=$data["id"];
           QB::table('help_contents')->where('id',$id)->update($dataArray);

           }else{
           QB::table('help_contents')->insert($dataArray); 
           
          }  

}

function get_helpContentByPage($pageName,$con=NULL){
       global $db;
       if(!empty($con)){

        return $db->result_one("SELECT * FROM help_contents WHERE page='$pageName'"); 
            
            }else{

        return $db->result_one("SELECT * FROM help_contents WHERE page='$pageName'AND status='1'");
    }
}

function get_all_helpContents(){
       global $db;
       return $db->result_all("SELECT * FROM help_contents ORDER BY id ASC"); 
}

function get_single_helpContents($id){
    global $db;
    return $db->result_one("SELECT * FROM help_contents WHERE id='$id'");
}

 function delete_helpContent($id='')
{
   QB::table('help_contents')->where('id',$id)->delete();
}

// Common Header of Reports
function common_print_header($title,$subTitle=NULL, $method=NULL,$lastsubTitle=NULL){
   global $userID;
   $titleLength= strlen($title);
   if($titleLength < 46){ $px="18px"; }else{ $px= "12px"; } 
      if(!empty($method)){
         $method= 'Method: <strong>'.$method.'</strong>';
      }

   return $result='
   <div class="balance-title">
         <div class="col-xs-6 col-xs-offset-3">
            <h3>'.is_setting_session("COMPANY_NAME").'</h3>
            <h4><span class="fgBlue" style="font-size:{$px}"><strong>'.$title.'</strong></span></h4>
           <h6>'.$subTitle.'</h6>
           <h6>'.$lastsubTitle.'</h6>
         </div>
         <div class="col-xs-3">
            <div class="print-info">                   
           <p>'.$method.'</p>
               <p>Printed: <strong>'. date("d-m-Y h:i:s A").'</strong></p>          
               <p>Printed By: <strong>'.User::user_return($userID)['fname'].'</strong></p>            
            </div>
         </div>
      </div>';
     
}

 function file_upload($FILES,$fileSize,$subpath=NULL){

    
    global $flash;
    $image_stack=array();   
    $valid_formats = array("jpg", "jpeg", "png", "gif", "bmp", "pdf", "zip", "svg", "pptx", "ppt", "xls", "xlsx", "doc", "docx", "txt", "mp4", "mpg", "wmv", "mp3");
    $max_file_size = 1024*$fileSize; //$fileSize kb / MB
   
    $path =  SITE_ROOT.DS."uploads".DS.SUBDOMAIN.DS; 
    
    foreach ($FILES['name'] as $f => $name) 
    {  
        $file_tmp = $FILES['tmp_name'][$f];
        $file_name = $FILES['name'][$f];
        $file_type = $FILES['type'][$f];
        $file_size = $FILES['size'][$f];
    
        $ext = pathinfo($file_name, PATHINFO_EXTENSION); // get the file extension name like png jpg
        if ($FILES['error'][$f] == 4) {
            continue; // Skip file if any error found
        }          
        if ($FILES['error'][$f] == 0) {            
            if ($FILES['size'][$f] > $max_file_size) {
                 $maxSize = round($fileSize/1024);
                $flash->error("$name is too large!.Each File size must be less than {$maxSize} MB");
                continue; // Skip large files
            }
            elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) ){
                $flash->error("$name is not a valid format");
                continue; // Skip invalid file formats
            }
            else{ // No error found! Move uploaded files 
                 if(!empty($subpath)){
                if(move_uploaded_file($file_tmp,$path.$file_name))
                    $new_dir= uniqid().rand(1000, 9999).".".$ext; //round(microtime(true) * 999).rand(1000, 9999).".".$ext;
                   
                    $new_name = rename($path.$file_name,$path."{$subpath}/".$new_dir) ; // rename file name
                     array_push($image_stack,$new_dir);
                }else{                   

                 if(move_uploaded_file($file_tmp,$path.$file_name)){
                   /*$new_name = rename($path.$file_name,$path.$new_dir) ; */
                    array_push($image_stack,$file_name); 
                  }
                } 
                // file name store in array          
            }
        }               
    }
   return implode(",", $image_stack);
}

function thousandsCurrencyFormat($num) {

  if($num>1000) {

        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;

  }

  return $num;
}


function religion_list(){
  
     return $array=array(
   "1"=>"Islam",
   "2"=>"Hindu",
   "3"=>"Christian",
   "4"=>"Buddhism",
   "5"=>"Other"
   );
}
