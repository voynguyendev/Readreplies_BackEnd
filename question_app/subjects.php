<?php
include('database/connection.php');
include('checktoken.php');

$subjectId=isset($_REQUEST['subjectId'])?$_REQUEST['subjectId']:'';
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';

if(!isset($_REQUEST['subject_name']))
	{
		$subject_name='';
	}
else	
	{
		$subject_name=trim($_REQUEST['subject_name']);
	}
	
///// CHECK WHETHER THE ACTION IS ADD OR EDIT OR DELETE  SUBJECT ///////////////
	
if($subjectId!='')
	{
		if($action=='delete')
			{
				$updateSubject=mysql_query("DELETE FROM subjects WHERE id=".$subjectId."");
				echo json_encode(array("status"=>"1","message"=>"Subject successfully deleted."));
				exit;	
			}
		else
			{
				if($subject_name != "")
					{
						$updateSubject=mysql_query("UPDATE subjects set subject_name='".$subject_name."' WHERE id=".$subjectId."");
						echo json_encode(array("status"=>"1","message"=>"Subject successfully updated."));
						exit;	
					}
				else	
					{
						echo json_encode(array("status"=>"1","message"=>"Subject could not be updated."));
						exit;	
					}
			}
	}
else
	{
		$checkSubject=mysql_query("SELECT * FROM subjects WHERE subject_name='".$subject_name."'");
		if(mysql_num_rows($checkSubject)>0)
			{
				echo json_encode(array("status"=>"0","message"=>"Subject already exists."));
				exit;
			}
		else
			{
				if($subject_name != "")
					{
						$addSubject=mysql_query("INSERT INTO subjects(subject_name) VALUES('".$subject_name."')");
						echo json_encode(array("status"=>"1","message"=>"Subject successfully added."));
						exit;
					}
				else	
					{
						echo json_encode(array("status"=>"1","message"=>"Subject could not be updated."));
						exit;	
					}
			}
	}