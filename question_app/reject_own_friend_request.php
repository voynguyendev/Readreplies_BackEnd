<?php

include('database/connection.php');
include('checktoken.php');
if(isset($_REQUEST['sender_id']) && isset($_REQUEST['receiver_id']))
{
	$sender_id = $_REQUEST['sender_id'];
	$receiver_id = $_REQUEST['receiver_id'];

	$sql = mysql_query("DELETE FROM friendRequests WHERE (status = 0 or status = 1)  AND (sender_id = '$sender_id' AND receiver_id = '$receiver_id') OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id')") or die(mysql_error());
	
	if(mysql_affected_rows() > 0)
	{
		echo json_encode(array("msg"=>"Friend request deleted","success"=>1));
		exit;	
	}
	else
	{
		echo json_encode(array("msg"=>"Friend request deletion failed","success"=>0));
		exit;
	}

}
else
{
	echo json_encode(array("msg"=>"Insufficient data","success"=>0));
	exit;	
}
?>