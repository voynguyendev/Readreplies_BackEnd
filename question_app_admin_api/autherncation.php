
<?php

function getallheaders()
    {
           $headers = '';
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }


function GetTokenFromHeader()
{
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
     include('database/connection.php');
     $token=GetTokenFromHeader() ;
     $userinfor=[];
     echo "token:". $token;
     $checkfortoken=mysql_query("select * from  tbl_admin where token <> '' and token='$token' and expireddate >SUBDATE(timestamp(now()), INTERVAL 0 HOUR) ") or die(mysql_error());
    if( mysql_num_rows($checkfortoken)<=0)
    {
       echo json_encode(array("status"=>"0","message"=>"invalid token"));
       exit;

    }
      else
    {
        	$userinfor=mysql_fetch_array($checkfortoken);
    }

?>