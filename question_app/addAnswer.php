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

function send_app_push_notification($deviceToken, $questionId, $message,$badge=0) { 	// Put your device token here (without spaces):
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
		// echo 'Message successfully delivered' . PHP_EOL;
		 //echo $result;
		
		// Close the connection to the server
		fclose($fp);
		//exit;
		// return true;
	} 


define('UPLOAD_DIR', '../question_app/answer_images/');
define('UPLOAD_DIR_THUMB', '../question_app/answer_images/thumbs/');

$answer=isset($_REQUEST['answer'])?$_REQUEST['answer']:'';
$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';
//$answer_date=isset($_REQUEST['date'])?$_REQUEST['date']:'';
$answer_date= date('Y-m-d h:i:s A');
$attachment=isset($_POST['attachment'])?$_POST['attachment']:'';
$status=isset($_REQUEST['status'])?$_REQUEST['status']:'';
    
$answerid=isset($_REQUEST['answerid'])?$_REQUEST['answerid']:'';
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
 $entity=isset($_REQUEST['entity'])?$_REQUEST['entity']:0;



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
//--------------------------------- username ---------------------------------------

$username_sql = mysql_query("SELECT name FROM usersinfo WHERE id = '".$userId."'") or die(mysql_error());
$username_result = mysql_fetch_assoc($username_sql);

$name = $username_result['name'];

//----------------------------------------------------------------------------------
if($answerid!='')
{
    if($action=='delete')
    {
        $deletequestion=mysql_query("DELETE From  answers  WHERE id=".$answerid."");
        echo json_encode(array("status"=>"1","message"=>"answer successfully deleted."));
        exit;
    }
    else
    {
        if($answer != "" && $userId != "")
        {
            $checkPoster=mysql_query("SELECT userId from answers  WHERE id=".$answerid."");
            if(mysql_num_rows($checkPoster)>0)
            {
                if($attachment!='')
                {
                    $attachment = str_replace('data:image/png;base64,', '', $attachment);
                    $attachment = str_replace(' ', '+', $attachment);
                    $data = base64_decode($attachment);
                    $fname = time()."_image.png";
                    
                    $success = file_put_contents($file, $data);
                    $file = UPLOAD_DIR.$fname;
                    $success = file_put_contents($file, $data);
                    $success ? $file : 'Unable to save the file.';
                    $serverName=$_SERVER['SERVER_NAME']."/question_app/answer_images/";
                    $filename=$serverName.$fname;
                    //$thumb = UPLOAD_DIR_THUMB.$name;
                    
                    $thumb=createImage($file,450,$fname);
                    
                }
                else
                {
                    $filename='';
                }
                if($filename!='')
                {
                    
                    
                    $updateanswer=mysql_query("UPDATE answers set answer='".$answer."',answer_date='".$answer_date."',attachment='".$filename."',thumb='".$_SERVER['SERVER_NAME']."/question_app/answer_images/thumbs/".$thumb."' WHERE id=".$answerid."");
                    echo json_encode(array("status"=>"1","message"=>"answer successfully updated."));
                }
                else
                {
                    $updateanswer=mysql_query("UPDATE answers set answer='".$answer."',answer_date='".$answer_date."' WHERE id=".$answerid."");
                    echo json_encode(array("status"=>"1","message"=>"answer successfully updated."));
                    exit;
                }
                
                
            }
            else
            {
                echo json_encode(array("status"=>"0","message"=>"You can not update this answer."));
                exit;
            }
                
        }
        else
        {
            echo json_encode(array("status"=>"0","message"=>"Error."));
            exit;
        }
    
    
    }

}
else
    {
  if($answer != "" && $questionId != ""  && $userId != "" && $answer_date != "" && $status!="")
	{
        
        $update="update questions set question_date_update=now(),point=point+1 where id=".$questionId;
        mysql_query($update) or die(mysql_error());
        
        $date = date('Y-m-d h:i:s A');
        $sql = "SELECT name,id FROM usersinfo WHERE id = (SELECT userId FROM questions WHERE id = ".$questionId.") ";
        $result = mysql_query($sql) or die(mysql_error());
        
        while($row = mysql_fetch_assoc($result))
        {
           
            $question_creator_id = $row['id'];
            
            
        }
        
        $pushNotifycationType="replay question";
        $parmaster=$questionId;
        $message=$name.' replied to your post';
         if($question_creator_id!=$userId)
            $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$userId.",".$question_creator_id.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());
        
        
		if($attachment!='')
			{
				$attachment = str_replace('data:image/png;base64,', '', $attachment);
				$attachment = str_replace(' ', '+', $attachment);
				$data = base64_decode($attachment);
				$fname = time()."_image.png";
										
				$success = file_put_contents($file, $data);
				$file = UPLOAD_DIR.$fname;
				$success = file_put_contents($file, $data);
				$success ? $file : 'Unable to save the file.';
				$serverName=$_SERVER['SERVER_NAME']."/question_app/answer_images/";
				$filename=$serverName.$fname;
				//$thumb = UPLOAD_DIR_THUMB.$name;
				
				$thumb=createImage($file,450,$fname);
				/*if(strpos($thumb,'png') === false || strpos($thumb,'jpg') === false) {
					$thumb = 'no thumb' ;
				}*/
			}
		else
			{
				$filename='';
			}
		$addanswer=mysql_query("INSERT INTO answers(answer,questionId,userId,answer_date,attachment,status,thumb,entity) VALUES('".mysql_real_escape_string($answer)."',".$questionId.",".$userId.",'".$answer_date."','".$filename."','".$status."','".$_SERVER['SERVER_NAME']."/question_app/answer_images/thumbs/".$thumb."','$entity')") or die(mysql_error());
		
		$insert_id = mysql_insert_id();
        $inforanswer= "select (MAX(orderindex)+1) as maxid from answers";
        $result = mysql_query($inforanswer);
        while($row = mysql_fetch_assoc($result))
        {
            $maxidanswer = $row['maxid'];


            
        }

        
         $updateanswer=mysql_query("UPDATE answers set orderindex=".$maxidanswer." WHERE id=".$insert_id."");
        
        
		//------------------------------------- push notification -------------------------------
		
		if($insert_id){
		 $notification_query = "SELECT device_token ,type FROM device_token_tb where user_id != '".$userId."' AND  (user_id = (SELECT userId FROM  questions WHERE id = '".$questionId."') OR user_id IN (SELECT userId FROM answers WHERE questionId = '".$questionId."')) ";
			$notification_record = mysql_query($notification_query) or die(mysql_error());
			if(mysql_num_rows($notification_record) > 0)
			{
			    $i=0;
			 	while($row_notification = mysql_fetch_assoc($notification_record))
				{
                    $badge=1;
					//$name = $row_notification['user_name'];
					//$message = $name.' answered a question';
					//$deviceToken[$i] = $row_notification['device_token'];
					//$deviceToken = $row_notification['device_token'];
					$type[$i] = $row_notification['type'];
					
						if($type[$i]==0){ 
						  $deviceToken[$i] = $row_notification['device_token'];		 
						  $apple = send_app_push_notification($deviceToken[$i], $questionId, $message, $badge);
							
						}
						else{
							$deviceToken[$i] = $row_notification['device_token'];
							$android = send_push_notification($deviceToken[$i], $questionId, $message);
						}
					

					$i++;
				}
			}
		}
		//---------------------------------------------------------------------------------------
		echo json_encode(array("status"=>"1","message"=>"Your answer is recorded."));
		exit;
	}
