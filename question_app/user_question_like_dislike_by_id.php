<?php

include('database/connection.php');
include('checktoken.php');

$question_id=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$like_dislike_status=isset($_REQUEST['like_dislike_status'])?$_REQUEST['like_dislike_status']:'';
$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';

if($user_id!='' && $like_dislike_status!='' && $question_id!='')
{
	$questionlikedislikeinsert=mysql_query("INSERT INTO question_like_dislike(user_id,questionId,like_dislike_status) VALUES('$user_id','$question_id','$like_dislike_status')") or die(mysql_error());	
	
	if($questionlikedislikeinsert)
	{
		echo json_encode(array("message"=>"Like dislike status updated successfully","success"=>1));	
		exit;
	}
}
else
{
	echo json_encode(array("message"=>"Incorrect parameters","success"=>0));	
	exit;
}

?>