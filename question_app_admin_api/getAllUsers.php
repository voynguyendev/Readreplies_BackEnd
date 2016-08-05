<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
    // include('autherncation.php');

     $usercurrentid=$userinfor["id"];

     $sqluserinfors=mysql_query("select * from  usersinfo order by id DESC ") or die(mysql_error());
     $userinfors=[];
     $i=0;
       if(mysql_num_rows($sqluserinfors)>0)
     {
         while($row1=mysql_fetch_array($sqluserinfors))
         {
           $userinfors[$i]["id"]=   $row1["id"]   ;
           $userinfors[$i]["email"]=   $row1["email"]   ;
           $userinfors[$i]["name"]=   $row1["name"]   ;
           $userinfors[$i]["lname"]=   $row1["lname"]   ;
           $i++;
         }
     }

     echo json_encode(array("status"=>"1","message"=>"successfully",
     "userinfors"=>$userinfors
     ));

?>