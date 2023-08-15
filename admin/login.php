<?php
/**
 * @author Sharif Ahmed
 * @Email winsharif@gmail.com
 * @http://esteemsoftbd.com
 * @copyright 2018
 * 
 */
 
require_once("class/initialize2.php");

$user = New User;
$time = date("Y-m-d H:i:s");

if(isset($_SESSION['access_group'])){
    // admin,account manager,lab consultant,lab tech, radiology tech have permission to access in Admin
    $access_to = array(1);
    has_permission($access_to,"index.php?page=dashboard","login.php");
}

if (isset($_POST['user_name']) && isset($_POST['password'])) {
  
  $myusername = $_POST['user_name'];
  $mypassword = $_POST['password']; 
  $userName = $db->escape_value($myusername);
  $status = '1';
	$sql  = "SELECT * FROM user ";
    $sql .= "WHERE eid = ? AND access_group IN (1) AND status=? ";
    $sql .= "LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('si', $userName,$status);
    $stmt->execute();
    $result = $stmt->get_result();
	$get_pass = $result->fetch_object(); 
    
    $ip = get_client_ip();
    $userInfo = getBrowser();
    $os = $userInfo['os'];
    $browser = $userInfo['name'];
     
	if($get_pass) {	
	// Check database to see if username/password exist.
	$found_user = User::authenticate($myusername, $mypassword);

	if ($found_user) {
		$session->login($found_user);
		$_SESSION['access_group'] = $found_user->access_group;		
		$_SESSION['eid'] = $found_user->eid;
		$_SESSION['user_id'] = $found_user->id;	
		$_SESSION['fname'] = $found_user->fname;	
		$_SESSION['email'] = $found_user->email;
        $sqlInsert = ("INSERT INTO `login_logs` (`userID`, `log_time`, `ip`, `browser`, `os`, `status`) VALUES (?, ?, ?, ?, ?, ?)");	
		$stmt = $db->prepare($sqlInsert);
        $stmt->bind_param('sssssi', $found_user->id,$time,$ip,$browser,$os,$status);
        $stmt->execute();

        redirect_to("index.php?page=dashboard");
	
	}
	else {
        $status = '2';
        $sqlInsert = ("INSERT INTO `login_logs` (`userID`, `log_time`, `ip`, `browser`, `os`, `status`) VALUES (?, ?, ?, ?, ?, ?)");	
		$stmt = $db->prepare($sqlInsert);
        $stmt->bind_param('sssssi', $myusername,$time,$ip,$browser,$os,$status);
        $stmt->execute();
        $flash->error("Invalid User or Password or Permission.Please try again!!!");
	   }
	}
	else {
        $status = '0';
        $sqlInsert = ("INSERT INTO `login_logs` (`userID`, `log_time`, `ip`, `browser`, `os`, `status`) VALUES (?, ?, ?, ?, ?, ?)");	
		$stmt = $db->prepare($sqlInsert);
        $stmt->bind_param('sssssi', $myusername,$time,$ip,$browser,$os,$status);
        $stmt->execute();
        $flash->error("Invalid User or Password or Permission.Please try again!!!");
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo is_setting_session("COMPANY_NAME"); ?></title>
	<meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content="Arch Hospital Management Software for <?php echo is_setting_session("COMPANY_NAME");?> Hospital By Esteem Soft Ltd"/>
    <meta name="author" content="sales@esteemsoftbd.com"/>
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo SITE_URL; ?>css/bootstrap.min.css" rel="stylesheet"/>
    <!-- Custom Fonts -->
    <link href="<?php echo SITE_URL; ?>includes/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>	
	<link rel="shortcut icon" href="favicon.ico"/>
</head>	

<body>
	<div class="container">		
		<div class="row">
			<div class="loginSectionWrapper">
				<div class="msgBox"><center><b><?php $flash->display(); ?></b></center></div>
				<div class="loginWrapper clearfix">
					<div class="col-lg-6 col-md-6 leftSideSection">
						<div class="leftSideColorBg">
							<div class="welcomeText">
								<h4>Welcome Back!</h4>
								<h3><?php echo is_setting_session("COMPANY_NAME"); ?></h3>
							</div>
							<div class="about-img text-center">
								<img src="images/cms-logo.png" alt="login-image" width="250">
							</div>
							<!-- <div class="amarChamberLogo text-center">
								<img src="images/arch-login-logo.svg" alt="arch-login-logo" width="130">
							</div> -->
						</div>
					</div>
					<div class="col-lg-6 col-md-6 rightSideSection">
						<div class="loginInfoSection">
							<!-- <div class="customerLogo text-center">
								<img src="images/lis-logo2.svg" alt="lis-logo">
							</div> -->
                            </br></br>
							<h3>Log In</h3>
							<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" role="form" method="POST">
								<fieldset>
									<div class="formInput">
										<i class="fa fa-user fa-fw"></i>
										<input class="form-control" placeholder="User ID" name="user_name" type="text" autofocus required="" pattern=".{5,}" maxlength="8"  />
									</div>
									<div class="formInput">
										<i class="fa fa-key fa-fw"></i>
										<input class="form-control" placeholder="Password" name="password" type="password" value="" required="" pattern=".{3,}"/>
									</div>

									<button class="btn-login form-control" type="submit">Log In</button>
								</fieldset>
							</form>
						</div>
					</div>
				</div>
				<!----- Footer Starts ----->
				<footer id="footer">
					<!--Footer-->
					<div class="footer-bottom">
						<div class="container">
							<div class="row">
								<p class="">Copyright © <?php echo date("Y");?> &amp; Developed By <a target="_blank" href="http://esteemsoftbd.com">Esteem Soft Ltd.</a><br> </p>
							</div>
						</div>
					</div>
				</footer>
				<!----- Footer Ens ----->
			</div>
		</div>
	</div>


<style type="text/css">
body {
   background: #F7F8FD;
 	font-family: 'Open Sans', sans-serif;
}
/*Custom Fonts Starts*/
 	/*Open Sans Fonts*/
  /*Regular*/
  @font-face {
      font-family: 'Open Sans';
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: local('Open Sans Regular'), local('OpenSans-Regular'), url('<?php echo SITE_URL;?>fonts/OpenSans/opensans-normal-400.woff2') format('woff2');
      unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  /*Semi Bold 600*/
  @font-face {
      font-family: 'Open Sans';
      font-style: normal;
      font-weight: 600;
      font-display: swap;
      src: local('Open Sans SemiBold'), local('OpenSans-SemiBold'), url('<?php echo SITE_URL;?>fonts/OpenSans/opensans-semibold-600.woff2') format('woff2');
      unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  /*Bold 700*/
  @font-face {
      font-family: 'Open Sans';
      font-style: normal;
      font-weight: 700;
      font-display: swap;
      src: local('Open Sans Bold'), local('OpenSans-Bold'), url('<?php echo SITE_URL;?>fonts/OpenSans/opensans-bold-700.woff2') format('woff2');
      unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
/*Custom Fonts Ends*/
/*Alert Message*/
.msgBox {
  width: 52em;
  margin: 0 auto;
}
.alert-danger{
	background: #ffcfcc;
	border-color: #ffcfcc;
	border-left: 10px solid #e85851;
	border-radius: 10px;
	color: #333;
}
/*Login Wrapper*/
.loginSectionWrapper {
    padding-top: 5em;
}
.loginWrapper {
	background: #fff;
	/*padding: 50px;*/
	width: 52em;
	border-bottom-left-radius: 100px;
	box-shadow: 0px 4px 6px #00000029;
	margin: 1em auto 2em;
}
.leftSideSection, .rightSideSection {
 	min-height: 30em!important;
}
/*Left Side Section*/
.leftSideSection {
	position: relative;
	background: #1d76bb;
	min-height: 100%;
	border-bottom-left-radius: 100px;
	padding: 20px;
	overflow: hidden;
	z-index: 1;
}
	/*LeftSideColorBg Circle Cloud*/
	.leftSideColorBg:before {
		position: absolute;
		content: "";
		background: #1769a9;
		top: -8em;
		left: -5em;
		width: 260px;
		height: 260px;
		border-radius: 50%;
		z-index: -1;
		transition: all 1s ease-in 0s;
	}
	.leftSideSection:hover .leftSideColorBg:before{
		width: 280px;
		height: 280px;
	}
	.loginWrapper:hover .leftSideSection .leftSideColorBg:before{
		width: 280px;
		height: 280px;
	}
	/*LeftSideColorBg Circle Cloud*/
.welcomeText {
 	color: #fff;
}
.welcomeText>h4 {
	font-size: 20px;
	font-weight: 400;
	margin: 0;
}
.welcomeText>h3 {
	font-weight: 700;
	font-size: 26px;
	height: 2.5em;
	line-height: 35px;
	margin: 10px 0;
}
/*About Image*/
.about-img {
	margin: 1.5em;
}
@keyframes leftImageAnimation{
	0% {
     	transform: translateY(0);
 	}
    100% {
     	transform: translateY(-10px);
 	}
}
/*.leftSideColorBg:hover .about-img>img{*/
.leftSideColorBg .about-img>img{
	animation: leftImageAnimation 1s infinite alternate;
	-webkit-animation: leftImageAnimation 1s infinite alternate;
}
/*About Image*/
/*Left Side Section*/
/*Right Side Section*/
.customerLogo{
	margin-top: 25px;
}
.customerLogo>img {
	width: auto;
	height: 100px;
}
.loginInfoSection > h3 {
	font-weight: 700;
	font-size: 26px;
	margin: 1em 0;
}
/*Form Input Field*/
.formInput {
	position: relative;
	margin-bottom: 15px;
}
.formInput > i {
	position: absolute;
	left: 10px;
	top: 17px;
	font-size: 17px;
	color: #1d76bb;
}
.formInput > input {
	height: 50px;
	border: 2px solid #999;
	box-shadow: none;
	border-radius: 10px;
	padding-left: 3em;
}
.formInput >input:hover, .formInput >input:focus {
	box-shadow: none;
	border: 2px solid #1d76bb;
}
p.rememberMe{
	color: #a2a2a2;
}
/*Form Input Field*/
/*Login Button*/
.btn-login {
	position: relative;
	background: #1d76bb;
	color: #fff;
	border: none;
	border-radius: 10px;
	box-shadow: none;
	font-size: 18px;
	font-weight: 600;
	z-index: 1;
	overflow: hidden;
	transition: color 0.4s linear;
	display: inline-block;
	height: 50px;
}
.btn-login::before {
	content: "";
	position: absolute;
	left: 0;
	top: 0;
	width: 101%;
	height: 101%;
	/*background: #04cfcf;*/
	background: #1769a9;
	z-index: 1;
	border-radius: 5px;
	transition: transform 0.5s;
	transition-timing-function: ease;
	transform-origin: 0 0;
	transition-timing-function: cubic-bezier(.5,1.6,.4,.7);
	transform: scaleX(0);
	border-radius: 0;
}
.btn-login:hover::before{
	transform:scaleX(1);
	color:#fff!important;
	z-index:-1
}
.btn-login:hover{
 	background-position:right
}
/*Login Button*/
a.forgot-password {
	color: #00498E;
	margin-top: 1em;
	display: block;
}
/*Footer/Copyright*/
.footer-bottom{
	text-align: center;
}
.footer-bottom p, .footer-bottom a {
	color: #a7a7a7!important;
}
img.arch-logo {
    height: 20px;
    margin-top: -10px;
}
/*Footer/Copyright*/

/*Smartphone Devices*/
@media (max-width: 575px){
	/*Login Wrapper*/
	.loginSectionWrapper {
	    padding-top: 0;
	}
	.loginWrapper {
		margin-top: 0;
		margin-bottom: 10px;
		width: 25em;
		padding: 0;
	}
	.leftSideSection {
  	min-height: 23em!important;
	}
	.rightSideSection{

	}
	/*Amar Chamber Logo*/
	.about-img{
		text-align: center;
		margin: .5em 0;
	}
	.about-img>img {
		height: 125px;
		width: auto;
	}
	.amarChamberLogo>img {
    	width: 100px;
	}
	/*Amar Chamber Logo*/
	.loginInfoSection > h3 {
    	margin: .7em 0;
	}
	.formInput {
    	margin-bottom: 10px;
	}
	a.forgot-password{
		text-align: center;
	}
	.footer-bottom p, .footer-bottom a{
		font-size: 10px;
	}
}
/*Mobile Device Landscape Mode*/
@media only screen and (min-width: 576px) and (max-width:  767px) and (orentation:  landscape){
	.loginSectionWrapper {
  	padding-top: 0;
	}
	.loginWrapper{
		width: 50em;
	}
	.leftSideSection, .rightSideSection{
    	width: 50%;
    	float: left;
	}
}
/*Mobile Device Landscape Mode*/

/*Tablet Devices*/
@media (min-width: 576px) and (max-width: 768px){
	.loginSectionWrapper {
    padding-top: 0;
	}
	/*Login Wrapper*/
	.loginWrapper {
		margin-top: 5em;
		width: 40em;
		padding: 0;
	}
}


/*Custom Checkbox/Radio Box Style*/
/*.checkboxWrapper {
  position: absolute;
  top: 50%;
  left: 20%;
  max-width: 60%;
  transform: translateY(-50%);
}*/
.checkboxWrapper label {
  color: #a2a2a2;
  font-weight: 400;
}
.checkboxWrapper input[type="checkbox"]:focus,
.checkboxWrapper input[type="radio"]:focus {
  box-shadow: 0 0 2px #999;
  outline: 0;
}
.point {
  cursor: pointer;
}
@supports (-webkit-appearance: none) or (-moz-appearance: none) {
 	.checkboxWrapper input[type="checkbox"],
  	.checkboxWrapper input[type="radio"] {
		-webkit-appearance: none;
		-moz-appearance: none;
		position: relative;
		display: inline-block;
		background: white;
		cursor: pointer;
		height: 20px;
		margin: 0;
		padding: 0;
		vertical-align: middle;
		border: 2px solid #999;
		-webkit-transition: background 0.3s, border-color 0.3s, box-shadow 0.2s;
		transition: background 0.3s, border-color 0.3s, box-shadow 0.2s;
		}
	.checkboxWrapper input[type="checkbox"]:after,
	.checkboxWrapper input[type="radio"]:after {
		content: "";
		display: block;
		position: absolute;
		top: 0;
		left: 0;
		-webkit-transition: opacity 0.2s, -webkit-transform 0.3s ease;
		transition: opacity 0.2s, -webkit-transform 0.3s ease;
		transition: transform 0.3s ease, opacity 0.2s;
		transition: transform 0.3s ease, opacity 0.2s, -webkit-transform 0.3s ease;
	}
	.checkboxWrapper input[type="checkbox"]:checked,
	.checkboxWrapper input[type="radio"]:checked {
		background: #1d76bb;
		border: 2px solid #1d76bb;
		-webkit-transition: opacity 0.3s,
		-webkit-transform 0.6s cubic-bezier(0.2, 0.85, 0.32, 1.2);
		transition: opacity 0.3s,
		-webkit-transform 0.6s cubic-bezier(0.2, 0.85, 0.32, 1.2);
		transition: transform 0.6s cubic-bezier(0.2, 0.85, 0.32, 1.2), opacity 0.3s;
		transition: transform 0.6s cubic-bezier(0.2, 0.85, 0.32, 1.2), opacity 0.3s,
		-webkit-transform 0.6s cubic-bezier(0.2, 0.85, 0.32, 1.2);
	}
	.checkboxWrapper input[type="checkbox"]:disabled,
	.checkboxWrapper input[type="radio"]:disabled {
		cursor: not-allowed;
		border: 2px solid #bbc1e1;
		opacity: 0.9;
	}
	.checkboxWrapper input[type="checkbox"]:disabled:checked,
	.checkboxWrapper input[type="radio"]:disabled:checked {
		background: #e1e6f9;
	}
	.checkboxWrapper input[type="checkbox"]:disabled + label,
  	.checkboxWrapper input[type="radio"]:disabled + label {
    	cursor: not-allowed;
  	}
  	.checkboxWrapper input[type="checkbox"]:hover:not(:checked):not(:disabled),
  	.checkboxWrapper input[type="radio"]:hover:not(:checked):not(:disabled) {
    	border: 2px solid #1d76bb;
  	}
  	.checkboxWrapper input[type="checkbox"]:focus,
  	.checkboxWrapper input[type="radio"]:focus {
    	border: 2px solid #19B3B3;
  	}

  	.checkboxWrapper input[type="checkbox"]:not(.switch),
  	.checkboxWrapper input[type="radio"]:not(.switch) {
    	width: 20px;
  	}
  	.checkboxWrapper input[type="checkbox"]:not(.switch):after,
  	.checkboxWrapper input[type="radio"]:not(.switch):after {
    	opacity: 0;
  	}
  	.checkboxWrapper input[type="checkbox"]:not(.switch):checked:after,
  	.checkboxWrapper input[type="radio"]:not(.switch):checked:after {
    	opacity: 1;
  	}
  	.checkboxWrapper input[type="checkbox"]:not(.switch):after {
	    top: 3px;
	    left: 6px;
	    width: 5px;
	    height: 9px;
	    border: 2px solid white;
	    border-top: 0;
	    border-left: 0;
	    -webkit-transform: rotate(20deg);
	    transform: rotate(20deg);
	  }
	  .checkboxWrapper input[type="checkbox"]:not(.switch):checked:after {
	    -webkit-transform: rotate(43deg);
	    transform: rotate(43deg);
	  }
	  .checkboxWrapper input[type="checkbox"].switch {
	    width: 38px;
	    border-radius: 11px;
	  }
	  .checkboxWrapper input[type="checkbox"].switch:after {
	    top: 2px;
	    left: 2px;
	    background: #bbc1e1;
	    width: 15px;
	    height: 15px;
	    border-radius: 50%;
	    -webkit-transform: translateX(0);
	    transform: translateX(0);
	  }
	  .checkboxWrapper input[type="checkbox"].switch:checked:after {
	    background: white;
	    -webkit-transform: translateX(15px);
	    transform: translateX(15px);
	  }
	  .checkboxWrapper input[type="checkbox"].switch:disabled:not(:checked):after {
	    opacity: 0.6;
	  }

	  .checkboxWrapper input[type="radio"] {
	    border-radius: 50%;
	  }
	  .checkboxWrapper input[type="radio"]:after {
	    background: white;
	    width: 19px;
	    height: 19px;
	    border-radius: 50%;
	    opacity: 0;
	    -webkit-transform: scale(0.7);
	    transform: scale(0.7);
	  }
	  .checkboxWrapper input[type="radio"]:checked:after {
	    -webkit-transform: scale(0.5);
	    transform: scale(0.5);
	  }									  	

}
*:before,
*:after {
 	box-sizing: inherit;
}
/*Custom Checkbox/Radio Box Style*/
</style>

<!-- jQuery Version 1.11.0 -->
<script src="<?php echo SITE_URL;?>js/jquery.min.js"></script>
<script type="text/javascript">
	// hide flash messages after some times
$(document).ready( function() {
  $('.dismissable').delay(10000).fadeOut();
});
</script>


</body>
</html>