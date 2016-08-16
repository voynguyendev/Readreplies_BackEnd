<?php
include('database/connection.php');
include('checktoken.php');
$answerId=isset($_REQUEST['answerId'])?$_REQUEST['answerId']:'';
$deleteanswer=mysql_query("DELETE FROM answers  WHERE id=".$answerId."");
echo json_encode(array("status"=>"1","message"=>"answer successfully deleted."));
exit;
?>