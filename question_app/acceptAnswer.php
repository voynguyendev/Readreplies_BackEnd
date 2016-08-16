<?php
include('database/connection.php');
include('checktoken.php');

function send_push_notification($registation_ids, $questionId, $message) {
	
	    
 	//define("GOOGLE_API_KEY","AIzaSyDkeHl6jszT59Jm1R3QjnphmlsrENMzHwc");
	define("GOOGLE_API_KEY","AIzaSyA6TSedoYzl9GKksAhJSQKQtUv44YCHTWM");
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

		//$registatoin_ids = array($registation_ids);
		//$msg = array("message" => $message,"questionId" => $questionId); 
		
  		/*$fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );*/
		
		$fields = array(
			'registration_ids' => array($registation_ids),
			'data' => array("message" => $message,"questionId"=>$questionId)
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
		 //echo 'Message successfully delivered' . PHP_EOL;
		 //echo $result;
		
		// Close the connection to the server
		fclose($fp);
		//exit;
		// return true;
	} 

$answer_id=isset($_REQUEST['answer_id'])?$_REQUEST['answer_id']:'';
$status=isset($_REQUEST['status'])?$_REQUEST['status']:'';
$logged_in_user_id = isset($_REQUEST['logged_in_user_id'])?$_REQUEST['logged_in_user_id']:'';
$date = date('Y-m-d h:i:s A');

$accept_date = date('Y-m-d h:i:s A');
//------------------------- user details ----------------------------------------

$sql = "SELECT name,id FROM usersinfo WHERE id = (SELECT userId FROM answers WHERE id = ".$answer_id.") ";
$result = mysql_query($sql) or die(mysql_error()); 

while($row = mysql_fetch_assoc($result))
{
	 $answer_creator_name = $row['name'];
     $answer_creator_id = $row['id'];
    
    
}
//-------------------------------------------------------------------------------

if($answer_id!='' && $status!='')
	{
		$checkAnswer=mysql_query("SELECT * FROM answers WHERE id=".$answer_id."");
		if(mysql_num_rows($checkAnswer)>0)
			{
				while($question_row = mysql_fetch_assoc($checkAnswer))
				{
					$questionId = $question_row['questionId'];
					$sql = "SELECT name,id FROM usersinfo WHERE id = (SELECT userId FROM questions WHERE id = ".$questionId.") ";
					$result = mysql_query($sql) or die(mysql_error()); 
					
					while($row = mysql_fetch_assoc($result))
					{
						 $question_creator_name = $row['name'];
					     $question_creator_id = $row['id'];
					    
					    
					}
				}
				
                $inforanswer= "select (MAX(orderindex)+1) as maxid from answers";
                $result = mysql_query($inforanswer);
                while($row = mysql_fetch_assoc($result))
                {
                    $maxidanswer = $row['maxid'];
                   
                    
                    
                }
                
				$updateanswer=mysql_query("UPDATE answers SET status='".$status."',orderindex=".$maxidanswer." WHERE id=".$answer_id."");
				if($updateanswer)
					{
			//------------------------------- push notification -------------------------------
			if($status == 'accepted')
			{		
				$status = 'accepted';
				
                

                $pushNotifycationType="accept answer";


                $parmaster=$questionId;
                $message=$question_creator_name.' accepted your replay as an answer';

                if($question_creator_id!=$logged_in_user_id)
                $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$logged_in_user_id.",".$answer_creator_id.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());
                
                
				$updateanswerdate=mysql_query("UPDATE answers SET  IsView=0,accept_date ='".$accept_date."' WHERE id=".$answer_id."") or die(mysql_error());
				//$accept_date_update = mysql_query("INSERT INTO answers(accept_date) VALUES('".$accept_date."')") or die(mysql_error());
				
				$notification_query = "SELECT device_token,type FROM device_token_tb where user_id =
				(SELECT userId FROM answers WHERE id = '".$answer_id."') AND user_id != '".$logged_in_user_id."'";
	
				$notification_record = mysql_query($notification_query) or die(mysql_error());
				if(mysql_num_rows($notification_record) > 0)
				{	
				 $i=0;
				 while($row_frd = mysql_fetch_assoc($notification_record))
					{	
						$type[$i] = $row_frd['type'];
						if($type[$i]==0){
							
						  	$deviceToken[$i] = $row_frd['device_token'];  
						 	$apple = send_app_push_notification($deviceToken[$i], $questionId, $message, $badge);
							
						}
						else{ 
							$message =  $answer_creator_name.' your answer accepted'; 
						    $deviceToken[$i] = $row_frd['device_token']; 
						    $android = send_push_notification($deviceToken[$i], $questionId, $message);
						  
						}
						$i++;
					} 
						echo json_encode(array("success"=>"1","message"=>$status));
						exit;
				}
				else{ 
					echo json_encode(array("success"=>"1","message"=>"accepted"));
					exit;
				}
				//echo json_encode(array("status"=>"1","message"=>$status));
				//exit;
				
			}
			elseif($status = 'rejected')
			{
				$status = 'rejected';
				
                
                $pushNotifycationType="reject answer";
                $parmaster=$questionId;
                $message=$answer_creator_name.' rejected your replay as an answer';
                
                $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$logged_in_user_id.",".$answer_creator_id.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());
                
                
				$notification_query = "SELECT device_token,type FROM device_token_tb where user_id =(SELECT userId FROM answers WHERE id = '".$answer_id."') AND user_id != '".$logged_in_user_id."'";
	
				$notification_record = mysql_query($notification_query) or die(mysql_error());
				if(mysql_num_rows($notification_record) > 0)
				{
				 $i=0;
				 while($row_frd = mysql_fetch_assoc($notification_record))
					{	
						
						$type[$i] = $row_frd['type'];
						if($type[$i]==0){ 
						 $message =  $answer_creator_name.' your answer rejected';	
						 $deviceToken[$i] = $row_frd['device_token'];  
						 $apple = send_app_push_notification($deviceToken[$i], $questionId, $message, $badge);
						
						}
						else{ 
							$message =  $answer_creator_name.' your answer rejected';
						    $deviceToken[$i] = $row_frd['device_token'];  
						    $android = send_push_notification($deviceToken[$i], $questionId, $message);
						    
						}
						$i++;
					}
					echo json_encode(array("success"=>"1","message"=>$status));
					exit;
				}else{ 
					echo json_encode(array("success"=>"1","message"=>"rejected"));
					exit;
				}
				
				//echo json_encode(array("status"=>"1","message"=>$status));
				//exit;
			}
			//---------------------------------------------------------------------------------
						
					//echo json_encode(array("success"=>"1","message"=>$status));
					//exit;
		}
		else
				{
					echo json_encode(array("success"=>"0","message"=>"Error"));
					exit;
				}
			}
	}
else
	{
		echo json_encode(array("success"=>"0","message"=>"Please provide correct data."));
		exit;
	}