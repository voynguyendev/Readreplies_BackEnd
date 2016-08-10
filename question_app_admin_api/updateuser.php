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
?>

<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');
     // password encrypt
      $namemenu="CREATE_EDIT_USER";
     include('authorization.php');
     $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
     $disabled=isset($_REQUEST['disabled'])?$_REQUEST['disabled']:'';
     $email=isset($_REQUEST['email'])?$_REQUEST['email']:'';
     $password=isset($_REQUEST['password'])?$_REQUEST['password']:'';
     $lname=isset($_REQUEST['lname'])?$_REQUEST['lname']:'';
     $fname=isset($_REQUEST['fname'])?$_REQUEST['fname']:'';
     $password = encrypt_decrypt('encrypt', $password);
     if($userid!="")
     {
        $sqlcheckexitsemail=mysql_query("select email from usersinfo where 	email='$email' and   id <> '$userid'");
         if( mysql_num_rows($sqlcheckexitsemail)>0)
         {
             echo json_encode(array("status"=>"0","message"=>"Email exist.please fill different email" ));
             return;
         }
        $updateuserinfors=mysql_query("update usersinfo set disabled='$disabled',email='$email',password='$password',lname='$lname',name='$fname' where id='$userid'") or die(mysql_error());

     }
     else
     {
           $sqlcheckexitsemail=mysql_query("select * from usersinfo where email='$email'");
         if( mysql_num_rows($sqlcheckexitsemail)>0)
         {
             echo json_encode(array("status"=>"0","message"=>"Email exist.please fill different email" ));
             return;
         }
      //  $insertuserinfors==mysql_query("insert into usersinfo (disabled,email,password,lname,name) values('$disabled','$email','$password','$lname','$fname' ") or die(mysql_error());
          	$query="INSERT INTO `usersinfo`(name,lname,email,password,disabled) VALUES('$fname','$lname','$email','$password','$disabled')";

	        $result = mysql_query($query) or die(mysql_error());

      }
     echo json_encode(array("status"=>"1","message"=>"successfully" ));

?>