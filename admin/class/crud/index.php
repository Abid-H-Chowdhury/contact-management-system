<?php
    include('xcrud.php');
    $xcrud = Xcrud::get_instance();
    $xcrud->table('patients');
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Some page title</title>
</head>
 
<body>
 
<?php
    echo $xcrud->render();
?>
 
</body>
</html>