<?php
include('database/connection.php');
include('checktoken.php');
$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';

if($user_id!='')
	{
		
		$query=mysql_query("SELECT friendRequests.id as request_id,friendRequests.*,usersinfo.* FROM friendRequests LEFT JOIN usersinfo ON friendRequests.sender_id=usersinfo.id WHERE receiver_id=".$user_id." AND status=0");
		if(mysql_num_rows($query)>0)
			{
				$data=array();
				$i=0;
				while($row=mysql_fetch_array($query))
					{
						$data[$i]['request_id']=$row['request_id'];
						$data[$i]['sender_id']=$row['sender_id'];
						$data[$i]['name']=$row['name'];
						$data[$i]['email']=$row['email'];
						$data[$i]['profile_pic']=$row['profile_pic'];
						$data[$i]['thumb']=$row['thumb'];
						$data[$i++]['mobile']=$row['mobile'];
                        $data[$i++]['lname']=$row['lname'];
                        
					}
				echo json_encode(array("status"=>"1","message"=>"".mysql_num_rows($query)." friend request found.","data"=>$data));
				exit;
			}
		else
			{
				echo json_encode(array("status"=>"0","message"=>"No friend request found."));
				exit;
			}
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"Please provide user id."));
		exit;
	}