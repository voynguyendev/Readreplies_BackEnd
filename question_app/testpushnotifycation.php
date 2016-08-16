

<?php
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

       // return;
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
    function nicetime($date)
    {
        if(empty($date)) {
            return "No date provided";
        }
          echo $date;
        $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths         = array("60","60","24","7","4.35","12","10");
        
        $now             = time();
        $unix_date         = strtotime($date);
        
        // check validity of date
        if(empty($unix_date)) {
            return "Bad date";
        }
        
        // is it future date or past date
        if($now > $unix_date) {
            $difference     = $now - $unix_date;
            $tense         = "ago";
            
        } else {
            $difference     = $unix_date - $now;
            $tense         = "from now";
        }

        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }
        
        $difference = round($difference);
        
        if($difference != 1) {
            $periods[$j].= "s";
        }
        
        return "$difference $periods[$j] {$tense}";
    }
    
    
    
    
    function GetDatetimefromtimezone($datetime,$timezone)
    {
        
        
        if($timezone=='null')
        {
            $timezone='America/Los_Angeles';
        }
        $date = new DateTime($datetime, new DateTimeZone(date_default_timezone_get()));
          echo $date->format('Y-m-d h:i:s A') . "\n";
        
    //    $date->setTimezone(new DateTimeZone($timezone));
        echo $date->format('Y-m-d h:i:s A') . "\n";
        return nicetime($date->format('Y-m-d h:i:s A'));
    }
    
       send_app_push_notification("2348f38006a09a523ef1116b5b556c0772468aaac9b197ba04a7a2d02d75311f", "test", "test notifycation", 1);
    
    
//echo GetDatetimefromtimezone('2015-09-26 08:08:41 AM','Asia/Ho_Chi_Minh');
?>


<h2>Push notifycation for request friend and accept friend </h2>

<form method="post" action="http://108.175.148.221/question_app/sendFriendRequest.php">
status: <input type="text" name="status" value="3">

<br><br>
senderid: <input type="text" name="sender_id" value="">
<br><br>
receiverId: <input type="text" name="receiver_id" value="">

<br><br>
pushnotifycationid: <input type="text" name="pushnotifycationid" value="">


<br><br>
<input type="submit" name="submit" value="Submit">
</form>


<h2>Push notifycation for accept reply </h2>

<form method="post" action="http://108.175.148.221/question_app/sendFriendRequest.php">
status: <input type="text" name="status" value="3">

<br><br>
senderid: <input type="text" name="logged_in_user_id" value="">
<br><br>
receiverId: <input type="text" name="receiver_id" value="">

<br><br>
answer_id: <input type="text" name="answer_id" value="">

<br><br>
status: <input type="text" name="status" value=""><br><br>


<input type="submit" name="submit" value="Submit">
</form>