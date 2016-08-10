<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');
     $namemenu=" MANAGER_USERS_SYSTEM";
     include('authorization.php');

     $sqluserinsystem=mysql_query("select * from  tbl_admin where isadmin=0 order by userId DESC ") or die(mysql_error());
     $usersystems=[];
     $i=0;
     if(mysql_num_rows($sqluserinsystem)>0)
     {
         while($row1=mysql_fetch_array($sqluserinsystem))
         {
           $usersystems[$i]["userId"]=   $row1["userId"]   ;
           $usersystems[$i]["username"]=   $row1["username"]   ;
           $i++;
         }
     }

     echo json_encode(array("status"=>"1","message"=>"successfully",
     "usersystems"=>$usersystems
     ));

?>