else	
	{
		echo json_encode(array("status"=>"0","message"=>"Error."));
		exit;
	}
}
?>

<?php
function  createImage( $upload, $newWidth , $file_name){
	
	if (!$info = getimagesize($upload) )
	return false;
	
	//create crude aspect ratio:
	$aspect = $info[0] / $info[1];
	//$newHeight = round( $newWidth/$aspect );
	
	
	$newHeight = floor( $info[1] * ( $newWidth / $info[0] ) );
	$mimetype=$info['2'];
	
	if( $mimetype == 2 )
	$src = @imagecreatefromjpeg("$upload");
	elseif( $mimetype == 1 )
	$src = @imagecreatefromgif("$upload");
	elseif( $mimetype == 3 )
	$src = @imagecreatefrompng("$upload");
	
	if ( !$src )
	return false;
	//echo $src;
	
	$tmp = @imagecreatetruecolor( $newWidth, $newHeight );
	imagecopyresampled( $tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $info[0], $info[1] );
	 // save thumbnail into a file
			$t = 'thumb_'.$file_name;
			imagejpeg( $tmp, UPLOAD_DIR_THUMB.$t );
			//$b = "INSERT INTO `tb_thumbnail` (`album_id`,`thumbnail`) VALUES ('".$aid."','".$t."')";
			//$datathumb = mysql_query($b);
			return $t;
	}

?>