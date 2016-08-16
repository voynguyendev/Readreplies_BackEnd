<?php 
include('database/connection.php');
include('checktoken.php');

$receiver_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';


if($receiver_id!='')
	{
		$getUnreadMessages=mysql_query("SELECT m.*,u.name FROM messaging m inner join usersinfo u on m.sender_id=u.id WHERE m.receiver_id=".$receiver_id." AND m.date_time >SUBDATE(timestamp(now()), INTERVAL 72 HOUR)");
		if(mysql_num_rows($getUnreadMessages)>0)
			{
				$messages=array();
				$userinfor=array();
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