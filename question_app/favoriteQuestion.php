<?php 
include('database/connection.php');
include('checktoken.php');

//include('apple_push_notification.php');
//include('android_push_notification.php');

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
	
function send_app_push_notification($deviceToken, $questionId, $message,$badge=0) { 	// Put your device token here (without spaces):
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

$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';
$status=isset($_REQUEST['status'])?$_REQUEST['status']:'0';

//---------------------- device token selection ------------------------------------

/*$device_token_query = "SELECT device_token FROM device_token_tb WHERE user_id = '".$userId."'";
$device_token_result = mysql_query($device_token_query) or die(mysql_error());
$j = 0; 
while($device_token_row=mysql_fetch_assoc($device_token_result))
{
	$d_t[$j] = 	$device_token_row['device_token'];
	$j++;
}*/

//----------------------------------------------------------------------------------

$getFavorite=mysql_query("SELECT * FROM favoriteQuestion WHERE questionId=".$questionId." AND userId=".$userId."");

if(mysql_num_rows($getFavorite)>0)
	{
		$updateFavorite=mysql_query("UPDATE favoriteQuestion SET status=".$status." WHERE questionId=".$questionId." AND userID=".$userId."") or die(mysql_error());
			if($updateFavorite)
				{
					echo json_encode(array("success"=>"1","message"=>"successful"));
					exit;
				}
			else
				{
					echo json_encode(array("success"=>"0","message"=>"You could not removed the question as favourite"));
					exit;
				}		
	}	
else
	{
		$addFavorite=mysql_query("INSERT INTO favoriteQuestion(questionId,userId,status) VALUES(".$questionId.",".$userId.",".$status.")") or die(mysql_error());
		if($addFavorite)
			{
			//------------------------------------- push notification -------------------------------
		
			 /*$notification_query = "SELECT device_token,type,(SELECT name FROM usersinfo WHERE id = '".$userId."') as user_name FROM device_token_tb where user_id = (SELECT userId FROM  questions WHERE id = '".$questionId."') AND user_id != '".$userId."' ";

			$notification_record = mysql_query($notification_query) or die(mysql_error());
			if(mysql_num_rows($notification_record) > 0)
			{
			$i=0;
			 while($row_notification = mysql_fetch_assoc($notification_record))
				{
					$name = $row_notification['user_name'];
					$message = $name.' favorited new question ';
					//$deviceToken[$i] = $row['device_token'];
					//$deviceToken = $row_notification['device_token'];
					$type[$i] = $row_notification['type'];
					//if(!in_array($deviceToken,$d_t))
					
						if($type[$i]==0){
						 $deviceToken[$i] = $row_notification['device_token'];		 
						 $apple = send_app_push_notification($deviceToken[$i], $questionId, $message,$badge);
							
						}
						else{
						   $deviceToken[$i] = $row_notification['device_token'];	
						   $android = send_push_notification($deviceToken[$i], $questionId, $message);
						}
					
				$i++;	
			
				}
			}*/
		//---------------------------------------------------------------------------------------
				echo json_encode(array("success"=>"1","message"=>"successful"));
				exit;
			}
		else
			{
				echo json_encode(array("success"=>"0","message"=>"You could not marked the question as favourite"));
				exit;
			}
		
	}
?>