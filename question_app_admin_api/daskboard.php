<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');

     $usercurrentid=$userinfor["id"];

     $sqlcountusers=mysql_query("select count(*) as countuser from  usersinfo ") or die(mysql_error());
     $countuser=mysql_fetch_array($sqlcountusers)["countuser"];

     $sqlcountposts=mysql_query("select count(*) as countpost from  questions") or die(mysql_error());
     $countpost=mysql_fetch_array($sqlcountposts)["countpost"];

     $sqlcountnewposts=mysql_query("select count(*) as countnewpost from  questions where TIMESTAMPDIFF( HOUR ,question_date_update ,NOW())<=24 ") or die(mysql_error());
     $countnewpost=mysql_fetch_array($sqlcountnewposts)["countnewpost"];

     $sqlnewposts=mysql_query("select q.*,u.email,(select count(*) from question_like_dislike where question_id=q.id) as countlike  from  questions q inner join usersinfo u on q.userid=u.id  where TIMESTAMPDIFF( HOUR ,q.question_date_update ,NOW())<=24 order by question_date_update DESC limit 5");
     $newposts=[];
     $i=0;
     if(mysql_num_rows($sqlnewposts)>0)
     {
         while($row1=mysql_fetch_array($sqlnewposts))
         {
             $newposts[$i]["question"]=$row1["question"];

             $getNumberOfCommentQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answers WHERE questionId=".$row['id'].""));
             $numberOfComments=$getNumberOfCommentQuery[0];
             $newposts[$i]["numberOfComments"]= $numberOfComments;

             $newposts[$i]["countlike"]=$row1["countlike"];

             $getNumberOfViewQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM questions_view WHERE questionId=".$row['id'].""));
             $numberOfViews=$getNumberOfViewQuery[0];
             $newposts[$i]["numberOfViews"]=$numberOfViews;

             $newposts[$i]["email"]=$row1["email"];
             $newposts[$i]["question_date_update"]=$row1["question_date_update"];

             $i++;
         }
     }

     $sqltoppostslike=mysql_query("select q.*,u.email,(select count(*) from question_like_dislike where question_id=q.id) as countlike from  questions q inner join usersinfo u on q.userid=u.id order by countlike DESC limit 5");
     $toppostslike=[];
     $i=0;
     if(mysql_num_rows($sqltoppostslike)>0)
     {
         while($row1=mysql_fetch_array($sqltoppostslike))
         {
             $toppostslike[$i]["question"]=$row1["question"];

             $getNumberOfCommentQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answers WHERE questionId=".$row['id'].""));
             $numberOfComments=$getNumberOfCommentQuery[0];
             $toppostslike[$i]["numberOfComments"]=$numberOfComments;

             $toppostslike[$i]["countlike"]=$row1["countlike"];

             $getNumberOfViewQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM questions_view WHERE questionId=".$row['id'].""));
             $numberOfViews=$getNumberOfViewQuery[0];
             $toppostslike[$i]["numberOfViews"]=$numberOfViews;

             $toppostslike[$i]["email"]=$row1["email"];
             $toppostslike[$i]["question_date_update"]=$row1["question_date_update"];
             $i++;
         }
     }

     $sqltoppostsview=mysql_query("select q.*,u.email,(select count(*) from question_like_dislike where question_id=q.id) as countlike, (select count(*) from questions_view where questionid=q.id) as countview from   questions q inner join usersinfo u on q.userid=u.id order by countview DESC limit 5");
     $toppostsview=[];
     $i=0;
     if(mysql_num_rows($sqltoppostsview)>0)
     {
         while($row1=mysql_fetch_array($sqltoppostsview))
         {
             $toppostsview[$i]["question"]=$row1["question"];

             $getNumberOfCommentQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answers WHERE questionId=".$row['id'].""));
             $numberOfComments=$getNumberOfCommentQuery[0];
             $toppostsview[$i]["numberOfComments"]=$numberOfComments;

             $toppostsview[$i]["countlike"]=$row1["countlike"];

             $toppostsview[$i]["numberOfViews"]=$row1["countview"];

             $toppostsview[$i]["email"]=$row1["email"];
             $toppostsview[$i]["question_date_update"]=$row1["question_date_update"];
             $i++;
         }
     }

     echo json_encode(array("status"=>"1","message"=>"successfully",
     "countuser"=>$countuser,"countpost"=>$countpost,"countnewpost"=>$countnewpost,
     "newposts"=>$newposts,"toppostslike"=>$toppostslike,"toppostsview"=>$toppostsview
     ));

?>