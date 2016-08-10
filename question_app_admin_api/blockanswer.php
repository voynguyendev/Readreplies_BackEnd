<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');
     $namemenu="BLOCK_COMMENT";
     include('authorization.php');
     $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
     $answerid=isset($_REQUEST['answerid'])?$_REQUEST['answerid']:'';
     $block=isset($_REQUEST['block'])?$_REQUEST['block']:'';

     $updateuserinfors=mysql_query("UPDATE  answers SET isblock='$block' WHERE id='$answerid'") or die(mysql_error());

     echo json_encode(array("status"=>"1","message"=>"successfully"));

?>