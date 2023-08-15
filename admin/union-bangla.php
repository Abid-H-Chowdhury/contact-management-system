<?php
$sn = 0;
$sn1 = 0;
$userID = $_SESSION['user_id'];
global $date, $time;

require_once("pagination.php");
$currentPage = "index.php?page=union-bangla";




     //start

        $statement = " upazilas_noneed t1 ";
        $statement .= " ORDER BY t1.name ASC ";
        $result_listes = QB::query("SELECT t1.*  FROM {$statement}")->get();

          foreach ($result_listes as $test) {

        $bn=$test->bn_name;
         $n=$test->name;

 


    $data1 = array(


        
        'bname' => $bn,
       

    );

    $update = QB::table('unions')->where('name', 'LIKE', "%$n%")->update($data1);
}

        


?>


