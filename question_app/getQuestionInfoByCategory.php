<?php 
include('database/connection.php');

include('checktoken.php');

$categoryId=isset($_REQUEST['categoryId'])?$_REQUEST['categoryId']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';

$getQuestion=mysql_query("SELECT questions . *,favoriteQuestion.status as favourite,usersinfo.id as userid,usersinfo.name as name,usersinfo.email as email FROM  questions
LEFT JOIN favoriteQuestion ON questions.userId=favoriteQuestion.userID AND questions.id=favoriteQuestion.questionId
LEFT JOIN usersinfo ON questions.userId=usersinfo.id
WHERE questions.userId=".$userId." AND   questions.categoryId=".$categoryId."") or die(mysql_error());
if(mysql_num_rows($getQuestion)>0)
	{
		$questionnumber=0;
		while($row=mysql_fetch_array($getQuestion))
			{
				$userinfo['userid']=$row['userid'];
				$userinfo['name']=$row['name'];
				$userinfo['email']=$row['email'];
				$questionInfo[$questionnumber]['questionId']=$row['id'];
				$questionInfo[$questionnumber]['question']=$row['question'];
				$questionInfo[$questionnumber]['categoryId']=$row['categoryId'];
				$questionInfo[$questionnumber]['subjectId']=$row['subjectId'];
				$questionInfo[$questionnumber]['question_date']=$row['question_date'];
				$questionInfo[$questionnumber]['attachment']=$row['attachment'];
				if($row['favourite']==NULL)
					{
						$questionInfo[$questionnumber]['favourite']='0';
					}
				else	
					{
						$questionInfo[$questionnumber]['favourite']=$row['favourite'];
					}
				$getNumberOfAnswersQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answers WHERE questionId=".$row['id'].""));
				$numberOfAnswers=$getNumberOfAnswersQuery[0];
				$questionInfo[$questionnumber]['totalAnswers']=$numberOfAnswers;
				$questionnumber++;
				
			}
		echo json_encode(array("success"=>"1","message"=>$questionnumber." questions founded.","userinfo"=>$userinfo,"questions"=>$questionInfo));
		exit;
	}	
else
	{
		echo json_encode(array("success"=>"0","message"=>"no question"));
		exit;
	}
	
	
?>
?>