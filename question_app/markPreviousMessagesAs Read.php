<?php 
include('database/connection.php');
include('checktoken.php');
$last_message_id=isset($_REQUEST['message_id'])?$_REQUEST['message_id']:'';
$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';

if($last_message_id!='' && $user_id!='')
	{
	
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"Error"));
		exit;
	}