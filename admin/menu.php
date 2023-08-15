<?php
$full_url = $_SERVER['REQUEST_URI'];
list($url) = explode("?", $full_url);  // get only file name
if (!isset($_GET["page"])) {
    // redirect if no page variable
    redirect_to("index.php?page=dashboard");
}
$page = $_GET["page"];
?>

<body>
  <div id="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top lab" role="navigation" style="margin-bottom: 0">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php?page=dashboard">Contact Management System</a>
      </div>
      <!-- /.navbar-header -->
      <div class="menuHide">
        <!-- Open & close the Sidenav Starts-->
        <a onclick="closeNav()" id="sidenavOne"><i class="fa fa-chevron-left fa-2x"></i></a>
        <a onclick="openNav()" id="sidenavTwo"><i class="fa fa-chevron-right fa-2x"></i></a>
        <!-- Open & close the Sidenav Ends-->
      </div>
      <ul class="nav navbar-top-links navbar-right">
      </ul>
      <!-- /.navbar-top-links -->
    </nav>
    <div id="mySidenav" class="navbar-default lab-menu sidebar" role="navigation">
      <div class="sidebar-nav navbar-collapse collapse">
        <ul class="nav" id="side-menu">
          <li class="sidebar-search">
            <i class="fas fa-user"></i>
            <strong>Hi <?php echo isset($_SESSION["fname"]) ? $_SESSION["fname"] : ""; ?>!!</strong>
          </li>
          <li><a class="<?php active_class($page, "dashboard");  ?>" href="index.php?page=dashboard"><i
                class="fas fa-tachometer-alt fa-fw"></i> Dashboard</a></li>
          <!-- <li><a class="<? php // active_class($page, "result-lists");  
                                        ?>" href="index.php?page=result-lists"><i class="fas fa-th-list fa-fw"></i> Result List</a></li> -->
          <li><a class="<?php active_class($page, "add-contact");  ?>" href="index.php?page=add-contact"><i
                class="fa fa-address-book"></i> Add Contact</a></li>
          <li><a class="<?php active_class($page, "contact-list");  ?>" href="index.php?page=contact-list"><i
                class="fas fa-th-list fa-fw"></i> Contact List</a></li>
          <li><a class="<?php active_class($page, "district-list");  ?>" href="index.php?page=district-list"><i
                class="fas fa-th-list fa-fw"></i> District List</a>
          </li>
          <li><a class="<?php active_class($page, "thana-list");  ?>" href="index.php?page=thana-list"><i
                class="fas fa-th-list fa-fw"></i> Thana List</a></li>
          <li><a class="<?php active_class($page, "union-list");  ?>" href="index.php?page=union-list"><i
                class="fas fa-th-list fa-fw"></i> Union List</a></li>

          <!-- <li><a class="<? php // active_class($page, "settings");  
                                        ?>" href="index.php?page=settings"><i class="fas fa-cogs fa-fw"></i> LIS Settings</a></li> -->
          <li><a class="<?php active_class($page, "logout");  ?>" href="logout.php"><i
                class="fas fa-sign-out-alt fa-fw"></i> Logout</a></li>
        </ul>
        <a class="<?php active_class($page, "add-thana");  ?>" href="index.php?page=add-thana.php"></a>
        <a class="<?php active_class($page, "add-union");  ?>" href="index.php?page=add-union.php"></a>
      </div>
      <!-- /.sidebar-collapse -->
    </div>