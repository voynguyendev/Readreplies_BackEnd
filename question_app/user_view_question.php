<?php

include('database/connection.php');

include('checktoken.php');

$user_id=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
$questionid=isset($_REQUEST['questionid'])?$_REQUEST['questionid']:'';

if($user_id=="" ||$questionid=='')      {
    echo json_encode(array("success"=>0));
	exit;

}

$getviewquestionDetail=mysql_query("SELECT * FROM questions_view WHERE userid = '".$user_id."'  and questionid='$questionid'") or die(mysql_error());

if(mysql_num_rows($getviewquestionDetail) <= 0)
{
    mysql_query("insert into questions_view (userid,questionid) values('$user_id','$questionid')") or die(mysql_error());
   	echo json_encode(array("success"=>1));
	exit;
}


?>