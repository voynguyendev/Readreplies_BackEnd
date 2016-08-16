<?php

   /* $token = isset($_REQUEST['token'])?$_REQUEST['token']:'';
    $userIdchecktoken = isset($_REQUEST['userIdchecktoken'])?$_REQUEST['userIdchecktoken']:'';
	//echo $token;
	
	
	if( $token=='' || $userIdchecktoken=='')
	{
		echo json_encode(array("status"=>"-2","message"=>"invalid token"));		
		exit;
	}
	  	    
	   
	 	    
    $checkfortoken=mysql_query("select * from usersinfo where id='$userIdchecktoken' AND tokent='$token' and TokentExpire >SUBDATE(timestamp(now()), INTERVAL 0 HOUR) ");
    
      $checkfortokenempty=mysql_query("select * from usersinfo where id='$userIdchecktoken' AND tokent=''");
    
    if( mysql_num_rows($checkfortokenempty)>0)
    {
       echo json_encode(array("status"=>"-2","message"=>"invalid token"));
       exit;
    
    }

    if(mysql_num_rows($checkfortoken)<=0 )
    {
       echo json_encode(array("status"=>"-2","message"=>"invalid token"));
       exit;
    
    }
	*/
?>