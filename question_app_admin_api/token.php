 <?php
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

   function encrypttokent($string) {
    $token="";
    foreach (getallheaders() as $name => $value) {
    if($name=="Authorization") {
          $token=$value;
          break;
    }
	}
     return  $token ;
}

 ?>

<?php
     header('Access-Control-Allow-Origin: *');  
    include('database/connection.php');
    $username=isset($_REQUEST['username'])?$_REQUEST['username']:'';
    $password=isset($_REQUEST['password'])?$_REQUEST['password']:'';
    $password=encrypt_decrypt("encrypt",$password);
   // echo  $password;
    $checkforlogin=mysql_query("select * from  tbl_admin where username='$username' and password='$password' ") or die(mysql_error());

    if( mysql_num_rows($checkforlogin)<=0)
    {
       echo json_encode(array("status"=>"0","message"=>"plese check your username or password"));
       exit;

    }
    else
    {
   //   $newtokent = random_bytes(32);
    $newtokent= $username;
    for($i=0;$i<3;$i++)
      $newtokent = encrypttokent($newtokent);
     // $password = $generator->generateString(26, $username);
      
      $updatetokent=mysql_query("update  tbl_admin set token='$newtokent' where username='$username' and password='$password' ") or die(mysql_error());
       echo json_encode(array("status"=>"1","message"=>"successfully","tokent"=>$newtokent));
    }

?>