
<?php
     include('autherncation');
     $namemenu=isset($_REQUEST['namemenu'])?$_REQUEST['namemenu']:'';
     $usercurrentid=$userinfor["id"];
     $checkforpermnissionmenu=mysql_query("select * from  tbl_roles_menus where roleid in (select roleid from tbl_users_roles where userid='$usercurrentid') and menuid in (select menuid from tbl_menu)) limit 1 ") or die(mysql_error());
     if( mysql_num_rows($checkforpermnissionmenu)<=0)
    {
       echo json_encode(array("status"=>"-1","message"=>"you don't have permission access this page"));
       exit;
      
    }
      else
    {
        	$userinfor=mysql_fetch_array($checkfortoken);
    }

?> 