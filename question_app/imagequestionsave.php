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
    return;
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
$imagequestionId=isset($_REQUEST['imagequestionId'])?$_REQUEST['imagequestionId']:'';
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
$guid=isset($_REQUEST['guid'])?$_REQUEST['guid']:'';
$attachment=isset($_REQUEST['attachment'])?$_REQUEST['attachment']:'';

$SERVERTest=$_SERVER['SERVER_NAME'];

$hashtag=isset($_REQUEST['hashtag'])?$_REQUEST['hashtag']:'';
///// CHECK WHETHER THE ACTION IS ADD OR EDIT OR DELETE question ///////////////

if($imagequestionId!='')
    {
        mysql_set_charset("UTF8");
        if($action=='delete')
            {
                $getimagequestion=mysql_query("SELECT * FROM imagesquestion WHERE imagequestionid='$imagequestionId'");
                $questionid="";
                  if(mysql_num_rows($getimagequestion)>0)
                 {
                    while($nrow=mysql_fetch_assoc($getimagequestion))
                    {
                         $questionid= $nrow["questionid"];
                    }
                }
                $updatequestion=mysql_query("DELETE FROM imagesquestion  WHERE imagequestionid=".$imagequestionId."");

                $getimagequestion=mysql_query("SELECT * FROM imagesquestion WHERE questionid='$questionid' order by imagequestionid limit 1");
                $urlimagenew="";
                $urlimagethumb="";
                if(mysql_num_rows($getimagequestion)>0)
                 {
                    while($nrow=mysql_fetch_assoc($getimagequestion))
                    {
                         $urlimagenew=  $nrow["image"];
                         $urlimagethumb=$nrow["imagethumb"];
                         break;
                    }
                }
                $updatequestion=mysql_query("update  questions set thumb='$urlimagethumb', attachment='$urlimagenew' WHERE id=".$questionid."");

                echo json_encode(array("status"=>"1","message"=>"image question successfully deleted."));

                exit;
            }
        else
            {


            }
    }
else
    {

        if($guid != "" && $attachment!="")
            {
                    $attachment = str_replace('data:image/png;base64,', '', $attachment);
                    $attachment = str_replace(' ', '+', $attachment);
                    $data = base64_decode($attachment);

                    $name = time()."_image.png";

                    $success = file_put_contents($file, $data);
                    $file = UPLOAD_DIR.$name;
                    $success = file_put_contents($file, $data);
                    $success ? $file : 'Unable to save the file.';

                    $serverName=$SERVERTest."/question_app/question_images/";
                    $filename=$serverName.$name;
                    $thumb=createImage($file,450,$name);

                     mysql_set_charset("UTF8");

                //echo $plusencodedquestion=str_replace("+","%20",$question);
                //echo "INSERT INTO questions(question,categoryId,subjectId,userId,question_date,attachment,thumb) VALUES('".$question."',".$category.",".$subjectId.",".$userId.",'".$question_date."','".$filename."','".$_SERVER['SERVER_NAME']."/question_app_test/question_images/thumbs/".$thumb."')";


                     $resultquery=mysql_query("INSERT INTO imagesquestion(idtemplate,image,imagethumb) VALUES('".$guid."','".$filename."','".$SERVERTest."/question_app/question_images/thumbs/".$thumb."')") or die(mysql_error());
                     $imagequestionid= mysql_insert_id();
                     echo json_encode(array("status"=>"1","imagequestionid"=>$imagequestionid,"ImageURL"=>$SERVERTest."/question_app/question_images/thumbs/".$thumb,"message"=>"image question successfully added."));
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