<?php
include('database/connection.php');


include('checktoken.php');

function send_push_notification($registation_ids, $questionId, $message) {
       
 	//define("GOOGLE_API_KEY","AIzaSyDkeHl6jszT59Jm1R3QjnphmlsrENMzHwc");
	
	define("GOOGLE_API_KEY","AIzaSyA6TSedoYzl9GKksAhJSQKQtUv44YCHTWM");
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

  		/*$fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );
		*/
		$fields = array(
			'registration_ids' => array($registation_ids),
			'data' => array("message" => $message,"questionId" => $questionId)
		);
		
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        //print_r($headers);
        // Open connection
		//echo $registatoin_ids;
       $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
     	curl_setopt($ch, CURLOPT_URL, $url);
 	
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
		
		
		
        if ($result === FALSE) {
           // die('Curl failed: ' . curl_error($ch));
		   echo "something is wrong";
        }
 
        // Close connection
        curl_close($ch);
        //echo $result;
		//exit;
    }

function send_app_push_notification($deviceToken, $questionId, $message, $badge=0) { 	// Put your device token here (without spaces):
		//$deviceToken = 'a114e0a7aeff4be406ba7cf203b09473937ee476f7e80b3adb63c17c6efbfae6';
		//$deviceToken = 'a114e0a7aeff4be406ba7cf203b09473937ee476f7e80b3adb63c17c6efbfae6'; 
		// Put your private key's passphrase here:
		
		return;
		$passphrase = '1234';
		 
		// Put your alert message here:
		// $message = 'A push notification has been sent!';
		// $message = 'You have an image to view';
		////////////////////////////////////////////////////////////////////////////////
		 
		$ctx = stream_context_create();
		
		// stream_context_set_option($ctx, 'ssl', 'local_cert', '../ck.pem');
		//stream_context_set_option($ctx, 'ssl', 'local_cert', 'PlayDate.pem');
		stream_context_set_option($ctx, 'ssl', 'local_cert', 'QboxPushlive.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		 
		// Open a connection to the APNS server
		$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		 
		if (!$fp)
		// exit("Failed to connect: $err $errstr" . PHP_EOL);
		 
		echo 'Connected to APNS' . PHP_EOL;
		 
		// Create the payload body
		/*$body['aps'] = array(
		'alert' => array(
		'body' => $message,
		'action-loc-key' => 'Bango App',
		),
		'badge' => 6,
		);*/
		
		/*$sql = "SELECT sound FROM signup WHERE id = '".$to."'";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) 
		{
			$n_sound = $row['sound'];
		}*/
		
		$n_sound = 'default';
		
		$body['aps'] = array(
		'badge'=> $badge,
		'alert' => $message,
		'questionId' => $questionId,
		'sound' => $n_sound
 		);
		

		// Encode the payload as JSON
		$payload = json_encode($body);
		
	
		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		//$msg = $payload;
		 
		// Send it to the server
		 $result = fwrite($fp, $msg, strlen($msg));
		 
		 
		 
		 //$result = $msg;
		 
		if (!$result)
		 echo 'Message not delivered' . PHP_EOL;
		else
		// echo 'Message successfully delivered' . PHP_EOL;
		 //echo $result;
		
		// Close the connection to the server
		fclose($fp);
		//exit;
		// return true;
	} 

 $sender_id=$_REQUEST['sender_id']; 
 $receiver_id=$_REQUEST['receiver_id'];
 $pushnotifycationid=$_REQUEST['pushnotifycationid'];
 
    
    
    
    
 $date = date('Y-m-d h:i:s A');
//------------------------- user details ----------------------------------------

 $sql = "SELECT name as s_name, (SELECT name FROM usersinfo WHERE id = ".$receiver_id.") as r_name FROM usersinfo WHERE id = ".$sender_id." ";
$result = mysql_query($sql) or die(mysql_error()); 

while($row = mysql_fetch_assoc($result))
{
	  $s_name = $row['s_name'];  
	  $r_name = $row['r_name'];  
}
//-------------------------------------------------------------------------------

$status=isset($_REQUEST['status'])?$_REQUEST['status']:0;
  //  if($status==3)
    //{
    //mysql_query("Delete from friendRequests  WHERE sender_id=".$sender_id." AND  receiver_id=".$receiver_id."");
    //mysql_query("Delete from PushNotifycation");
    //}

/*$checkPreviousRequest=mysql_query("SELECT * FROM friendRequests WHERE (sender_id=".$sender_id." AND  receiver_id=".$receiver_id.") OR (sender_id=".$receiver_id." AND  receiver_id=".$sender_id.")");*/
$checkPreviousRequest=mysql_query("SELECT * FROM friendRequests WHERE sender_id=".$sender_id." AND  receiver_id=".$receiver_id."");

// This condition will be true if the request has been sent before and the status is not 0
if(mysql_num_rows($checkPreviousRequest)>0 && $status!=0)
	{
		
		if($status==1)
			{
				$s="accepted";
				/*$friendRequestUpdate=mysql_query("UPDATE friendRequests SET status=1,date='$date' WHERE (sender_id=".$sender_id." AND  receiver_id=".$receiver_id.") OR (sender_id=".$receiver_id." AND  receiver_id=".$sender_id.")") or die(mysql_error());*/
				$friendRequestUpdate=mysql_query("UPDATE friendRequests SET status=1,date='$date' WHERE sender_id=".$sender_id." AND  receiver_id=".$receiver_id."") or die(mysql_error());
              
                
                
			if(mysql_affected_rows() > 0) 
			{
                
                $acceptedpushnotifycation=mysql_query("UPDATE PushNotifycation SET Isdelete=1 WHERE id=".$pushnotifycationid."") or die(mysql_error());
                
                
                $pushNotifycationType="accepted friend request";
                $parmaster=$sender_id;
                $message=$r_name.' accepted your friend request ';
                
                
                
                
                $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$receiver_id.",".$sender_id.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());
                
                
                
			//------------------------------- push notification -------------------------------
			
			//$question_id_query = mysql_query("SELECT sender_id FROM friendRequests WHERE (sender_id=".$sender_id." AND  receiver_id=".$receiver_id.") OR (sender_id=".$receiver_id." AND  receiver_id=".$sender_id.")") or die(mysql_error());
			//$question_id_result = mysql_fetch_assoc($question_id_query);
			//$q_id = $question_id_result['sender_id'];			
			
			
			$notification_query = "SELECT device_token,type FROM device_token_tb where user_id = '".$sender_id."'";
			
			$notification_record = mysql_query($notification_query) or die(mysql_error());
			if(mysql_num_rows($notification_record) > 0){
	
			$i = 0;
			 while($row_frd = mysql_fetch_assoc($notification_record))
					{
					$message = $r_name.' has accepted your friend request ';
					$type[$i] = $row_frd['type'];
					
						if($type[$i]==0){
                            $badge=1;
							 $questionId = $receiver_id ; 
							 $deviceToken[$i] = $row_frd['device_token'];
						 $apple = send_app_push_notification($deviceToken[$i], $questionId, $message, $badge);
						}
						else{ 
							$questionId = $receiver_id ; 
							$deviceToken[$i] = $row_frd['device_token'];
						   $android = send_push_notification($deviceToken[$i], $questionId, $message);
						}
					
					$i++;
					}
				}
			//---------------------------------------------------------------------------------
			  }
			}
		elseif($status==2)
			{
				$s="rejected";
                $acceptedpushnotifycation=mysql_query("UPDATE PushNotifycation SET Isdelete=1 WHERE id=".$pushnotifycationid) or die(mysql_error());
				$friendRequestUpdate=mysql_query("DELETE FROM  friendRequests WHERE sender_id=".$sender_id." AND  receiver_id=".$receiver_id."");
			
			/*if(mysql_affected_rows() > 0) 
			{	
			//------------------------------- push notification -------------------------------
						
			$notification_query = "SELECT device_token,type FROM device_token_tb where user_id =
			'".$sender_id."'";

			$notification_record = mysql_query($notification_query) or die(mysql_error());
			if(mysql_num_rows($notification_record) > 0){
			$i=0;
			 while($row_frd = mysql_fetch_assoc($notification_record))
				{
					$message = $s_name.' your friend request rejected';
					$type[$i] = $row_frd['type'];
					
						if($type[$i]==0){  
							$questionId = $receiver_id ; 
							$deviceToken[$i] = $row_frd['device_token'];
						 $apple = send_app_push_notification($deviceToken[$i], $questionId, $message, $badge);
							
						}
						else{ 
							$questionId = $receiver_id ; 
							$deviceToken[$i] = $row_frd['device_token'];
						   $android = send_push_notification($deviceToken[$i], $questionId, $message);
						}
					
					$i++;
				}
				}
						
			//---------------------------------------------------------------------------------
			  }*/
			}
		echo json_encode(array("status"=>"1","message"=>"Friend request ".$s.""));
		exit;
	}
