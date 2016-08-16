<?php
include('database/connection.php');
include('checktoken.php');

$answer=isset($_REQUEST['answer'])?$_REQUEST['answer']:'';
$answerId=isset($_REQUEST['answerId'])?$_REQUEST['answerId']:'';
$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';
$answer_date=isset($_REQUEST['date'])?$_REQUEST['date']:'';
$attachment=isset($_REQUEST['attachment'])?$_REQUEST['attachment']:'';

///// CHECK WHETHER THE ACTION IS ADD  answer ///////////////
	
if($answerId!='')
	{
		if($action=='delete')
			{
				$updateanswer=mysql_query("DELETE FROM answers  WHERE id=".$answerId."");
				echo json_encode(array("status"=>"1","message"=>"answer successfully deleted."));
				exit;
			}
		else	
			{
				if($answer != "" && $categoryId != "" && $subjectId != "" && $userId != "" && $answer_date != "")
					{
						$checkPoster=mysql_query("SELECT userId from answers  WHERE id=".$answerId."");
						if(mysql_num_rows($checkPoster)>0)
							{
								$updateanswer=mysql_query("UPDATE answers set answer='".$answer."',categoryId=".$categoryId.",subjectId=".$subjectId.",userId=".$userId.",answer_date='".$answer_date."',attachment='".$attachment."',status='pending' WHERE id=".$answerId."");
								echo json_encode(array("status"=>"1","message"=>"answer successfully updated."));
								exit;
							}
						else	
							{
								echo json_encode(array("status"=>"0","message"=>"You can not update the answer."));
								exit;
							}
					}
				else	
					{
						echo json_encode(array("status"=>"0","message"=>"Error."));
						exit;
					}
			}
	}
else
	{
		if($answer != "" && $questionId != ""  && $userId != "" && $answer_date != "")
			{
				$addanswer=mysql_query("INSERT INTO answers(answer,questionId,userId,answer_date,attachment,status) VALUES('".$answer."',".$questionId.",".$userId.",'".$answer_date."','".$attachment."','pending')");
				echo json_encode(array("status"=>"1","message"=>"Your answer is recorded."));
				exit;
			}
		else	
			{
				echo json_encode(array("status"=>"0","message"=>"Error."));
				exit;
			}
			
	}