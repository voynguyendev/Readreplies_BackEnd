
<?php
     include('autherncation');
  
     $usercurrentid=$userinfor[0]["userId"];
          // echo json_encode(array("status"=>"-1","userinfor"=>$userinfor));
     $checkforpermnissionmenu=mysql_query("select * from  tbl_roles_menus where roleid in (select roleid from tbl_users_roles where userid='$usercurrentid') and menuid in (select menuid from tbl_menu where name='$namemenu') limit 1 ") or die(mysql_error());
     if( mysql_num_rows($checkforpermnissionmenu)<=0 && $userinfor[0]["isadmin"]!="1")
    {
       echo json_encode(array("status"=>"-1","message"=>"you don't have permission access this page"));
       exit;

    }


?>