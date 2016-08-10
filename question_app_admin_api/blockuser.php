<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');

     $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
     $disabled=isset($_REQUEST['disabled'])?$_REQUEST['disabled']:'';
     $updateuserinfors=mysql_query("UPDATE  usersinfo SET disabled='$disabled' WHERE id='$userid'") or die(mysql_error());

     echo json_encode(array("status"=>"1","message"=>"successfully"
     ));

?>