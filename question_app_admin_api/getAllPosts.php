<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


     include('database/connection.php');
     include('autherncation.php');
     $namemenu="MANAGER_POSTS";
     include('authorization.php');

     $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
     mysql_query('SET CHARACTER SET utf8');
     $sqlquestions=mysql_query("select q.*,u.email from  questions q inner join usersinfo u on q.userId=u.id where ('$userid'='' or q.userId= '$userid')  order by id DESC ") or die(mysql_error());
     $questions=[];
     $i=0;
     if(mysql_num_rows($sqlquestions)>0)
     {
         while($row1=mysql_fetch_array($sqlquestions))
         {
           $questions[$i]["id"]=   $row1["id"]   ;
           $questions[$i]["question"]=   $row1["question"]   ;
           $questions[$i]["question_date"]=   $row1["question_date"]   ;
           $questions[$i]["question_date_update"]=   $row1["question_date_update"]   ;
           $questions[$i]["email"]=  $row1["email"]   ;
           $questions[$i]["isblock"]=  $row1["isblock"].""   ;
           $questions[$i]["userId"]=  $row1["userId"].""   ;
           $getNumberOfViewQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM flaganswerquestion WHERE entity=0 and entityid='".$row1['id']."'"));
           $numberOfViews=$getNumberOfViewQuery[0];
           $questions[$i]["numberOfFlag"]=$numberOfViews;
           $i++;
         }
     }

     echo json_encode(array("status"=>"1","message"=>"successfully",
     "questions"=>$questions
     ));

?>