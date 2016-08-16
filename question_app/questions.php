<?php
include('database/connection.php');

//include('checktoken.php');

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


//mysql_set_charset("utf8");

define('UPLOAD_DIR', '../question_app/question_images/');
define('UPLOAD_DIR_THUMB', '../question_app/question_images/thumbs/');

$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';

$guidimage=isset($_REQUEST['guidimage'])?$_REQUEST['guidimage']:'';
$question=isset($_REQUEST['question'])?$_REQUEST['question']:'';
$categoryId=isset($_REQUEST['categoryId'])?$_REQUEST['categoryId']:'';
$subjectId=isset($_REQUEST['subjectId'])?$_REQUEST['subjectId']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';
//$question_date=isset($_REQUEST['date'])?$_REQUEST['date']:'';
$question_date= date('Y-m-d h:i:s A');
$attachment=isset($_REQUEST['attachment'])?$_REQUEST['attachment']:'';
$tagfriends=isset($_REQUEST['tagfriends'])?$_REQUEST['tagfriends']:'';
$SERVERTest=$_SERVER['SERVER_NAME'];
    
$hashtag=isset($_REQUEST['hashtag'])?$_REQUEST['hashtag']:'';
///// CHECK WHETHER THE ACTION IS ADD OR EDIT OR DELETE question ///////////////
  $date = date('Y-m-d h:i:s');
if($questionId!='')
	{
		mysql_set_charset("UTF8");
		if($action=='delete')
			{
				$updatequestion=mysql_query("DELETE FROM questions  WHERE id='".$questionId."'");
                $updateimagequestion=mysql_query("DELETE FROM imagesquestion  WHERE questionid='".$questionId."'");
				echo json_encode(array("status"=>"1","message"=>"question successfully deleted."));
				exit;
			}
		else
			{

				        if($question != "" && $categoryId != "" && $subjectId != "" && $userId != "" && $question_date != "" && $guidimage!="")
				     	{

                            //images question
                                $filename='';
                                $filenamethumb='';
                                $updateimagequestion=mysql_query("update imagesquestion set questionid='".$questionId. "' WHERE idtemplate='".$guidimage."'");
                                $selectquestionimage= mysql_query("select * from  imagesquestion  WHERE questionid='".$questionId."' order by imagequestionid limit 1")
                                 or die(mysql_error());

                                while($row = mysql_fetch_assoc($selectquestionimage))
                                {
                                    $filename = $row['image'];
                                    $filenamethumb= $row['imagethumb'];

                                }

                            //end

					       	    $updatequestion=mysql_query("UPDATE questions set question='".$question."',categoryId=".$categoryId.",subjectId=".$subjectId.",userId=".$userId.",hashtag='".$hashtag."',
                                userId=".$userId.",question_date='".$question_date."',attachment='".$filename."',thumb='".$filenamethumb."',question_date_update='".$date."' WHERE id=".$questionId."");

                                $categoryArr=explode(',',$categoryId);
                                $deletecategoryquestion=mysql_query("delete from categoryquestion where questionid=".$questionId);

                                foreach($categoryArr as $category)
                                {

                                    $addquestioncategoryy=mysql_query("INSERT INTO categoryquestion(questionid,categoryid) values(".$questionId.",".$category.")");
                                    
                                }
                                echo json_encode(array("status"=>"1","message"=>"question successfully updated."));

							}
						else	
							{
								echo json_encode(array("status"=>"0","message"=>"You can not update this question."));
								exit;
							}

			}
	}
else
	{
		         if($question != "" && $categoryId != "" && $subjectId != "" && $userId != ""  && $guidimage!="")
		                 	{

                          //images question
                                $filename='';
                                $filenamethumb='';

                                $selectquestionimage= mysql_query("select  * from  imagesquestion  WHERE idtemplate='".$guidimage."' order by imagequestionid limit 1")
                                 or die(mysql_error());

                                while($row = mysql_fetch_assoc($selectquestionimage))
                                {
                                   $filename = $row['image'];
                                   $filenamethumb= $row['imagethumb'];

                                }

                            //end

                mysql_set_charset("UTF8");

                //echo $plusencodedquestion=str_replace("+","%20",$question);
                //echo "INSERT INTO questions(question,categoryId,subjectId,userId,question_date,attachment,thumb) VALUES('".$question."',".$category.",".$subjectId.",".$userId.",'".$question_date."','".$filename."','".$_SERVER['SERVER_NAME']."/question_app_test/question_images/thumbs/".$thumb."')";


                $addquestion=mysql_query("INSERT INTO questions(question,tagfriend,subjectId,userId,question_date,attachment,thumb,hashtag,question_date_update) VALUES('".$question."','".$tagfriends."',".$subjectId.",".$userId.",'".$question_date."','".$filename."','".$filenamethumb."','".$hashtag."','".$question_date."')") or die(mysql_error());
                $questionId = mysql_insert_id();

               $updateimagequestion=mysql_query("update imagesquestion set questionid='". $questionId. "' WHERE idtemplate='".$guidimage."'");
                //$tagfriends="274";

                if($tagfriends!='')
                {
                    $sql = "SELECT name,id FROM usersinfo WHERE id = (SELECT userId FROM questions WHERE id = ".$questionId.") ";
                    $result = mysql_query($sql) or die(mysql_error());

                    while($row = mysql_fetch_assoc($result))
                    {
                        $question_creator_name = $row['name'];
                        $question_creator_id = $row['id'];

                    }


                    //push notifycation
                    $arrfriends=explode(",",$tagfriends);
                    foreach ($arrfriends as &$friendid) {
                        //$friendid=274;
                        //$friendid=isset($friendideach)?'':$friendideach;
                        //if($friendid=='')
                          //  continue;

                        $pushNotifycationType="tagged post";
                        $parmaster=$questionId;
                        $message=$question_creator_name.' tagged you in a post';

                       // $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$question_creator_id.",".$friendid.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());
                        $insertFriendRequest=mysql_query("INSERT INTO PushNotifycation(SenderId,ReceiverId,Isdelete,DateCreate,PushNotifycationType,Parmaster,Message) VALUES(".$question_creator_id.",".$friendid.",0,'$date','".$pushNotifycationType."',".$parmaster.",'".$message."')") or die(mysql_error());

                        $notification_query = "SELECT device_token,type FROM device_token_tb where user_id ='".$friendid."'";
                        $badge=1;
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
                                  	//push notifycation in android
								    $deviceToken[$i] = $row_frd['device_token'];  
									send_push_notification( $deviceToken[$i], $questionId,$message);
                                }
                                $i++;
                            } 
                            
                        }
                        
                        
                    }
                    
                }
                
                $categoryArr=explode(',',$categoryId);

                
				foreach($categoryArr as $category)
				{
					
                    $addquestioncategoryy=mysql_query("INSERT INTO categoryquestion(questionid,categoryid) values(".$questionId.",".$category.")");
					
				}
				//$addquestion=mysql_query("INSERT INTO questions(question,categoryId,subjectId,userId,question_date,attachment,thumb) VALUES('".mysql_real_escape_string($question)."',".$categoryId.",".$subjectId.",".$userId.",'".$question_date."','".$filename."','".$_SERVER['SERVER_NAME']."/question_app/question_images/thumbs/".$thumb."')") or die(mysql_error());
				
				echo json_encode(array("status"=>"1","message"=>"question successfully added."));
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
	$src = @imagecreatefromjpeg($upload);
	elseif( $mimetype == 1 )
	$src = @imagecreatefromgif($upload);
	elseif( $mimetype == 3 )
	$src = @imagecreatefrompng($upload);
	
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




