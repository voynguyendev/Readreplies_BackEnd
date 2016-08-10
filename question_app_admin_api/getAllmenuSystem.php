<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
    include('autherncation.php');

     $roleid=isset($_REQUEST['roleid'])?$_REQUEST['roleid']:'';
     $sqlmenu=mysql_query("select * from  tbl_menu where ('$roleid'='' or menuid in (select menuid from tbl_roles_menus where roleid='$roleid' )) order by menuid DESC ") or die(mysql_error());
     $menus=[];
     $i=0;
     if(mysql_num_rows($sqlmenu)>0)
     {
         while($row1=mysql_fetch_array($sqlmenu))
         {
           $menus[$i]["menuid"]=   $row1["menuid"]   ;
           $menus[$i]["name"]=   $row1["name"]   ;
           $i++;
         }
     }

     echo json_encode(array("status"=>"1","message"=>"successfully",
     "menus"=>$menus
     ));

?>