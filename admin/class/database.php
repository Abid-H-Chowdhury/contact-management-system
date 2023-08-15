<?php

require_once(LIB_PATH.DS.'config.php');

class MySQLDatabase {
	
	public $connection;
	public $last_query;
	private $magic_quotes_active;
	private $real_escape_string_exists;
	
    function __construct()
        {
            $this->open_connection();
        /*    $this->magic_quotes_active= get_magic_quotes_gpc();*/
            $this->real_escape_string_exists = function_exists( "mysqli_real_escape_string" );
        }

	public function open_connection() 
        {
    		$this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
    		if (!$this->connection) 
                {
    			     die("Database connection failed: " . mysqli_error($this->connection));
                } 
                else 
                {
        			$db_select = mysqli_select_db($this->connection,DB_NAME);
					mysqli_query($this->connection,"SET CHARACTER SET utf8");
        			if (!$db_select) 
                    {
       				die("Database selection failed: " . mysqli_error($this->connection));
        			}
                }
    	}

	public function close_connection() {
		if(isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}
    
    public function __destruct() 
   {
		$this->close_connection();
   }

	public function query($sql) {
		$this->last_query = $sql;
		$result = mysqli_query($this->connection,$sql) or die(mysqli_error($this->connection)); //or redirect_to("404.php");
		$this->confirm_query($result);
		return $result;
	}
	/////////////////////////////////////// using prepare statement by sharif //////////////////////////////////
	public function prepare($sql) {
		$result =mysqli_prepare($this->connection,$sql) or die(mysqli_error($this->connection)); //or redirect_to("404.php");
		return $result;
	}
	public function execute($stmt) {
		$result = mysqli_stmt_execute($stmt) or die(mysqli_error($this->connection));
		return $result;
	}	
	public function fetch($stmt) {
		$result = mysqli_stmt_fetch($stmt) or die(mysqli_error($this->connection));
		return $result;
	}
	public function result_one($sql) {
		$stmt=$this->prepare($sql);
		$this->execute($stmt);
		//mysqli_stmt_store_result($stmt);
		$result = mysqli_stmt_get_result($stmt) or die(mysqli_error($this->connection));
		$result = mysqli_fetch_object($result);
		return $result;
	}
	
	public function result_all($sql) {
		$stmt=$this->prepare($sql);
		$this->execute($stmt);
		//mysqli_stmt_store_result($stmt);
		$result_set = mysqli_stmt_get_result($stmt) or die(mysqli_error($this->connection));
		$result = array();	
		while($r = mysqli_fetch_object($result_set)) { 
		 $result[] = $r;
		}
		return $result;
	}
	
    public function number_rows($sql) {
    $result_set=$this->query($sql);
    return mysqli_num_rows($result_set);
    }
	public function close($stmt) {
		$stmt=$this->prepare($stmt);
		$result = mysqli_stmt_close($stmt) or die(mysqli_error($this->connection));
		return $result;
	}
    
    public function bindQueryParams($sql, $param_type, $param_value_array) {
        $param_value_reference[] = & $param_type;
        for($i=0; $i<count($param_value_array); $i++) {
            $param_value_reference[] = & $param_value_array[$i];
        }
        call_user_func_array(array(
            $sql,
            'bind_param'
        ), $param_value_reference);
    }
/**
* any insert query 
*/    
   public function insert_query($query, $param_type, $param_value_array) {
        $sql = $this->prepare($query);
        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
    }
/**
* any update query 
*/     
   public function update_query($query, $param_type, $param_value_array) {
        $sql = $this->prepare($query);
        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
    }
//////////////////////////////////////////////////////////////////////////////////	

	public function escape_value( $value ) {
		if( $this->real_escape_string_exists ) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $this->magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysqli_real_escape_string($this->connection, $value );
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$this->magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}
	
	// "database-neutral" methods
    public function fetch_array($result_set) {
    return mysqli_fetch_array($result_set);
  }
  
    public function select_all($table) {
    $result_set=$this->query("SELECT * FROM $table");
	return mysqli_fetch_assoc($result_set);
    }
  
    public function fetch_assoc($result_set) {
    return mysqli_fetch_assoc($result_set);
  }
  
  public function total_rows($table) {
  return mysqli_num_rows($this->query("SELECT * FROM $table")); // find total number of rows
  }
  public function num_row($result_set) {
   return mysqli_num_rows($result_set);
  }
  public function insert_id() {
    // get the last id inserted over the current db connection
    return mysqli_insert_id($this->connection);
  }
  
  public function affected_rows() {
    return mysqli_affected_rows($this->connection);
  }

	private function confirm_query($result) {
		if (!$result) {
	    $output = "Database query failed: " . mysqli_error($this->connection) . "<br /><br />";
	    //$output .= "Last SQL query: " . $this->last_query;
	    die( $output );
		}
	}
    
    public function state_con($value)
    {
       $query = $this->query("SELECT name FROM state WHERE id='$value'");   
       $result = $this->fetch_assoc($query);    
       return $result["name"];
    }
    public function access_control()
    {
       if($_SESSION["level"]=='0' OR empty($_SESSION["level"])) // if user is not in admin group
        {
        	redirect_to("404.php");
        }
    }
    
    
    function pagination($query,$per_page=10,$page=1,$url='?') {   
    
    $query = "SELECT COUNT(*) as `num` FROM {$query}";
    $row = mysqli_fetch_array($this->query($query));
    $total = $row['num'];
    $adjacents = "2"; // default was  2
     
    $firstlabel = "&lsaquo;&lsaquo; First";
    $prevlabel = "&lsaquo; Prev";
    $nextlabel = "Next &rsaquo;";
	$lastlabel = "Last &rsaquo;&rsaquo;";
     
    $page = ($page == 0 ? 1 : $page);  
    $start = ($page - 1) * $per_page;                               
     
    $first = 1;
    $prev = $page - 1;                          
    $next = $page + 1;
     
    $lastpage = ceil($total/$per_page);
     
    $lpm1 = $lastpage - 1; // //last page minus 1
     
    $pagination = "";
    if($lastpage > 1){   
        $pagination .= "<ul class='pagination'>";
        $pagination .= "<li class='page_info'>&nbsp;Page {$page} of {$lastpage}</li>";
             
            if ($page > 1) { $pagination.= "<li><a href='{$url}pages={$first}'>{$firstlabel}</a></li>";
                           $pagination.= "<li><a href='{$url}pages={$prev}'>{$prevlabel}</a></li>"; }
             
        if ($lastpage < 3 + ($adjacents * 2)){   
            for ($counter = 1; $counter <= $lastpage; $counter++){
                if ($counter == $page)
                    $pagination.= "<li><a class='active'>{$counter}</a></li>";
                else
                    $pagination.= "<li><a href='{$url}pages={$counter}'>{$counter}</a></li>";                    
            }
         
        } elseif($lastpage > 3 + ($adjacents * 2)){
             
            if($page < 1 + ($adjacents * 2)) {
                 
                for ($counter = 1; $counter < 2 + ($adjacents * 2); $counter++){
                    if ($counter == $page)
                        $pagination.= "<li><a class='active'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}pages={$counter}'>{$counter}</a></li>";                    
                }
                $pagination.= "<li class='dot'></li>";
                $pagination.= "<li><a href='{$url}pages={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}pages={$lastpage}'>{$lastpage}</a></li>";  
                     
            } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                 
                $pagination.= "<li><a href='{$url}pages=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}pages=2'>2</a></li>";
                $pagination.= "<li class='dot'></li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='active'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}pages={$counter}'>{$counter}</a></li>";                    
                }
                $pagination.= "<li class='dot'></li>";
                $pagination.= "<li><a href='{$url}pages={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}pages={$lastpage}'>{$lastpage}</a></li>";      
                 
            } else {
                 
                $pagination.= "<li><a href='{$url}pages=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}pages=2'>2</a></li>";
                $pagination.= "<li class='dot'></li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='active'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}pages={$counter}'>{$counter}</a></li>";                    
                }
            }
        }
		 // temp solution for not showing pagination
         if(!isset($counter)){
            $counter = $total;
         }
         
            if ($page < $counter - 1) {
            	$pagination.= "<li><a href='{$url}pages={$next}'>{$nextlabel}</a></li>";
				$pagination.= "<li><a href='{$url}pages=$lastpage'>{$lastlabel}</a></li>";
			}
         
        $pagination.= "</ul>";        
    }
     
    return $pagination;
}
	
}

$database = new MySQLDatabase();
$db =& $database;
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

/**
 * This is for pixie query builder from usman.it
 */
require 'Query_builder/vendor/autoload.php';

$config = array(
            'driver'    => 'mysql', // Db driver
            'host'      => DB_SERVER,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASS,
            'charset'   => 'utf8', // Optional
            'collation' => '', // Optional
            'prefix'    => '', // Table prefix, optional
        );

new \Pixie\Connection('mysql', $config, 'QB');
?>