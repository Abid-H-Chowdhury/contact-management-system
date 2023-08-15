<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(LIB_PATH.DS.'database.php');
require_once(LIB_PATH.DS.'functions.php');
require_once(SITE_ROOT.DS.'api/config_api.php');

class Hrm extends DatabaseObject {
	
  
 public static function return_month($month){  
        switch ($month) {
           case 1: return "January";
           case 2: return "February";
           case 3: return "march";
           case 4: return "April";
           case 5: return "May";
           case 6: return "June";
           case 7: return "July";
           case 8: return "August";
           case 9: return "September";
           case 10: return "October";
           case 11: return "November";
           case 12: return "December";
        }    
    } 

   public static function limit_to_numwords($string, $numwords){
          $excerpt = explode(' ', $string, $numwords + 1);
          if (count($excerpt) >= $numwords) {
            array_pop($excerpt);
          }
          $excerpt = implode(' ', $excerpt);
          return $excerpt;
        }

    public static function year_list($status=NULL) {
     
       if(isset($_SESSION['years']) && ($status==NULL OR $status==1)){
            return $_SESSION['years'];
       }elseif(!isset($_SESSION['years']) && ($status==NULL OR $status==1)){
            global $db;
           empty($status) ? $statement ="" : $statement = " WHERE status IN($status,2) ";
           return $_SESSION['years'] = $db->result_all("SELECT * FROM years $statement "); 
       }else{
           global $db;
           empty($status) ? $statement ="" : $statement = " WHERE status IN($status,2) ";
           return  $db->result_all("SELECT * FROM years $statement "); 
       }    
   }



    
      
}
?>