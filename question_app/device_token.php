<?php
include('database/connection.php');
include('checktoken.php');

$user_id=isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : "";	
$device_token = isset($_REQUEST['device_token']) ? $_REQUEST['device_token'] : "";	
$type= isset($_REQUEST['type']) ? $_REQUEST['type'] : "";	



 //$query = "SELECT id FROM  device_token_tb WHERE device_token = '$device_token' AND user_id = '$user_id'";
  $query = "SELECT id FROM  device_token_tb WHERE device_token = '$device_token'";
	$record = mysql_query($query) or die(mysql_error());
	if(mysql_num_rows($record) > 0 )
	{
		while($row=mysql_fetch_assoc($record))
		{
			$id = $row['id'];
		}
	 	$update_sql = "UPDATE device_token_tb SET user_id = ".$user_id." WHERE id = ".$id."";
		$update_record = mysql_query($update_sql) or die(mysql_error());
		if($update_record) {
		echo json_encode(array("success"=>"0","msg"=>"updated successfully"));
		//echo json_encode(array("success"=>"0","msg"=>"already exists")); 
		exit;
		}
	}
       else
	{
		$n_sql = "INSERT INTO device_token_tb(device_token,user_id,type) VALUES('$device_token','$user_id','$type')";
		$round = mysql_query($n_sql) or die(mysql_error());
		 echo json_encode(array("success"=>"1","msg"=>"You have successfully inserted")); 
		 exit;
	}
        

?>