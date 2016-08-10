<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');
     $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';

     mysql_query('SET CHARACTER SET utf8');
     $sqluserImages=mysql_query("select * from  imagesuser where 	userid='$userid'") or die(mysql_error());
     $userimages=[];
     $i=0;
     if(mysql_num_rows($sqluserImages)>0 && $userid!="")
     {
         while($row1=mysql_fetch_array($sqluserImages))
         {
           $userimages[$i]["id"]=   $row1["id"]   ;
           $userimages[$i]["thumb"]=   $row1["thumb"]   ;
           $userimages[$i]["attachment"]=   $row1["attachment"]   ;
           $userimages[$i]["userid"]=   $row1["userid"] ;
           $i++;
         }
     }
     else
     {
              echo json_encode(array("status"=>"0","message"=>"successfully",
             "userimages"=>$userimages
             ));
             return;
     }
     echo json_encode(array("status"=>"1","message"=>"successfully",
     "userimages"=>$userimages
     ));

?>