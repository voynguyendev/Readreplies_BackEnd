<?php

include('database/connection.php');
 $email = isset($_REQUEST['email'])?$_REQUEST['email']:""; 
 $password = isset($_REQUEST['password'])?$_REQUEST['password']:""; 

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
 $encrypted_passwordtxt = encrypt_decrypt('encrypt', $password);

if($email !="" && $password!=""  )
{		
	$checkEmail = mysql_query("SELECT id,password FROM usersinfo WHERE email ='".$email."'"); 
	if(mysql_num_rows($checkEmail) > 0)
	{
		
		$row=mysql_fetch_assoc($checkEmail);
		$id = $row['id'];	
		$query="UPDATE usersinfo SET password='".$encrypted_passwordtxt."' WHERE id=".$id.""; 
		$result = mysql_query($query) or die(mysql_error());
		if($result)
					{					 
						echo json_encode(array("status"=>"1","message"=>"successfully updated"));
						exit;
					}

				else
					{   
						
						echo json_encode(array("status"=>"0","message"=>"could not be updated")) ;
						exit;
					}
	}
	else{

		echo json_encode(array("status"=>0,"message"=>"email does not exists"));
		exit;
	
	}
	
}
else{

	echo json_encode(array("status"=>0,"message"=>"Invalid data"));
	exit;
	
}

?>