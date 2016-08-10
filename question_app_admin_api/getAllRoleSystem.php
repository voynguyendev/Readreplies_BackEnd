<?php
     header('Access-Control-Allow-Origin: *');
     header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
     include('database/connection.php');
     include('autherncation.php');
      $namemenu="CREATE_EDIT_ROLES";
     include('authorization.php');

     $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
     if($userid!="")
        $sqlrole=mysql_query("select r.*,(select count(*) from tbl_users_roles where roleid=r.roleid and userid='$userid' ) as countuser from  tbl_roles r order by r.roleid DESC ") or die(mysql_error());
     else
        $sqlrole=mysql_query("select r.*,0 as countuser from  tbl_roles r order by r.roleid DESC ") or die(mysql_error());
     $roles=[];
     $i=0;
     if(mysql_num_rows($sqlrole)>0)
     {
         while($row1=mysql_fetch_array($sqlrole))
         {
           $roles[$i]["roleid"]=   $row1["roleid"]   ;
           $roles[$i]["rolename"]=   $row1["rolename"]   ;
           $roles[$i]["description"]=   $row1["description"]   ;
           $roles[$i]["countuser"]=   $row1["countuser"] .""  ;
           $i++;
         }
     }

     echo json_encode(array("status"=>"1","message"=>"successfully",
     "roles"=>$roles
     ));

?>