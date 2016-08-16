<?php 
include('database/connection.php');

$receiver_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';


if($receiver_id!='')
	{
$getUnreadMessages=
mysql_query("SELECT m.*,u.name,u.profile_pic 'user_image_thumb' FROM messaging m inner join usersinfo u on m.sender_id=u.id WHERE m.sender_id =".$receiver_id ." or m.receiver_id=".$receiver_id." ORDER BY message_id DESC ");
		if(mysql_num_rows($getUnreadMessages)>0)
			{
				$messages=array();
				$userinfor=array();
				while($row=mysql_fetch_assoc($getUnreadMessages))
					{
						$messages[]=$row;
						
					}
                                        $updatmessage=mysql_query("update messaging set IsView=1 where receiver_id=".$receiver_id."");


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