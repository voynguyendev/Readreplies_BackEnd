<?php

/*include("database/connection.php");

if($_REQUEST['question_id'] !="" && $_REQUEST['verdict'] !="")
{
	$q_id = $_REQUEST['question_id'];
	$getQ_id = mysql_query("SELECT * FROM questions WHERE id = '".$q_id."'") or die(mysql_error());
	if(mysql_num_rows($getQ_id) > 0)
	{
		$verdict = $_REQUEST['verdict'];
		if($verdict == 0)
		{
			$updateLikes = mysql_query("UPDATE questions SET likes=likes + 1 WHERE id = '".$q_id."'") or die(mysql_error());
			if($updateLikes)
			{
				echo json_encode(array("message"=>"likes updated","success"=>1));
				exit;
			}
		}
		else if($verdict == 1)
		{
			$updateDislikes = mysql_query("UPDATE questions SET dislikes=dislikes + 1 WHERE id = '".$q_id."'") or die(mysql_error());
			if($updateDislikes)
			{
				echo json_encode(array("message"=>"dislikes updated","success"=>1));
				exit;
			}
		}
		else
		{
			echo json_encode(array("message"=>"garbage value","success"=>0));
			exit;	
		}
	}
	else{
		echo json_encode(array("message"=>"Incorrect info","success"=>0));
		exit;
	}
}
else{
	echo json_encode(array("message"=>"Incorrect info","success"=>0));
	exit;	
}*/
?>
<?php

    
include("database/connection.php");
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
  // return;
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

    

$date = date('Y-m-d h:i:s A');

if($_REQUEST['question_id'] !="" && $_REQUEST['verdict'] !="" && $_REQUEST['user_id']!='')
{
	$u_id = $_REQUEST['user_id'];
	$q_id = $_REQUEST['question_id'];
	$getQ_id = mysql_query("SELECT * FROM questions WHERE id = '".$q_id."'") or die(mysql_error());
    $update="update questions set question_date_update=now(),point=point+1 where id=".$q_id;
    mysql_query($update) or die(mysql_error());
    
    $sql = "SELECT name,id FROM usersinfo WHERE id = (SELECT userId FROM questions WHERE id = ".$q_id.") ";
    $result = mysql_query($sql) or die(mysql_error());

    while($row = mysql_fetch_assoc($result))
    {
        
        $question_creator_id = $row['id'];
        
        
    }
    
    
    $sql = "SELECT name,id FROM usersinfo WHERE id = ".$u_id."";
    $result = mysql_query($sql) or die(mysql_error());
    
    while($row = mysql_fetch_assoc($result))
    {
        $question_creator_name = $row['name'];
        
        
    }
    
    
    if(mysql_num_rows($getQ_id) > 0)
	{
		$verdict = $_REQUEST['verdict'];
		if($verdict == 1)
		{
			
			$updateLikes = mysql_query("UPDATE questions SET likes=likes + 1 WHERE id = '".$q_id."'") or die(mysql_error());
			if($updateLikes)
			{
				$checklike_dislike=mysql_query("select id from question_like_dislike where user_id='".$u_id."' AND question_id ='".$q_id."'") or die(mysql_error());
				if(mysql_num_rows($checklike_dislike) == 0)
				{
					$insertlike_dislike=mysql_query("insert into question_like_dislike(user_id,question_id,status) values('$u_id','$q_id','$verdict')") or die(mysql_error());
					if($insertlike_dislike)
					{
                
                        $pushNotifycationType="like question";
                        $parmaster=$q_id;
                        $message=$question_creator_name.' likes your post';

                        if($question_creator_id!=$u_id)
                             $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$u_id.",".$question_creator_id.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());
                        
                        $notification_query = "SELECT device_token,type FROM device_token_tb where user_id = (SELECT userId FROM questions WHERE id = '".$q_id."') AND user_id != '".$u_id."'";
                        
                        $notification_record = mysql_query($notification_query) or die(mysql_error());
                        if(mysql_num_rows($notification_record) > 0)
                        {
                            $i=0;
                            while($row_frd = mysql_fetch_assoc($notification_record))
                            {
                                $type[$i] = $row_frd['type'];
                                $badge=1;
                                if($type[$i]==0){
                                    
                                    $deviceToken[$i] = $row_frd['device_token'];
                                    $apple = send_app_push_notification($deviceToken[$i], $q_id, $message, $badge);
                                    
                                }
                                else{ 
                                   // $message =  $answer_creator_name.' your answer accepted';
                                    $deviceToken[$i] = $row_frd['device_token']; 
                                    $android = send_push_notification($deviceToken[$i], $q_id, $message);
                                    
                                }
                                $i++;
                            } 
                          
                        }
                        
                        
                        
                        
                        
                        
                        
						echo json_encode(array("message"=>"likes updated","success"=>1));
						exit;
					}
				}
				else{
					echo json_encode(array("message"=>"Already liked or disliked","success"=>0));
					exit;	
					
				}
			}
		}
		else if($verdict == 0)
		{
			$updateDislikes = mysql_query("UPDATE questions SET dislikes=dislikes + 1 WHERE id = '".$q_id."'") or die(mysql_error());
			if($updateDislikes)
			{
				$checklike_dislike=mysql_query("select id from question_like_dislike where user_id='".$u_id."' AND question_id ='".$q_id."'") or die(mysql_error());
				if(mysql_num_rows($checklike_dislike) == 0)
				{
                    
                    $pushNotifycationType="dislike question";
                    $parmaster=$q_id;
                    $message=$question_creator_name.' dislikes your post';
                    
                    $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$u_id.",".$question_creator_id.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());
                    
                    $notification_query = "SELECT device_token,type FROM device_token_tb where user_id = (SELECT userId FROM questions WHERE id = '".$q_id."') AND user_id != '".$u_id."'";
                    
                    $notification_record = mysql_query($notification_query) or die(mysql_error());
                    if(mysql_num_rows($notification_record) > 0)
                    {
                        $i=0;
                        while($row_frd = mysql_fetch_assoc($notification_record))
                        {
                            $type[$i] = $row_frd['type'];
                            if($type[$i]==0){
                                
                                $deviceToken[$i] = $row_frd['device_token'];
                                $apple = send_app_push_notification($deviceToken[$i], $q_id, $message, $badge);
                                
                            }
                            else{
                               // $message =  $answer_creator_name.' your answer accepted';
                                $deviceToken[$i] = $row_frd['device_token'];
                                $android = send_push_notification($deviceToken[$i], $q_id, $message);
                                
                            }
                            $i++;
                        }
                        
                    }

                    
                    
                    
					$insertlike_dislike=mysql_query("insert into question_like_dislike(user_id,question_id,status) values('$u_id','$q_id','$verdict')") or die(mysql_error());
					if($insertlike_dislike)
					{
						echo json_encode(array("message"=>"dislikes updated","success"=>1));
						exit;
					}
				}
				else{
					echo json_encode(array("message"=>"Already liked or disliked","success"=>0));
					exit;	
					
				}
			}
		}
		else
		{
			echo json_encode(array("message"=>"garbage value","success"=>0));
			exit;	
		}
	}
	else{
		echo json_encode(array("message"=>"Incorrect info","success"=>0));
		exit;
	}
}
else{
	echo json_encode(array("message"=>"Incorrect info","success"=>0));
	exit;	
}

?>
