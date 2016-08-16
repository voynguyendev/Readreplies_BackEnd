<?php

include('database/connection.php');


function encrypt_decrypt($action, $string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'This is my secret key';
    $secret_iv = 'This is my secret iv';
    $key = hash('sha256', $secret_key);
    
   
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}



if($_REQUEST['email'] !="")
{
	$email = $_REQUEST['email'];
	
	
	$checkEmail = mysql_query("SELECT id,password FROM usersinfo WHERE email = '$email' AND fbid = 0 AND gmailid = 0"); 
	if(mysql_num_rows($checkEmail) > 0)
	{
		$row=mysql_fetch_assoc($checkEmail);
		$id = $row['id'];	
		$password = $row['password'];
		
		if($id!=''){
			$decrypted_txt = encrypt_decrypt('decrypt', $password);
			//$updatePassword =  mysql_query("update usersinfo SET password = '$encrypted_txt' WHERE id = '$id'"); 
			
		//if($updatePassword){
			
				$to = $email;
				$msg = 'Your password is : '.$decrypted_txt;
				$subject ='Qbox - Your password';
				$send = mail($to,$subject,$msg);
				if($send){
				echo json_encode(array("success"=>1,"message"=>"password reset successful"));
				
				exit;
				}
				else{
					echo json_encode(array("success"=>0,"message"=>"email sending failed"));
					exit;
						
				}
			//}

		}
		/*else{
			echo json_encode(array("success"=>0,"message"=>"email does not exists"));
			exit;
				
		}*/
	}
	else{

		echo json_encode(array("success"=>0,"message"=>"email does not exists"));
		exit;
	
	}
	
}
else{

	echo json_encode(array("success"=>0,"message"=>"Invalid data"));
	exit;
	
}

?>