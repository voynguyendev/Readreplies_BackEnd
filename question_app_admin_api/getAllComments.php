<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');

     $namemenu="MANAGER_COMMENTS";
     include('authorization.php');
     mysql_query('SET CHARACTER SET utf8');
     $questionid=isset($_REQUEST['questionid'])?$_REQUEST['questionid']:'';
     $pagenumber=isset($_REQUEST['pagenumber'])?$_REQUEST['pagenumber']:'';
     $pagesize=isset($_REQUEST['pagesize'])?$_REQUEST['pagesize']:'';
     $start = $pagenumber;
     $textsearch = isset($_REQUEST['textsearch'])?$_REQUEST['textsearch']:'';
     $sortfield = isset($_REQUEST['sortfield'])?$_REQUEST['sortfield']:'';
     $sortorder = isset($_REQUEST['sortorder'])?$_REQUEST['sortorder']:'';
     $useridansweraccept = isset($_REQUEST['useridansweraccept'])?$_REQUEST['useridansweraccept']:'';
     $useridgood = isset($_REQUEST['useridgood'])?$_REQUEST['useridgood']:'';
     $useridcomment = isset($_REQUEST['useridcomment'])?$_REQUEST['useridcomment']:'';

     $query="select a.*,u.email from  answers a inner join usersinfo u on a.userId=u.id where a.entity=0 and ('$questionid'='' or a.questionId= '$questionid')";
     if($textsearch!="")
     {
         $query=$query." and (a.answer like '%".$textsearch."%' or u.email like '%".$textsearch."%')";
     }
     if($useridgood!="")
     {
         $query=$query." and (a.id in (select answer_id from answer_like_dislike where  status=1 ) and a.userId= '$useridgood') " ;
     }
     if($useridansweraccept!="")
     {
         $query=$query." and (a.status='accepted' and a.userId= '$useridansweraccept') " ;
     }
      if($useridcomment!="")
     {
         $query=$query." and a.userId= '$useridcomment' " ;
     }

     $totalrow=mysql_num_rows(mysql_query($query))   ;
     $sqlanswers=mysql_query($query." order by a.id DESC LIMIT $start,$pagesize") or die(mysql_error());
     $answers=[];
     $i=0;
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
           $answers[$i]["questionid"]=  $row1["questionId"].""   ;
           $getNumberOfViewQuery=mysql_fetch_row(mysql_query("SELECT cou
     if(mysql_num_rows($sqlanswers)>0)                                  nt(*) FROM flaganswerquestion WHERE entity=1 and entityid='".$row1['id']."'"));
           $numberOfViews=$getNumberOfViewQuery[0];
           $answers[$i]["numberOfFlag"]=$numberOfViews;
           $i++;
         }
     }
       $data[] = array(
       'TotalRows' => $totalrow,
       'Rows' => $answers
    );
     echo json_encode(array("status"=>"1","message"=>"successfully",
     "data"=>$data
     ));

?>