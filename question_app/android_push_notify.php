<?php

function send_push_notification($registation_ids, $message) {
     
	   
 		//define("GOOGLE_API_KEY","AIzaSyAht6wp1UVIFsuf616-Q4kBhSN7ZXDNeIU");
		//qbox	
		define("GOOGLE_API_KEY","AIzaSyAht6wp1UVIFsuf616-Q4kBhSN7ZXDNeIU");
        $url = 'https://android.googleapis.com/gcm/send';
		
  		/*$fields = array(
            'registration_ids' => $registation_ids,
            'data' => $message,
        );*/
		
		$fields = array(
			'registration_ids' => array($registation_ids),
			'data' => array("message" => $message)
		);
		

        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
      
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
		//print_r($result); exit;
		if ($result === FALSE) {
           // die('Curl failed: ' . curl_error($ch));
		   echo "something is wrong";
        }
 		
        // Close connection
        curl_close($ch);
        //echo $result;
		//exit;
    }
?>	
<?php

if($_REQUEST['registation_ids'] != '')
{
	$registation_ids = $_REQUEST['registation_ids'];
	//$registation_ids = 'APA91bF-0iF9aahE9GCUE2jc9cJtyXX9E3jlrRJ-zQwhaRylngZJm7vBOrDCsmZ_IvhYCgVvg5dNIKlS4KJ069VSfUrqOvbWyI7rPs2IqQa2b71EEzsQlDaEYTvI6niUl7FVsqSawrzUeCsbrZLjTGCMRwBFhHD26g';
	$message = "this is hello message ";

/*$registation_ids = explode(',',$registation_ids);

foreach($registation_ids as $registation_id)
{	 
	$android = send_push_notification($registation_id, $message);
}*/

	$android = send_push_notification($registation_ids, $message);
	echo json_encode(array("push"=>"push notification sent"));
	exit;
}
else
{
	echo json_encode(array('msg'=>'provide relevant data'));	
	exit;
}
?>	