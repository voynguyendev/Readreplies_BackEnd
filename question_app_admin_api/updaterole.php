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
     $namemenu="CREATE_EDIT_ROLES";
     include('authorization.php');
     $roleid=isset($_REQUEST['roleid'])?$_REQUEST['roleid']:'';
     $rolename=isset($_REQUEST['rolename'])?$_REQUEST['rolename']:'';
     $description=isset($_REQUEST['description'])?$_REQUEST['description']:'';
     $menuids=isset($_REQUEST['menuids'])?$_REQUEST['menuids']:'';

     if($roleid!="")
     {
        $sqlcheckexitsrole=mysql_query("select * from tbl_roles where 	rolename='$rolename' and   roleid <> '$roleid'");
         if( mysql_num_rows($sqlcheckexitsrole)>0)
         {
             echo json_encode(array("status"=>"0","message"=>"role name exist.please fill different name" ));
             return;
         }
        $updaterole=mysql_query("update tbl_roles set rolename='$rolename',description='$description' where roleid='$roleid'") or die(mysql_error());
        $deleterolemenu=  mysql_query("DELETE FROM  tbl_roles_menus where roleid='$roleid'") or die(mysql_error());

     }
     else
     {
           $sqlcheckexitsrole=mysql_query("select * from tbl_roles where rolename='$rolename'");
         if( mysql_num_rows($sqlcheckexitsrole)>0)
         {
             echo json_encode(array("status"=>"0","message"=>"role name exist.please fill different name" ));
             return;
         }
       //  $insertuserinfors==mysql_query("insert into usersinfo (disabled,email,password,lname,name) values('$disabled','$email','$password','$lname','$fname' ") or die(mysql_error());
          	$query="INSERT INTO tbl_roles(rolename,description) VALUES('$rolename','$description')";
            $result = mysql_query($query) or die(mysql_error());
            $roleid = mysql_insert_id();


      }

      if($menuids!="")
            {
                  $arrmenyids=explode(",",$menuids);
                  foreach ($arrmenyids as &$menuid) {
                     	 $query="INSERT INTO tbl_roles_menus(menuid,roleid) VALUES('$menuid','$roleid')";
                         $result = mysql_query($query) or die(mysql_error());

                   }
            }



     echo json_encode(array("status"=>"1","message"=>"successfully" ));

?>