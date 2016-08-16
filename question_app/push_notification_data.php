<?php

include('database/connection.php');




if(isset($_REQUEST['user_id']))
{
	$user_id = $_REQUEST['user_id'];
	
	//message code block	
	$messaging_data_select = mysql_query("SELECT message_id FROM  messaging WHERE receiver_id = '$user_id' ORDER BY date_time DESC LIMIT 10") or die(mysql_error()); // sender_id = '$user_id' OR
	
	if(mysql_num_rows($messaging_data_select) > 0)
	{
		$i=0;
		$m=0;
		while($msg_data = mysql_fetch_assoc($messaging_data_select))
		{
			//------------------------- user_ids -------------------------------------------
			$u_id_query = mysql_query("SELECT sender_id, receiver_id, message, status, date_time FROM messaging WHERE message_id = '".$msg_data['message_id']."' ORDER BY date_time DESC LIMIT 10") or die(mysql_error()); 
			 
			//$m=0;
			while($message_data = mysql_fetch_assoc($u_id_query))
			{
				//-----------------------------------------------------------------------------
				$m_data[$m]['sender_id'] = $message_data['sender_id'];
				$m_data[$m]['receiver_id'] = $message_data['receiver_id'];
				//----------------------------------- sendername fetch -------------------------------------------
				
				$username_sql = mysql_query("SELECT name FROM usersinfo WHERE id = '".$message_data['sender_id']."'") or die(mysql_error());
				$sender_name = mysql_fetch_assoc($username_sql);
				
				$m_data[$m]['sender_name'] = $sender_name['name'];
				//------------------------------------------------------------------------------------------------
				
				$m_data[$m]['message'] = $message_data['message'];
				$m_data[$m]['status'] = $message_data['status'];
				$m_data[$m]['date'] = $message_data['date_time'];
				$m_data[$m]['type'] = 'message_data';
				$m++;
			}
			$i++;
		}
	}
	
	
	//accept answer code block
	$accept_answer_data_select = mysql_query("SELECT questionId, (SELECT name FROM usersinfo WHERE id = '$user_id') as name, answer, attachment, status, thumb, accept_date  FROM  answers WHERE userId = '$user_id' ORDER BY accept_date DESC LIMIT 10") or die(mysql_error());
	if(mysql_num_rows($accept_answer_data_select) > 0)
	{
		$j=0;
		while($accept_answer_data = mysql_fetch_assoc($accept_answer_data_select))
		{
			$a_a_data[$j]['questionId'] = $accept_answer_data['questionId'];
			$a_a_data[$j]['name'] = $accept_answer_data['name'];
			$a_a_data[$j]['answer'] = $accept_answer_data['answer'];
			$a_a_data[$j]['attachment'] = $accept_answer_data['attachment'];
			$a_a_data[$j]['status'] = $accept_answer_data['status'];
			$a_a_data[$j]['thumb'] = $accept_answer_data['thumb'];
			$a_a_data[$j]['date'] = $accept_answer_data['accept_date'];
			$a_a_data[$j]['type'] = 'accept_answer_data';
			$j++;
		}
	}
	
	
	// add answer block code
	
	/* $add_answer_data_select=mysql_query("SELECT answers.*,questions.userID FROM `questions` right join answers on questions.userId = answers.userId WHERE answers.userId = '".$user_id."' ORDER BY answers.answer_date DESC LIMIT 10") or die(mysql_error()); */
	
	
	/*$add_answer_data_select=mysql_query("SELECT * FROM answers WHERE questionId IN (SELECT id FROM questions WHERE userId = ".$user_id.") ORDER BY answer_date DESC LIMIT 10") or die(mysql_error());
	
	
		if(mysql_num_rows($add_answer_data_select) > 0)
		{
			$k=0;
			while($add_answer_data = mysql_fetch_assoc($add_answer_data_select))
			{
				//$a_data[$k]['who_answer_id'] = $add_answer_data['userId'];
				$a_data[$k]['questionId'] = $add_answer_data['questionId'];
				//--------------------------- answered a question ----------------------------------------------
				$user_answer_username = mysql_query("SELECT answers.*,usersinfo.name FROM answers join usersinfo  on  usersinfo.id=answers.userId WHERE  answers.questionId=".$add_answer_data['questionId']." AND usersinfo.id != ".$user_id."") or die(mysql_error());
				$answer_user_name = mysql_fetch_assoc($user_answer_username);
				$a_data[$k]['who_answer_name'] = $answer_user_name['name'];
				
				
				//----------------------------------------------------------------------------------------------
				
				
				
				//---------------------------- user_details (create question)-------------------------------------
				
				$create_answer_username = mysql_query("SELECT questions.*,usersinfo.name FROM questions join usersinfo  on  usersinfo.id=questions.userId WHERE  questions.id=".$add_answer_data['questionId']."") or die(mysql_error());
				$create_user_name = mysql_fetch_assoc($create_answer_username);
				$a_data[$k]['who_create_name'] = $create_user_name['name'];
				//--------------------------------------------------------------------------------
				$a_data[$k]['answer'] = $add_answer_data['answer'];
				$a_data[$k]['attachment'] = $add_answer_data['attachment'];
				$a_data[$k]['status'] = $add_answer_data['status'];
				$a_data[$k]['thumb'] = $add_answer_data['thumb'];
				$a_data[$k]['date'] = $add_answer_data['answer_date'];
				$a_data[$k]['type'] = 'add_answer_data';
				$k++;
			} 
		}*/
		
		$add_answer_data_select=mysql_query("SELECT * FROM answers WHERE questionId IN (SELECT id FROM questions WHERE userId = ".$user_id." UNION SELECT questionId FROM answers WHERE userId = ".$user_id.") AND userId !=".$user_id." ORDER BY answer_date DESC LIMIT 10") or die(mysql_error());
		
		if(mysql_num_rows($add_answer_data_select) > 0)
		{
		$k=0;
			while($add_answer_data = mysql_fetch_assoc($add_answer_data_select))
			{
				//$a_data[$k]['who_answer_id'] = $add_answer_data['userId'];
				$a_data[$k]['questionId'] = $add_answer_data['questionId'];
			//--------------------------- answered a question/create a question -------------------------------------------------
				$user_answer_username = mysql_query("SELECT name FROM usersinfo WHERE id=".$add_answer_data['userId']." AND id !=".$user_id."") or die(mysql_error());
				$answer_user_name = mysql_fetch_assoc($user_answer_username);
				$a_data[$k]['who_answer_name'] = $answer_user_name['name'];
				
				
			//--------------------------------------------------------------------------------------------------
			//---------------------------- user_details (create question)---------------------------------------
				
				/*$create_answer_username = mysql_query("SELECT questions.*,usersinfo.name FROM questions join usersinfo  on  usersinfo.id=questions.userId WHERE  questions.id=".$add_answer_data['questionId']."") or die(mysql_error());
				$create_user_name = mysql_fetch_assoc($create_answer_username);
				$a_data[$k]['who_create_name'] = $create_user_name['name'];*/
				
			//--------------------------------------------------------------------------------------------------	
				$a_data[$k]['answer'] = $add_answer_data['answer'];
				$a_data[$k]['attachment'] = $add_answer_data['attachment'];
				$a_data[$k]['status'] = $add_answer_data['status'];
				$a_data[$k]['thumb'] = $add_answer_data['thumb'];
				$a_data[$k]['date'] = $add_answer_data['answer_date'];
				$a_data[$k]['type'] = 'add_answer_data';
				$k++;
			} 
		}
	
	// friend request code block
	$frd_request_data=mysql_query("SELECT *,(SELECT name FROM usersinfo WHERE id = ".$user_id.") as name FROM friendRequests WHERE  receiver_id=".$user_id." ORDER BY friendRequests.date DESC LIMIT 10");//sender_id=".$user_id." OR 
	if(mysql_num_rows($frd_request_data) > 0)
	{
		$l=0;
		while($frd_data = mysql_fetch_assoc($frd_request_data))
		{
			$friend_data[$l]['name'] = $frd_data['name'];
			$friend_data[$l]['sender_id'] = $frd_data['sender_id'];
			$friend_data[$l]['receiver_id'] = $frd_data['receiver_id'];
			$friend_data[$l]['status '] = $frd_data['status'];
			$friend_data[$l]['date'] = $frd_data['date'];
			$friend_data[$l]['type'] = 'friend_request_data';
			$l++;
		}
	}
	
	if(empty($m_data) && empty($a_a_data) && empty($a_data) && empty($friend_data))
	{
		
		echo json_encode(array("message_data"=>array(),"accept_answer_data"=>array(),"add_answer_data"=>array(),"friend_data"=>array(),"success"=>0));
		exit;
	}
	else{
	echo json_encode(array("message_data"=>$m_data,"accept_answer_data"=>$a_a_data,"add_answer_data"=>$a_data,"friend_data"=>$friend_data,"success"=>1));
	exit;
	}
			
}

?>