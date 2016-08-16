<?php 
include('database/connection.php');
include('checktoken.php');

function send_push_notification($registation_ids, $questionId, $message) {
       
 	define("GOOGLE_API_KEY","AIzaSyDkeHl6jszT59Jm1R3QjnphmlsrENMzHwc");
	//define("GOOGLE_API_KEY","AIzaSyC-Xb6lUZs87N2G774r-EqEhU0QU1xwAiA");
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

  		/*$fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );
		*/
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
		//exit;
    }

function send_app_push_notification($deviceToken, $questionId, $message, $badge=0) { 	// Put your device token here (without spaces):
		//$deviceToken = 'a114e0a7aeff4be406ba7cf203b09473937ee476f7e80b3adb63c17c6efbfae6';
		//$deviceToken = 'a114e0a7aeff4be406ba7cf203b09473937ee476f7e80b3adb63c17c6efbfae6'; 
		// Put your private key's passphrase here:
		$passphrase = '';
		 
		// Put your alert message here:
		// $message = 'A push notification has been sent!';
		// $message = 'You have an image to view';
		////////////////////////////////////////////////////////////////////////////////
		 
		$ctx = stream_context_create();
		
		// stream_context_set_option($ctx, 'ssl', 'local_cert', '../ck.pem');
		//stream_context_set_option($ctx, 'ssl', 'local_cert', 'PlayDate.pem');
		stream_context_set_option($ctx, 'ssl', 'local_cert', 'Qbox.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		 
		// Open a connection to the APNS server
		$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		 
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


$answerId=isset($_REQUEST['answerId'])?$_REQUEST['answerId']:'';
$rating=isset($_REQUEST['rating'])?$_REQUEST['rating']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';

//------------------------- user details ----------------------------------------

/*$sql = "SELECT name FROM usersinfo WHERE id = (SELECT userId FROM answers WHERE id = ".$answerId.") ";
$result = mysql_query($sql) or die(mysql_error()); 

while($row = mysql_fetch_assoc($result))
{
	 $answer_creator_name = $row['name'];
}*/
//-------------------------------------------------------------------------------

$checkPreviousRating=mysql_query("SELECT * FROM answers_rating WHERE answerId=".$answerId." AND userId=".$userId."");
if(mysql_num_rows($checkPreviousRating)>0)
	{
		 $question_id_query = mysql_query("SELECT * FROM answers WHERE id=".$answerId."") or die(mysql_error());
		
		while($question_row = mysql_fetch_assoc($question_id_query))
				{
					$questionId = $question_row['questionId'];
				}
				
				
	 $updateRating=mysql_query("UPDATE answers_rating SET rating=".$rating."  WHERE answerId=".$answerId." AND userId=".$userId."");
		if($updateRating)
			{
				//------------------------------- push notification -------------------------------
					
			/*$notification_query = "SELECT device_token,type FROM device_token_tb where user_id =
			(SELECT userId FROM answers WHERE id = '".$answerId."')";

			$notification_record = mysql_query($notification_query) or die(mysql_error());
			$i=0;
			 while($row_frd = mysql_fetch_assoc($notification_record))
				{
					$message = $answer_creator_name.' rated your answer';
					//$deviceToken[$i] = $row['device_token'];
					$deviceToken[$i] = $row_frd['device_token'];
					$type[$i] = $row_frd['type'];
					if($type[$i]==0){
 
					 $apple = send_app_push_notification($deviceToken[$i], $questionId, $message, $badge);
						
					}
					else{
					   $android = send_push_notification($deviceToken[$i], $questionId, $message);
					}
					$i++;
				}*/
						
			//---------------------------------------------------------------------------------
				
				echo json_encode(array("status"=>"1","message"=>"Your rating has been successfully updated to this answer."));
				exit;

			}
		else	
			{
				echo json_encode(array("status"=>"0","message"=>"Your rating could not be updated to this answer."));
				exit;

			}
	}
else
	{
		 $addRating=mysql_query("INSERT INTO answers_rating(rating,answerId,userId) VALUES('".$rating."','".$answerId."','".$userId."')") ;
		if($addRating)
			{
				//------------------------------- push notification -------------------------------
						
		 	/*$notification_query = "SELECT device_token,type FROM device_token_tb where user_id =
			(SELECT userId FROM answers WHERE id = '".$answerId."')";

			$notification_record = mysql_query($notification_query) or die(mysql_error());
			$i=0;
			 while($row_frd = mysql_fetch_assoc($notification_record))
				{
					$message = $answer_creator_name.' rated your answer';
					//$deviceToken[$i] = $row['device_token'];
					$deviceToken[$i] = $row_frd['device_token'];
					$type[$i] = $row_frd['type'];
					if($type[$i]==0){
 
					 $apple = send_app_push_notification($deviceToken[$i], $questionId, $message, $badge);
						
					}
					else{
					   $android = send_push_notification($deviceToken[$i], $questionId, $message);
					}
					$i++;
				}*/
						
			//---------------------------------------------------------------------------------
				
				echo json_encode(array("status"=>"1","message"=>"You have successfully rated to this answer."));
				exit;

			}
		else	
			{
				echo json_encode(array("status"=>"0","message"=>"Your rating could not be added."));
				exit;

			}	
	}

?>