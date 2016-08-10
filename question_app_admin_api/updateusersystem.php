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
     $namemenu="EDIT_USER_SYSTEM";
     include('authorization.php');
     $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
     $username=isset($_REQUEST['username'])?$_REQUEST['username']:'';
     $password=isset($_REQUEST['password'])?$_REQUEST['password']:'';
     $roleids=isset($_REQUEST['roleids'])?$_REQUEST['roleids']:'';
     $password=encrypt_decrypt("encrypt",$password);
     if($userid!="")
     {
        $sqlcheckexitsuser=mysql_query("select * from tbl_admin where username='$username' and   userId <> '$userid'");
         if( mysql_num_rows($sqlcheckexitsuser)>0)
         {
             echo json_encode(array("status"=>"0","message"=>"username  exist please fill different username" ));
             return;
         }
        $updaterole=mysql_query("update tbl_admin set username='$username',password='$password' where userid='$userid'") or die(mysql_error());
        $deleteroleuser=  mysql_query("DELETE FROM  tbl_users_roles where userid='$userid'") or die(mysql_error());

     }
     else
     {
         $sqlcheckexitsuser=mysql_query("select * from tbl_admin where username='$username'");
         if( mysql_num_rows($sqlcheckexitsuser)>0)
         {
             echo json_encode(array("status"=>"0","message"=>"username exist please fill different username" ));
             return;
         }
       //  $insertuserinfors==mysql_query("insert into usersinfo (disabled,email,password,lname,name) values('$disabled','$email','$password','$lname','$fname' ") or die(mysql_error());
          	$query="INSERT INTO tbl_admin(username,password,token) VALUES('$username','$password','')";
            $result = mysql_query($query) or die(mysql_error());
            $userid = mysql_insert_id();

      }

      if($roleids!="")
            {
                  $arrroleids=explode(",",$roleids);
                  foreach ($arrroleids as &$roleid) {
                     	 $query="INSERT INTO tbl_users_roles(userid,roleid) VALUES('$userid','$roleid')";
                         $result = mysql_query($query) or die(mysql_error());

                   }
            }



     echo json_encode(array("status"=>"1","message"=>"successfully" ));

?>