<?php
include('database/connection.php');
include('checktoken.php');

$questionId=isset($_REQUEST['question_id'])?$_REQUEST['question_id']:'';
$userId=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';
$status=isset($_REQUEST['status'])?$_REQUEST['status']:'';

if($questionId!='' && $userId!='' && $status!='')
	{
		$checkPreviousEntryQuery="SELECT * FROM savequestions WHERE questionId=".$questionId." AND userId=".$userId."";
		$checkPreviousEntryResult=mysql_query($checkPreviousEntryQuery);
		if(mysql_num_rows($checkPreviousEntryResult)>0)
			{
				$saveQuestionUpdateQuery="UPDATE savequestions SET status=".$status." WHERE questionId=".$questionId." AND userId=".$userId."";
				$saveQuestionUpdateResult=mysql_query($saveQuestionUpdateQuery);
				if($saveQuestionUpdateResult)
					{
						echo json_encode(array("success"=>"1","message"=>"Successful"));	
						exit;
					}
				else
					{
						echo json_encode(array("success"=>"0","message"=>"Error"));	
						exit;
					}
			}
		else	
			{
				$saveQuestionInsertQuery="INSERT INTO savequestions(questionId,userId,status) VALUES(".$questionId.",".$userId.",".$status.")";
				$saveQuestionInsertResult=mysql_query($saveQuestionInsertQuery);
				if($saveQuestionInsertResult)
					{
						echo json_encode(array("success"=>"1","message"=>"Successful"));	
						exit;
					}
				else
					{
						echo json_encode(array("success"=>"0","message"=>"Error"));	
						exit;
					}
			}
	}
else
	{
		echo json_encode(array("success"=>"0","message"=>"Please provide correct information."));	
	}
