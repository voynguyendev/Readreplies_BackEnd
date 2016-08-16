<?php
include('database/connection.php');
include('checktoken.php');
define('UPLOAD_DIR', '../question_app/question_images/');
$answer=isset($_REQUEST['answer'])?$_REQUEST['answer']:'';
$answerId=isset($_REQUEST['answerId'])?$_REQUEST['answerId']:'';
$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';
$answer_date=isset($_REQUEST['date'])?$_REQUEST['date']:'';
$attachment=isset($_REQUEST['attachment'])?$_REQUEST['attachment']:'';
$status=isset($_REQUEST['status'])?$_REQUEST['status']:'';
 
if($answerId != "" && $answer != "" && $questionId != "" && $userId != "" && $answer_date != "")
	{
		$checkPoster=mysql_query("SELECT userId from answers  WHERE id=".$answerId."");
			if(mysql_num_rows($checkPoster)>0)
				{
					
					if($attachment!='')
						{
							$attachment = str_replace('data:image/png;base64,', '', $attachment);
							$attachment = str_replace(' ', '+', $attachment);
							$data = base64_decode($attachment);
							$name = time()."_image.png";
													
							$success = file_put_contents($file, $data);
							$file = UPLOAD_DIR.$name;
							$success = file_put_contents($file, $data);
							$success ? $file : 'Unable to save the file.';
							$serverName=$_SERVER['SERVER_NAME']."/question_app/answer_images/";
							$filename=$serverName.$name;
						}
					else
						{
							$filename='';
						}	
					$updateanswer=mysql_query("UPDATE answers set answer='".mysql_real_escape_string($answer)."',questionId=".$questionId.",userId=".$userId.",answer_date='".$answer_date."',attachment='".$filename."',status='".$status."' WHERE id=".$answerId."");
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