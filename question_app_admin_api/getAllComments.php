<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');

     $namemenu="MANAGER_COMMENTS";
     include('authorization.php');
     mysql_query('SET CHARACTER SET utf8');
     $sqlanswers=mysql_query("select a.*,u.email from  answers a inner join usersinfo u on a.userId=u.id where ('$questionid'='' or a.questionId= '$questionid')  order by id DESC ") or die(mysql_error());
     $answers=[];
     $i=0;
     if(mysql_num_rows($sqlanswers)>0)
     {
         while($row1=mysql_fetch_array($sqlanswers))
         {
           $answers[$i]["id"]=   $row1["id"]   ;
           $answers[$i]["answer"]=   $row1["answer"]   ;
           $answers[$i]["answer_date"]=   $row1["answer_date"]   ;
           $answers[$i]["attachment"]=   $row1["attachment"]   ;
           $answers[$i]["thumb"]=   $row1["thumb"] ;
           $answers[$i]["email"]=  $row1["email"]   ;
           $answers[$i]["isblock"]=  $row1["isblock"].""   ;
           $answers[$i]["userId"]=  $row1["userId"].""   ;
           $getNumberOfViewQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM flaganswerquestion WHERE entity=1 and entityid='".$row1['id']."'"));
           $numberOfViews=$getNumberOfViewQuery[0];
           $answers[$i]["numberOfFlag"]=$numberOfViews;
           $i++;
         }
     }

     echo json_encode(array("status"=>"1","message"=>"successfully",
     "answers"=>$answers
     ));

?>