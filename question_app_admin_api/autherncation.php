




<?php
   header('Access-Control-Allow-Origin: *');
     include('database/connection.php');
     $token = '';
  $headers = apache_request_headers();
  if(isset($headers['Authorization'])){
    $matches = array();
   // preg_match('/Token token="(.*)"/', $headers['Authorization'], $matches);
  //  if(isset($matches[1])){
   //   $token = $matches[1];
  //  }

    $token= $headers['Authorization'];
  }

    $userinfor= array();
    // echo 'tokent: '.$token;
    // echo "token:". $token;
  //
 //                                                               VXZQSmRPNnlwcldLYWM3N1dydjBkUzlGeUNmUE1VUlM5VVA4SHRobFBOQkIxWEhtL0o0cU9UNGVleXVzK1MxMTduZXJoK2p0bG5US09acThVUDFFL1BBQmlLcy85OXM4UzFiQ2Zoc2pKODlSMjhaQUhDU0wrU21VbG40VnU3Qk8=
     $checkfortoken=mysql_query("select username,userId,isadmin from  tbl_admin where token <> '' and token='$token' and expireddate >SUBDATE(timestamp(now()), INTERVAL 48 HOUR) ") or die(mysql_error());
    if( mysql_num_rows($checkfortoken)<=0)
    {
       echo json_encode(array("status"=>"-2","message"=>"invalid token"));
       exit;

    }
    else
    {
        $userinfor[]=mysql_fetch_array($checkfortoken);
        
    }

?>