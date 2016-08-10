<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');
     $questionid=isset($_REQUEST['questionid'])?$_REQUEST['questionid']:'';
    // echo    $questionid          ;
     mysql_query('SET CHARACTER SET utf8');
     $sqlquestionImages=mysql_query("select * from  imagesquestion where questionid='$questionid'") or die(mysql_error());
     $questionimages=[];
     $i=0;
     if(mysql_num_rows($sqlquestionImages)>0 && $questionid!="")
     {
         while($row1=mysql_fetch_array($sqlquestionImages))
         {
           $questionimages[$i]["imagequestionid"]=   $row1["imagequestionid"]   ;
           $questionimages[$i]["imagethumb"]=   $row1["imagethumb"]   ;
           $questionimages[$i]["questionid"]=   $row1["questionid"]   ;
           $questionimages[$i]["image"]=   $row1["image"] ;

           $i++;
         }
     }
     else
     {
              echo json_encode(array("status"=>"0","message"=>"successfully",
             "questionimages"=>$questionimages
             ));
             return;
     }
     echo json_encode(array("status"=>"1","message"=>"successfully",
     "questionimages"=>$questionimages
     ));

?>