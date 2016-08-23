<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     //include('autherncation.php');
     $namemenu="MANAGER_USERS";
     //include('authorization.php');
     //$usercurrentid=$userinfor["id"];
     $pagenumber=isset($_REQUEST['pagenumber'])?$_REQUEST['pagenumber']:"";
     $pagesize=isset($_REQUEST['pagesize'])?$_REQUEST['pagesize']:"";
     $start = $pagenumber;
     $textsearch = isset($_REQUEST['textsearch'])?$_REQUEST['textsearch']:'';
     $sortfield = isset($_REQUEST['sortfield'])?$_REQUEST['sortfield']:'';
     $sortorder = isset($_REQUEST['sortorder'])?$_REQUEST['sortorder']:'';
     $friendid = isset($_REQUEST['friendid'])?$_REQUEST['friendid']:'';
     $followerid = isset($_REQUEST['followerid'])?$_REQUEST['followerid']:'';
     $query="select * from  usersinfo where 1=1 ";

     if($textsearch!="")
     {
         $query=$query." and (email like '%".$textsearch."%' or name like '%".$textsearch."%' or lname like '%".$textsearch."%')" ;
     }

     if($followerid!="")
     {
         $query=$query." and id in (select follower_id from followers where user_id='$followerid')" ;
     }

     if($friendid!="")
     {
         $query=$query." and (id in (select receiver_id from friendRequests where sender_id='$friendid' and status=1) or id in (select sender_id  from friendRequests where receiver_id='$friendid' and status=1) ) " ;
     }

     $totalrow=mysql_num_rows(mysql_query($query))   ;
     $sqluserinfors=mysql_query($query." order by id DESC LIMIT $start,$pagesize") or die(mysql_error());
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
           $userinfors[$i]["disabled"]=  $row1["disabled"]   ;

           $getNumberOfImagesQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM questions WHERE entity=1 and userid='".$row1['id']."'"));
           $numberOfImages=$getNumberOfImagesQuery[0];
           $userinfors[$i]["numberOfImage"]=$numberOfImages;

           $getNumberOfQuestionQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM questions WHERE entity=0 and userId='".$row1['id']."'"));
           $numberOfquestions=$getNumberOfQuestionQuery[0];
           $userinfors[$i]["numberOfquestion"]=$numberOfquestions;


           $i++;
         }
     }
     $data[] = array(
       'TotalRows' => $totalrow,
       'Rows' => $userinfors
    );
     echo json_encode(array("status"=>"1","message"=>"successfully",
     "data"=>$data
     ));

?>