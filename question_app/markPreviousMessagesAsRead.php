<?php 
include('database/connection.php');
include('checktoken.php');
$last_message_id=isset($_REQUEST['message_id'])?$_REQUEST['message_id']:'';
$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';

if($last_message_id!='' && $user_id!='')
	{
		$markMessagesAsRead=mysql_query("UPDATE messaging SET status=1 WHERE message_id<=".$last_message_id." AND receiver_id=".$user_id."");
			if($markMessagesAsRead)
				{
					echo json_encode(array("status"=>"1","message"=>"messages marked as read."));
					exit;
				}
			else	
				{
					echo json_encode(array("status"=>"0","message"=>"messages could not be marked as read"));
					exit;
				}
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"Error"));
		exit;
	}