<?php 
include('database/connection.php');
include('checktoken.php');


function send_push_notification($registation_ids, $questionId, $message) {
      
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

$sender_id=isset($_REQUEST['sender_id'])?$_REQUEST['sender_id']:'';
$receiver_id=isset($_REQUEST['receiver_id'])?$_REQUEST['receiver_id']:'';
$message=isset($_REQUEST['message'])?$_REQUEST['message']:'';
//$date_time=isset($_REQUEST['date_time'])?$_REQUEST['date_time']:'';
$date_time = date('Y-m-d h:i:s A');
$status=isset($_REQUEST['status'])?$_REQUEST['status']:0;

//------------------------- user details ----------------------------------------

$date = date('Y-m-d h:i:s A');
    
    
$sql = "SELECT name FROM usersinfo WHERE id = ".$sender_id." ";
$result = mysql_query($sql) or die(mysql_error()); 

while($row = mysql_fetch_assoc($result))
{
	 $sender_name = $row['name'];
}
//-------------------------------------------------------------------------------

if($sender_id!='' && $receiver_id!='' && $date_time!='')
	{
	 $message_insert=mysql_query("INSERT INTO messaging(sender_id,receiver_id,message,date_time,status) VALUES(".$sender_id.",".$receiver_id.",'".$message."','".$date_time."',".$status.")") or die(mysql_error());
		if($message_insert)
			{
				//------------------------------- push notification -------------------------------
						
                
                $pushNotifycationType="chat";
                $parmaster=$sender_id;
                $message=$sender_name.' sent message to you';
                
                $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$sender_id.",".$receiver_id.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());
        
        
		 $notification_query = "SELECT device_token,type FROM device_token_tb where user_id =".$receiver_id."";

			$notification_record = mysql_query($notification_query) or die(mysql_error());
			if(mysql_num_rows($notification_record) > 0)
			{
			 $i=0;
			 while($row_frd = mysql_fetch_assoc($notification_record))
				{
					//$message =  $sender_name.' sent you a message';
					//$deviceToken[$i] = $row['device_token'];
					//$deviceToken[$i] = $row_frd['device_token'];
                    $badge=1;
					$type[$i] = $row_frd['type'];
					if($type[$i]==0){ 
 						$questionId = $sender_id;
						$deviceToken[$i] = $row_frd['device_token'];
					 $apple = send_app_push_notification($deviceToken[$i], $questionId, $message, $badge);
						
					}
					else{ 
					   $questionId = $sender_id;
					   $deviceToken[$i] = $row_frd['device_token'];
	  				   $android = send_push_notification($deviceToken[$i], $questionId, $message);
					}
					$i++;
				} // echo "<pre>"; print_r($deviceToken);
			}
			//---------------------------------------------------------------------------------
				
				echo json_encode(array("status"=>"1","message"=>"message sent successfully","message_id"=>(string)mysql_insert_id(),"date_time"=>$date_time ));
				exit;
			}
		else	
			{
				echo json_encode(array("status"=>"0","message"=>"message could not be sent"));
				exit;
			}
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"Please provide sufficient data"));
		exit;
	}

?>