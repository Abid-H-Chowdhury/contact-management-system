<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(LIB_PATH.DS.'database.php');

class User extends DatabaseObject {
	
	protected static $table_name="user";
	protected static $db_fields = array('id', 'eid', 'fname', 'lname', 'f_name', 'm_name', 'password', 'sex', 'birth_day', 'blood', 'religion', 'cell', 'phone', 'email', 'address', 'per_address',
	'city', 'state', 'position', 'department', 'education', 'join_date', 'leave_date', 'access_group', 'status', 'image',);
	
	public $id;
	public $eid;
	public $fname;
	public $lname;
	public $f_name;
	public $m_name;
	public $password;
	public $sex;
	public $birth_day;
	public $blood;
	public $religion;
	public $cell;
	public $phone;
	public $email;
	public $address;
	public $per_address;
	public $city;
	public $state;
	public $position;
	public $department;
	public $education;
	public $join_date;
	public $leave_date;
	public $access_group;
	public $status;
	public $image;

	
  public function full_name() {
    if(isset($this->fname) && isset($this->lname)) {
      return $this->fname . " " . $this->lname;
    } elseif(isset($this->fname)) {
      return $this->fname;
    }
	elseif(isset($this->lname)) {
      return $this->lname;
    }
	else {
      return "";
    }
	
  }

	public static function authenticate($userName="", $password="") {
    global $database;
    $userName = escape($userName);
    $password = escape($password);
	
    $sql  = "SELECT * FROM user ";
    $sql .= "WHERE eid = ? ";
    $sql .= "LIMIT 1";
    $stmt = $database->prepare($sql);
    $stmt->bind_param('s', $userName);
    $stmt->execute();
    $result = $stmt->get_result();
	$get_pass = $result->fetch_object(); 
	
    if(password_verify($password, $get_pass->password)) { 
       $result_array = self::find_by_sql("SELECT * FROM `user` WHERE eid = '{$userName}' LIMIT 1 ");
       return !empty($result_array) ? array_shift($result_array) : false;
    }
    else{
        return false;
    }
}

