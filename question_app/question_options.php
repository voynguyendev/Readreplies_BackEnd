<?php
include('database/connection.php');

include('checktoken.php');

$option=isset($_REQUEST['option'])?$_REQUEST['option']:'';
$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';

$checkQuestionCreater=mysql_query("SELECT * FROM questions WHERE userId=".$userId."");

if(mysql_num_rows($checkQuestionCreater)>0)
	{
		if($option != "" && $questionId != ""  && $userId != "")
			{
				$insertAnswer=mysql_query("INSERT INTO question_options(answer,questionID) VALUES('".$option."',".$questionId.")");
				echo json_encode(array("status"=>"1","message"=>"Option has been successfully saved."));
				exit;
			}
		else	
			{
				echo json_encode(array("status"=>"0","message"=>"Error"));
				exit;
			}
	}
else	
	{
		echo json_encode(array("status"=>"0","message"=>"You can not add the options to this question."));
		exit;
	}