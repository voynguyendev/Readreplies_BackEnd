<?php

function send_push_notification($registation_ids, $message) {
       
 	define("GOOGLE_API_KEY","AIzaSyDkeHl6jszT59Jm1R3QjnphmlsrENMzHwc");
	//define("GOOGLE_API_KEY","AIzaSyC-Xb6lUZs87N2G774r-EqEhU0QU1xwAiA");
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
$registation_ids = 'APA91bF-0iF9aahE9GCUE2jc9cJtyXX9E3jlrRJ-zQwhaRylngZJm7vBOrDCsmZ_IvhYCgVvg5dNIKlS4KJ069VSfUrqOvbWyI7rPs2IqQa2b71EEzsQlDaEYTvI6niUl7FVsqSawrzUeCsbrZLjTGCMRwBFhHD26g';

$message = "u r gud";
  		/*$fields = array(
            'registration_ids' => $registation_ids,
            'data' => $message,
        );*/
		
		$fields = array(
			'registration_ids' => array($registation_ids),
			'data' => array("message" => $message)
		);
		echo "<pre>";
		print_r($fields);
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
		exit;
    }
?>	
<?php
$android = send_push_notification($registation_ids, $message);

?>	