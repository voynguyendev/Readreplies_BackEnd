<?php 
include('database/connection.php');
include('checktoken.php');
$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$rating=isset($_REQUEST['rating'])?$_REQUEST['rating']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';

$checkPreviousRating=mysql_query("SELECT * FROM questions_rating WHERE questionId=".$questionId." AND userID=".$userId."");
if(mysql_num_rows($checkPreviousRating)>0)
	{
		$updateRating=mysql_query("UPDATE questions_rating SET rating=".$rating."  WHERE questionId=".$questionId." AND userID=".$userId."");
		if($updateRating)
			{
				echo json_encode(array("status"=>"1","message"=>"Your rating has been successfully updated to this question."));
				exit;

			}
		else	
			{
				echo json_encode(array("status"=>"0","message"=>"Your rating could not be updated to this question."));
				exit;

			}
	}
else
	{
		$addRating=mysql_query("INSERT INTO questions_rating(rating,questionId,userId) VALUES('".$rating."','".$questionId."','".$userId."')") ;
		if($addRating)
			{
				echo json_encode(array("status"=>"1","message"=>"You have successfully rated to this question."));
				exit;

			}
		else	
			{
				echo json_encode(array("status"=>"0","message"=>"Your rating could not be added."));
				exit;

			}	
	}

?>