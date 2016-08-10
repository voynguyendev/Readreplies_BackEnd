<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');
     $namemenu="BLOCK_POST";
     include('authorization.php');
     $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
     $questionid=isset($_REQUEST['questionid'])?$_REQUEST['questionid']:'';
     $block=isset($_REQUEST['block'])?$_REQUEST['block']:'';

     $updateuserinfors=mysql_query("UPDATE  questions SET isblock='$block' WHERE id='$questionid'") or die(mysql_error());

     echo json_encode(array("status"=>"1","message"=>"successfully"));

?>