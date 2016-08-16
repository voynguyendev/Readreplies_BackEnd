<?php 
include('database/connection.php');
include('checktoken.php');

$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';

$getQuestion=mysql_query("SELECT * FROM questions,usersinfo WHERE questions.questionId=".$questionId." AND userId=".$userId."");
if(mysql_num_rows($getQuestion)>0)
	{
		while($row=mysql_fetch_array($getQuestion))
			{
				print_r($row);
			}
	}	
?>