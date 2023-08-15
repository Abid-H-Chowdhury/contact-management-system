<?php
// A class to help work with Sessions
// In our case, primarily to manage logging users in and out

// Keep in mind when working with sessions that it is generally 
// inadvisable to store DB-related objects in sessions

require_once(LIB_PATH.DS.'database.php');

class Session {
	
	private $logged_in=false;
	public $user_id;
	public $message;
	
	function __construct() {
		
        session_start();
	}
	
  public function is_logged_in() {
    return $this->logged_in;
  }

	public function login($user) {
    // database should find user based on username/password
    if($user){
      $this->user_id = $_SESSION['user_name'] = $user->id;
      $this->logged_in = true;
    }
  }
  
  public function logout() {
	//session_start();
    global $database;
    // for office module logout
    $if_office_module = $database->number_rows("SHOW TABLES LIKE 'ont_people'");
    if($if_office_module>0){
        $database->query("UPDATE `ont_people` SET `sessionid` = '' WHERE `ont_people`.`id` = '{$_SESSION['user_id']}'");
    }
    
    unset($_SESSION['user_name']);
    unset($_SESSION['eid']);
    unset($_SESSION['access_group']);
    unset($_SESSION['user_id']);
    unset($_SESSION['fname']);
    unset($_SESSION['email']);
    $this->logged_in = false;
	$session_destroy=session_destroy();
	if($session_destroy)
	{
		header("location:login.php"); 
	}
  }

	public function message($msg="") {
	  if(!empty($msg)) {
	    // then this is "set message"
	    // make sure you understand why $this->message=$msg wouldn't work
	    $_SESSION['message'] = $msg;
	  } else {
	    // then this is "get message"
			return $this->message;
	  }
	}

	private function check_login() {
    if(isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->logged_in = true;
    } else {
      unset($this->user_id);
      $this->logged_in = false;
    }
  }
  
	private function check_message() {
		// Is there a message stored in the session?
		if(isset($_SESSION['message'])) {
			// Add it as an attribute and erase the stored version
      $this->message = $_SESSION['message'];
      unset($_SESSION['message']);
    } else {
      $this->message = "";
    }
	}
	
}

class Token {
    
   function getMemberByUsername($username) {
        global $db;
        return $db->result_one("Select * from patients where id='{$username}' ");
    }
     
	function getTokenByUsername($username,$expired) {
	    global $db;
	    return $db->result_one("Select * from user_token_auth where username ='{$username}' and is_expired ='{$expired}' ");
    }
    
    function markAsExpired($tokenId) {
        global $db;
        $query = "DELETE FROM `user_token_auth` WHERE `user_token_auth`.`id`='{$tokenId}' ";
        //$expired = 1;
        //$result = $db->update($query, 'i', array($tokenId));
        $result = $db->query($query);
        return $result;
    }
    
    function insertToken($username, $type, $random_password_hash, $random_selector_hash, $expiry_date) {
        global $db;
        $query = "INSERT INTO user_token_auth (username, type, password_hash, selector_hash, expiry_date) values (?, ?, ?, ?, ?)";
        $result = $db->insert_query($query, 'sssss', array($username, $type, $random_password_hash, $random_selector_hash, $expiry_date));
        return $result;
    }
    
}

class Util {
    
    public function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i ++) {
            $token .= $codeAlphabet[$this->cryptoRandSecure(0, $max)];
        }
        return $token;
    }
    
    public function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) {
            return $min; // not so random...
        }
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }
    
  /*  public function redirect($url) {
        header("Location:" . $url);
        exit;
    }*/
    
    public function clearAuthCookie() {
        if (isset($_COOKIE["member_login"])) {
            setcookie("member_login", "");
        }
        if (isset($_COOKIE["random_password"])) {
            setcookie("random_password", "");
        }
        if (isset($_COOKIE["random_selector"])) {
            setcookie("random_selector", "");
        }
    }
}

$session = new Session();
$message = $session->message();

?>