<?php 
include('database/connection.php');

$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';

$getQuestion=mysql_query("SELECT questions.*,usersinfo.id as userid,usersinfo.name as name,usersinfo.email as email,usersinfo.thumb as userthumb  FROM usersinfo,questions  WHERE  questions.id=".$questionId." AND questions.userId=usersinfo.id") or die(mysql_error());
if(mysql_num_rows($getQuestion)>0)
	{
		$questionnumber=0;
		while($row=mysql_fetch_array($getQuestion))
			{
				$userinfo['userid']=$row['userid'];
				$userinfo['name']=$row['name'];
				$userinfo['email']=$row['email'];
				$questionInfo['questionId']=$row['id'];
				$questionInfo['question']=$row['question'];
				$questionInfo['categoryId']=$row['categoryId'];
				$questionInfo['subjectId']=$row['subjectId'];
				$questionInfo['question_date']=$row['question_date'];
				$questionInfo['attachment']=$row['attachment'];
				$questionInfo['name']=$row['name'];
				$questionInfo['userthumb']=$row['userthumb'];
				
				$checkSavedOrNot=mysql_query("SELECT status FROM savequestions WHERE userId=".$userId." AND questionID=".$questionId."");
				if(mysql_num_rows($checkSavedOrNot)>0)
					{
						$getStatus=mysql_fetch_assoc($checkSavedOrNot);
						$questionInfo['questionSaved']=$getStatus['status'];
					}
				else	
					{
						$questionInfo['questionSaved']="0";
					}
				
				$getNumberOfAnswersQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answers WHERE questionId=".$row['id'].""));
				$getFavouriteStatus=mysql_fetch_row(mysql_query("SELECT status FROM favoriteQuestion WHERE questionId=".$row['id']." AND userID=".$userId.""));
				if($getFavouriteStatus[0])
					$favouriteQuestionStatus=$getFavouriteStatus[0];
				else
					$favouriteQuestionStatus="0";
				$numberOfAnswers=$getNumberOfAnswersQuery[0];
				$questionInfo['totalAnswers']=$numberOfAnswers;
				$questionInfo['favouriteStatus']=$favouriteQuestionStatus;
				$questionnumber++;
				
			}
		echo json_encode(array("success"=>"1","message"=>$questionnumber." questions founded.","userinfo"=>$userinfo,"questions"=>$questionInfo));
		exit;
	}	
else
	{
		echo json_encode(array("success"=>"0","message"=>"No question found"));
		exit;
	}
	
	
?>