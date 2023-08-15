<?php
    require_once('class.phpmailer.php');
    include("class.smtp.php");
	//require (XCRUD_PATH . '/xcrud_config.php'); // configuration     
	
	/* $mailhost = "";
	   $username = "";
	   $mailpass = "";
	   $mail_organization = "";
	   $emailfrom = "";*/
	   	
    define("HOST", Xcrud_config::$mail_host);
	define("PORT", Xcrud_config::$mail_port);
	define("SMTPAUTH",Xcrud_config::$smtp_auth);
	define("USERNAME",Xcrud_config::$username);
	define("PASSWORD",Xcrud_config::$password);
	define("EMAILFROM",Xcrud_config::$emailfrom);
	define("SMTPSECURE",Xcrud_config::$smtpsecure);
	
	
	function get_include_contents($filename, $variablesToMakeLocal) {
        extract($variablesToMakeLocal);           
        ob_start();
        include $filename;
        return ob_get_clean();            
        return false;
    }
	
	function initializeMailer($email){
	      $mail = new PHPMailer();
		  $mail->SetLanguage("en", 'language/');
          $mail->IsSMTP();
          $mail->Host       = HOST;
          $mail->SMTPAuth   = SMTPAUTH;
		  $mail->Port       = PORT;  
          $mail->Username   = USERNAME;
          $mail->Password   = PASSWORD;
          $mail->From       = USERNAME;
          $mail->FromName   = EMAILFROM;
		  $mail->AddAddress("$email","");
          $mail->WordWrap   = 50;
          $mail->IsHTML(true);
		  $mail->SMTPSecure = SMTPSECURE;
		  /*$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)*/
	     return $mail;
	}
	
	function updateOnPoCRequests(){
	   
       $fetchSQL = "SELECT pocid,name,phone,email,country,institution,orgtype,studentstotal,stafftotal,programstotal,coursetotal FROM poc_request WHERE isadminupdated='N'";
	   $result = mysql_query($fetchSQL);
	   
	    while ($row = mysql_fetch_array($result)) {

           //$row already an array
		   $message = get_include_contents('poc_templ.php',$row); 
		   $mail = initializeMailer();
		   $mail->Subject = "PoC Request: #".$row[0];
           $mail->Body    = $message;
		   
		   if($mail->Send()){ 

		   	$updateSQL="UPDATE poc_request SET isadminupdated='Y' where pocid=".$row[0];
			$result_= mysql_query($updateSQL);
		   
		   }
	   
        }
        
        mysql_free_result($result);	   
	}
	
	
	function updateOnPartnerApplication(){
		 $fetchSQL = "SELECT id,partnertype,region,name,jobtitle,organization,email,website,country,city,streetaddress,fax,businesstype,yearstart,employees,technicalteam,technicalsupport,message FROM partner_application WHERE isadminupdated='N'";
	     $result = mysql_query($fetchSQL);
	   
	    while ($row = mysql_fetch_array($result)) {
           //$row already an array
		   $message = get_include_contents('partner_templ.php',$row); 
		   $mail = initializeMailer();
		   $mail->Subject = "Partner Application: #".$row[0];
           $mail->Body    = $message;
		   if($mail->Send()){ 
		      $updateSQL="UPDATE partner_application SET isadminupdated='Y' where id=".$row[0];
			  $result_ = mysql_query($updateSQL);
		   }
	   
        }
        mysql_free_result($result);	
    
	}
	
	function sendMail($header,$email,$content,$from){
	
		$mail = initializeMailer($email);
		$mail->Subject = $header;
		$mail->Body    = $content;
		$mail->FromName   = $from;
	
		if($mail->Send()){
			//$updateSQL="UPDATE contactus SET isadminupdated='Y' where contactusid=".$row[0];
			//$result_ = mysql_query($updateSQL);
			echo "Email sent successfully to " . $email;
		}else{
			echo "Failed to send email";
		}
	
	}
	
	function sendActivationMail($email,$content){
		
		$mail = initializeMailer($email);
		$mail->Subject = "Activate Account";
		$mail->Body    = $content;
		
		if($mail->Send()){
			
			
			//$updateSQL="UPDATE contactus SET isadminupdated='Y' where contactusid=".$row[0];
			//$result_ = mysql_query($updateSQL);
		}
		
	}
	
	function updateOnGenContact(){
		 $fetchSQL = "SELECT contactusid,name,jobtitle,organization,phone,email,website,country,hearabtus,message FROM contactus WHERE isadminupdated='N'";
	   $result = mysql_query($fetchSQL);
	   
	    while ($row = mysql_fetch_array($result)) {
           //$row already an array
		   $message = get_include_contents('contact_templ.php',$row); 
		   $mail = initializeMailer();
		   $mail->Subject = "General Contact: #".$row[0];
           $mail->Body    = $message;
		   if($mail->Send()){ 
		      $updateSQL="UPDATE contactus SET isadminupdated='Y' where contactusid=".$row[0];
			  $result_ = mysql_query($updateSQL);
		   }
	   
        }
        mysql_free_result($result);	
	
	}
	
	
	function updateOnDownloads(){
		 $fetchSQL = "SELECT id,name,jobtitle,organization,phone,email,heardus,type,version FROM downloads WHERE isadminupdated='N'";
	   $result = mysql_query($fetchSQL);
	   
	    while ($row = mysql_fetch_array($result)) {
           //$row already an array
		   $message = get_include_contents('download_templ.php',$row); 
		   $mail = initializeMailer();
		   $mail->Subject = "Academia Server Download: #".$row[0];
           $mail->Body    = $message;
		   if($mail->Send()){ 
		      $updateSQL="UPDATE downloads SET isadminupdated='Y' where id=".$row[0];
			  $result_ = mysql_query($updateSQL);
		   }
	   
        }
        mysql_free_result($result);	
	
	}
	
?>