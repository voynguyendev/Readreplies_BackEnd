<?php

include('database/connection.php');

include('checktoken.php');

$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';

$getLikeDislikeDetail=mysql_query("SELECT * FROM like_dislike WHERE user_id = '".$user_id."'") or die(mysql_error());

if(mysql_num_rows($getLikeDislikeDetail) > 0)
{
	$i=0;
	while($row=mysql_fetch_assoc($getLikeDislikeDetail))
	{
		//$data[$i]=$row;
		$data[$i]['user_id']=$row['user_id'];
		$data[$i]['question_id']=$row['question_id'];
		
		if($row['question_id']!=0){
			$get_q_count=mysql_fetch_assoc(mysql_query("select likes,dislikes from questions where id='".$row['question_id']."'")) or die(mysql_error());
			$data[$i]['question_likes_count']=$get_q_count['likes'];
			$data[$i]['question_dislikes_count']=$get_q_count['dislikes'];
			//echo "<pre>";print_r($get_q_count); 
		}
		
		$data[$i]['answer_id']=$row['answer_id'];
		if($row['answer_id']!=0){
			$get_a_count=mysql_fetch_assoc(mysql_query("select likes,dislikes from answers where id='".$row['answer_id']."'")) or die(mysql_error());
			$data[$i]['answer_likes_count']=$get_a_count['likes'];
			$data[$i]['answer_dislikes_count']=$get_a_count['dislikes'];
			//echo "<pre>";print_r($get_a_count);exit;
		}
		$data[$i]['status']=$row['status'];
		$i++;
	}
	echo json_encode(array("data"=>$data,"success"=>1));
	exit;
}
else
{
	$data=array();
	echo json_encode(array("data"=>$data,"success"=>0));
	exit;
}

?>