
<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');

     $usercurrentid=$userinfor["id"];
       
     echo json_encode(array("status"=>"1","message"=>"successfully",
     "userinfor"=>$userinfor
     ));

?>