// This condition will be true if the request has been sent before and the status is either empty or 0
elseif(mysql_num_rows($checkPreviousRequest)>0)
	{
		echo json_encode(array("status"=>"0","message"=>"You have already sent request to this user or the user is already friend with you"));
		exit;
	}
// This condition will be true if the request has not been sent before 
else
	{
        
        //test
        
        
        
        //test

        
	 $insertFriendRequest=mysql_query("INSERT INTO friendRequests(sender_id,receiver_id,status,date) VALUES(".$sender_id.",".$receiver_id.",0,'$date') ") or die(mysql_error());
		
		if($insertFriendRequest)
			{
					//------------------------------- push notification -------------------------------
			
                
                
                $pushNotifycationType="friend request";
                $parmaster=$sender_id;
                $message=$s_name.' sent you a friend request';
                
                $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$sender_id.",".$receiver_id.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());
    
                
                
		   $notification_query = "SELECT device_token , type FROM device_token_tb where user_id ='".$receiver_id."'";
            
			$notification_record = mysql_query($notification_query) or die(mysql_error());
			$i = 0;
			 while($row_frd = mysql_fetch_assoc($notification_record))
				{
					
					$type[$i] = $row_frd['type'];

						if($type[$i]==0){ 
							$questionId = $sender_id ; 
							$deviceToken[$i] = $row_frd['device_token'];
						 $apple = send_app_push_notification($deviceToken[$i], $questionId, $message, $badge);
							
						}
						else{ 
							$questionId = $sender_id ; 
							$deviceToken[$i] = $row_frd['device_token'];
						   $android = send_push_notification($deviceToken[$i], $questionId, $message);
						}
					
					$i++;
				}
						
			//---------------------------------------------------------------------------------
				
				echo json_encode(array("status"=>"1","message"=>"Friend request has been successfully sent."));
				exit;
			}
		else		
			{
				echo json_encode(array("status"=>"0","message"=>"Friend request could not be sent."));
				exit;
			}
	}
?>