	// Common Database Methods
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  }
  
  public static function find_by_id($eid=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE eid='$eid' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
  }
  
  public static function find_by_sql($sql="") {
    global $database;
    $result_set = $database->query($sql);
    $object_array = array();
    while ($row = $database->fetch_array($result_set)) {
      $object_array[] = self::instantiate($row);
    }
    return $object_array;
  }

	public static function count_all() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM ".self::$table_name;
      $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}

	private static function instantiate($record) {
		// Could check that $record exists and is an array
    $object = new self;
		// Simple, long-form approach:
		// $object->id 			= $record['id'];
		// $object->username 	= $record['username'];
		// $object->password 	= $record['password'];
		// $object->first_name  = $record['first_name'];
		// $object->last_name 	= $record['last_name'];
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
	  $attributes = array();
	  foreach(self::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $database;
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}
	
	public function save() {
	  // A new record won't have an id yet.
	  return isset($this->id) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - INSERT INTO table (key, key) VALUES ('value', 'value')
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
	  $sql = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
	  $sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
	  if($database->query($sql)) {
	    $this->id = $database->insert_id();
	    return true;
	  } else {
	    return false;
	  }
	}

	public function update() {
	  global $database;
		// Don't forget your SQL syntax and good habits:
		// - UPDATE table SET key='value', key='value' WHERE condition
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
		  $attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". $database->escape_value($this->id);
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - DELETE FROM table WHERE condition LIMIT 1
		// - escape all values to prevent SQL injection
		// - use LIMIT 1
	  $sql = "DELETE FROM ".self::$table_name;
	  $sql .= " WHERE id=". $database->escape_value($this->id);
	  $sql .= " LIMIT 1";
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	
		// NB: After deleting, the instance of User still 
		// exists, even though the database entry does not.
		// This can be useful, as in:
		//   echo $user->first_name . " was deleted";
		// but, for example, we can't call $user->update() 
		// after calling $user->delete().
	}
                                            
  public function status_return($value) 
  {
    return $value==1 ? "Active" : "Inactive";      
  }
/**
 * @Return all staff list
**/     
    public static function employee_list($status=NULL) 
        {
            global $db;
            $sql = !empty($status) ? " WHERE `status`={$status} " : NULL;
            return $db->result_all("SELECT id,eid,fname,lname FROM `user` {$sql} ORDER BY fname ASC");
        }        
/**
 * @Return all department list
**/     
    public static function department_list() 
        {
            global $db;
            return $db->result_all("SELECT * FROM department ORDER BY name ASC");   
        }        
     public static function department_return($id) 
        {
            global $db;
            $department = $db->result_one("SELECT * FROM department WHERE id='$id'");
            return $department->name;    
        }
/**
 * @Return all designation list
**/     
    public static function designation_list() 
        {
            global $db;
            return $db->result_all("SELECT * FROM designation ORDER BY name ASC");   
        }        
     public static function designation_return($id) 
        {
            global $db;
            $department = $db->result_one("SELECT * FROM designation WHERE id='$id'");
            return $department->name;    
        } 
	 public static function user_return($userID)
        {
            global $db;
            if($userID!=0){
            
                $sql = $db->result_one("SELECT * FROM user WHERE id='$userID'");
                if(!empty($sql)){
                    $fullName = $sql->fname." ".$sql->lname;
                    return array("eid"=>$sql->eid, "email"=>$sql->email, "fname"=>$sql->fname, "lname"=>$sql->lname, 
                    "FullName"=>$fullName, "cell"=>$sql->cell, "edu"=>$sql->education, "depart"=>$sql->department);
                }else{
                    return array("eid"=>"", "email"=>"", "fname"=>"", "lname"=>"", "FullName"=>"Self", "edu"=>"", "cell"=>"", "depart"=>"");
                }
            }
            else{
               return array("eid"=>"", "email"=>"", "fname"=>"", "lname"=>"", "FullName"=>"Self", "edu"=>"", "cell"=>"", "depart"=>"");
            }
        
        }
    public static function hr_staff_shift($uID)
        {
            global $db;
            $sql = $db->result_one("SELECT * FROM hr_staff_shift WHERE eid='$uID'");
            if($sql){
            return array("shift_set"=>$sql->shift_set, "type"=>$sql->type);
            }else { array("shift_set"=>"", "type"=>""); }            
        }
    public static function hr_shift_list()
        {
            global $db;
            return $db->result_all("SELECT t1.*,t2.shiftName,t2.shiftShort FROM hr_shift_setting t1 INNER JOIN `hr_shift` t2 ON t1.shiftID=t2.id ");          
        }
        
     public static function staff_roster_shift_find($uID,$date)
        {
            global $db;
            $sql = $db->result_one("SELECT * FROM hr_roster_staff WHERE uid='$uID'");
            if(!empty($sql)){
              $dayname =  strtolower(date("D",strtotime($date))); // get day name like sun/mon
              $column = $dayname."_shift";
              return $sql->$column;
            }else { return ""; }            
        }              
   /**
 * @Return number of days
**/         
    public static function leave_report($fdate,$tdate,$status,$eid=NULL)
    {
        global $db;

        $statement = " `hr_attendance` WHERE (date BETWEEN '$fdate' AND '$tdate')  "; 
        empty($eid) ? NULL : $statement .= " AND eid='$eid' ";	
        empty($status) ? NULL : $statement .= " AND attend='$status' ";     
        return $db->result_one("SELECT COUNT(id) as num FROM {$statement}")->num;
    }   
    public static function status_attendance($value,$path=NULL){
        switch ($value) {
            case 1: 
            return  "Present";break;
            case 2: 
            return   "Absent";break;
            case 3: 
            return   "Late";break;
        }
    }         
/**
 * Return array of billing user
 * access_group 1=Admin, 4=account,6=receiption,5=billing
**/        
      public static function billing_users($status=NULL){
            
            global $db;
            $sql = "";
            if(!empty($status)){
                $sql = " WHERE t2.status='1' ";
            }
            return $db->result_all("SELECT t2.id,t2.fname FROM `ac_income` t1 INNER JOIN user t2 on t1.userID=t2.id {$sql} GROUP BY t1.userID ORDER BY fname ASC");
        
        } 

/**
 * Return array of billing user
 * access_group 1=Admin, 4=account,6=receiption,5=billing
**/        
      public static function billing_users_pharmacy(){
            
            global $db;
            return $db->result_all("SELECT * FROM user WHERE access_group IN(7,14) AND status='1' ORDER BY fname ASC");
        
        }
        
/**
 * Return array of billing user
 * access_group 1=Admin, 4=account,6=receiption,5=billing
**/        
      public static function payment_users(){
            
            global $db;
            return $db->result_all("SELECT t2.id,t2.fname FROM user t2 WHERE t2.access_group IN(1,4) AND t2.id<>1 ORDER BY t2.fname ASC");
        
        } 
                
/**
 * @Return login latest 5 access logs
**/         
    public static function access_logs($user_ID=NULL)
    {
        global $userID;
        $accessSQL = "";
        if($userID!=1){
            $accessSQL = " AND t1.userID<>1";
        }
        $userSQL = "";
        if(!empty($user_ID) OR !is_admin()){
            $userSQL = !empty($user_ID) ? " AND t1.userID='$user_ID'" : " AND t1.userID='$userID'";
        }
        
        return QB::query("SELECT t1.userID,t1.log_time,t2.fname FROM login_logs t1 INNER JOIN user t2 ON t1.userID=t2.id WHERE t1.status=1 {$accessSQL} {$userSQL} ORDER BY t1.id DESC LIMIT 0,5")->get();
    }
    
    public static function user_info($uid){
        global $db;
       if(is_numeric($uid)){
        return $db->result_one("SELECT t1.*,t2.id AS customerID, t2.zila,t2.thana,t2.union_id, t3.name AS departNmae, t4.name AS positionName, t5.name AS districtName, t6.name AS thanaName, t7.name AS uname FROM user t1
                                              INNER JOIN customer t2 ON t1.eid=t2.cid
                                              LEFT JOIN department t3 ON t1.department=t3.id
                                              LEFT JOIN designation t4 ON t1.position=t4.id
                                              LEFT JOIN district t5 ON t2.zila=t5.id
                                              LEFT JOIN thana t6 ON t2.thana=t6.id
                                              LEFT JOIN unions t7 ON t2.thana=t7.id
                                              WHERE t1.id='$uid' ");
      }else{
            return $db->result_one("SELECT t1.*,t2.id AS customerID,t2.zila,t2.thana,t2.union_id, t3.name AS departNmae, t4.name AS positionName, t5.name AS districtName, t6.name AS thanaName, t7.name AS uname FROM user t1
                                              INNER JOIN customer t2 ON t1.eid=t2.cid
                                              LEFT JOIN department t3 ON t1.department=t3.id
                                              LEFT JOIN designation t4 ON t1.position=t4.id
                                              LEFT JOIN district t5 ON t2.zila=t5.id
                                              LEFT JOIN thana t6 ON t2.thana=t6.id
                                              LEFT JOIN unions t7 ON t2.thana=t7.id
                                              WHERE t1.eid='$uid' ");
       }
    }	


    /*employee list clumn dynamic*/

    public static  function displayUserClumn($skip){
         $clumn=array();
         foreach (self::$db_fields as $key => $value) {
               if(!in_array($value, $skip)){
            $clumn[]= $value;   
               }
         }
        return $clumn;
    }

    public static function showClumnName($clumn){
        switch ($clumn) {

                case 'f_name':
                return "Father Name";
                break;

                case 'm_name':
                return "Mother Name";
                break;

                case 'birth_day':
                return "DOB";
                break;

                case 'cell':
                return "Mobile";
                break;

                case 'per_address':
                return "Permanant Address";
                break; 
                
                case 'address':
                return "Present Address";
                break;             

                case 'position':
                return "Designation";
                break;
                
                default: 
                $columnname = str_replace("_"," ",$clumn);
                return ucwords($columnname);
                break;    
        }

    }


    
 

 
}

//Role-based access control class
class Rbac{ 

//user permissions insert and update
 public static function save_user_role_permissions($post=''){

    
     $roleName= $post['user_role'];
     $permissionsId= $post['permissionsId'];
     
     if(empty($permissionsId)){
       $ckUserRole= QB::table('user_roles')->where('role_name',$roleName)->first();
       if(empty($ckUserRole)){
         $roleData=array("role_name"=>$roleName);
         $roleId=QB::table('user_roles')->insert($roleData); 
         $data= array(
                    "roleId"=>$roleId,
                    "perms"=>implode(",", $post['perms'])
                      );

         $query = QB::table('user_role_permissions')->where('roleId','=',$roleId);
         $ck_role = $query->first();
    
        if(empty($ck_role)){

           $insertId=QB::table('user_role_permissions')->insert($data);
          return $permissionsId=ID_encode($insertId);
           
          
          }
         
       }
     

     }else{
        
        $checkPermissions = QB::table('user_role_permissions')->where('id',$permissionsId)->first();
        $roleInfo= QB::table('user_roles')->where('id',$checkPermissions->roleId)->first();

        if($roleInfo->role_name != $roleName){
             QB::table('user_roles')->where('id',$roleInfo->id)->update(array("role_name"=>$roleName));
        }


         $data= array("perms"=>implode(",", $post['perms']) );
         QB::table('user_role_permissions')->where('id',$permissionsId)->update($data);
        
         return ID_encode($permissionsId); 
     }
  
    }


  public static function get_allPermissions($value=''){

       return $result = QB::query("SELECT * FROM user_role_permissions  ORDER BY roleId ASC")->get();
    }

 

  public static function isAthorized($mypermission){
        
    global $flash,$SUPER_ADMIN;
        $mypermissionArray=explode(",",$mypermission);
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
       if(in_array($_SESSION['eid'], $SUPER_ADMIN)){     
     
           return true;
    
          }elseif(!array_intersect($mypermissionArray,$_SESSION['PERMISSIONS'])) {

          //its for prevent direct access 
          $flash->error("You don't have permission to access.","index.php?page=404&access=denied");

          }else{
         
           return true;
          
       }
  }

  public static function isMenuAthorized($permissions){
    global $SUPER_ADMIN;
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


/**
 * Return user role access  List 
**/

public static function get_all_user_role() {
        $query = QB::table('user_roles')->select('*')->orderBy('role_name','ASC');
         return $result = $query->get();
    } 


/**
 * Return user role access name 
**/   

  public static function return_access($roleID){ 
      
   $roleInfo= QB::table('user_roles')->where('id','=',$roleID)->first();
   if(!empty($roleInfo)){
    return $roleInfo->role_name;
   }
  }



}
?>