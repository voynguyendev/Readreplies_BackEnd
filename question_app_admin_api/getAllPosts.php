<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


     include('database/connection.php');
     include('autherncation.php');
     $namemenu="MANAGER_POSTS";
     include('authorization.php');

     $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
     $pagenumber=isset($_REQUEST['pagenumber'])?$_REQUEST['pagenumber']:'';
     $pagesize=isset($_REQUEST['pagesize'])?$_REQUEST['pagesize']:'';
     $start = $pagenumber;
     $textsearch = isset($_REQUEST['textsearch'])?$_REQUEST['textsearch']:'';
     $sortfield = isset($_REQUEST['sortfield'])?$_REQUEST['sortfield']:'';
     $sortorder = isset($_REQUEST['sortorder'])?$_REQUEST['sortorder']:'';
     $useridview = isset($_REQUEST['useridview'])?$_REQUEST['useridview']:'';
     $useridgood = isset($_REQUEST['useridgood'])?$_REQUEST['useridgood']:'';

     mysql_query('SET CHARACTER SET utf8');


     $query = "select q.*,u.email from  questions q inner join usersinfo u on q.userId=u.id where q.entity=0 and ('$userid'='' or q.userId= '$userid')  ";
     if($textsearch!="")
     {
         $query=$query." and (q.question like '%".$textsearch."%' or u.email like '%".$textsearch."%')" ;
     }

     if($useridview!="")
     {
         $query=$query." and (q.id in (select questionid from questions_view) and q.userId= '$useridview') " ;
     }
     if($useridgood!="")
     {
         $query=$query." and (q.id in (select question_id from question_like_dislike where status=1) and q.userId= '$useridgood') " ;
     }
     $totalrow=mysql_num_rows(mysql_query($query))   ;

    /* if($sortfield!="")
     {
            if($sortfield=="email")
            {
                  if($sortorder=="asc")
                        {
                               $query=$query." order by u.email";

                        }
                        else
                               $query=$query." order by u.email desc";

            }
             else if($sortfield=="question")
             {}
     }
       */


     $sqlquestions=mysql_query($query." order by q.id DESC LIMIT $start,$pagesize") or die(mysql_error());
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

           $getNumberOfImagesQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM imagesquestion WHERE questionid='".$row1['id']."'"));
           $numberOfImages=$getNumberOfImagesQuery[0];
           $questions[$i]["numberOfImages"]=$numberOfImages;

           $getNumberOfCommentQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answers WHERE questionid='".$row1['id']."'"));
           $numberOfComments=$getNumberOfCommentQuery[0];
           $questions[$i]["numberOfComments"]=$numberOfComments;
           $i++;
         }
     }
         $data[] = array(
       'TotalRows' => $totalrow,
       'Rows' => $questions
    );
     echo json_encode(array("status"=>"1","message"=>"successfully",
     "data"=>$data
     ));

?>