<?php 

include('database/connection.php');

 $message_id=isset($_REQUEST['message_id'])?$_REQUEST['message_id']:'';
 $status=isset($_REQUEST['status'])?$_REQUEST['status']:0;

if($message_id!='' && $status!=0)
	{
		$read_message=mysql_query("UPDATE messaging SET status=".$status." WHERE message_id=".$message_id."") ;
		if($read_message)
			{
				echo json_encode(array("status"=>"1","message"=>"message has been marked as read"));
				exit;
			}
		else
			{
				echo json_encode(array("status"=>"0","message"=>"message could not be marked as read"));
				exit;
			}
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"Error"));
		exit;
	}
	
?>	