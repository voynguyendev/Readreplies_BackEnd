<?php
include('database/connection.php');

include('checktoken.php');
$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';

if($user_id!='')
	{
		$getFriends=mysql_query("SELECT * FROM friendRequests Left JOIN usersinfo ON friendRequests.receiver_id=usersinfo.id WHERE status=1 AND sender_id=".$user_id."") or die(mysql_error());
		
		$friends_data=array();
		$i=0;
		
		if(mysql_num_rows($getFriends)>0)
			{
				while($row=mysql_fetch_array($getFriends))
					{
						$friends_data[$i]['id']=$row['receiver_id'];
						$friends_data[$i]['name']=$row['name'];
						$friends_data[$i]['email']=$row['email'];
						$friends_data[$i]['profile_pic']=$row['profile_pic'];
						$friends_data[$i]['thumb']=$row['thumb'];
						$friends_data[$i]['StatusText']=$row['StatusText'];
                        $friends_data[$i]['lname']=$row['lname'];
                        

						$i++;
					}
			}
		$getFriends=mysql_query("SELECT * FROM friendRequests Left JOIN usersinfo ON friendRequests.sender_id=usersinfo.id   WHERE status=1 AND receiver_id=".$user_id."") or die(mysql_error());
		
		if(mysql_num_rows($getFriends)>0)
			{
				while($row=mysql_fetch_array($getFriends))
					{
						$friends_data[$i]['id']=$row['sender_id'];
						$friends_data[$i]['name']=$row['name'];
						$friends_data[$i]['email']=$row['email'];
						$friends_data[$i]['profile_pic']=$row['profile_pic'];
						$friends_data[$i]['thumb']=$row['thumb'];
						$friends_data[$i]['StatusText']=$row['StatusText'];
                        $friends_data[$i]['lname']=$row['lname'];
						$i++;
					}
			}
		if(count($friends_data)>0)
			{
				echo json_encode(array("status"=>"1","message"=>"Please provide user id.","data"=>$friends_data));
				exit;
			}
		else	
			{
				echo json_encode(array("status"=>"0","message"=>"No friend found."));
				exit;
			}
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"Please provide user id."));
		exit;
	}
?>

