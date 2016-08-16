<?php 
include('database/connection.php');
include('checktoken.php');

$receiver_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';
$sender_id=isset($_REQUEST['friend_id'])?$_REQUEST['friend_id']:'';
$limitrow=isset($_REQUEST['limitrow'])?$_REQUEST['limitrow']:'';

if($receiver_id!='')
	{
		$getUnreadMessages=mysql_query("SELECT m.*,u.name,u.profile_pic 'user_image_thumb' FROM messaging m inner join usersinfo u on m.sender_id=u.id WHERE (receiver_id=".$receiver_id." AND sender_id=".$sender_id.") OR (sender_id=".$receiver_id." AND receiver_id=".$sender_id.") ORDER BY 	
message_id DESC LIMIT ".$limitrow );
		if(mysql_num_rows($getUnreadMessages)>0)
			{
				$messages=array();
				while($row=mysql_fetch_assoc($getUnreadMessages))
					{
						$messages[]=$row;
					}
				echo json_encode(array("status"=>"1","message"=>$messages));
				exit;
			}
		else	
			{
				echo json_encode(array("status"=>"0","message"=>"No new message found."));
				exit;
			}
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"Error"));
		exit;
	}