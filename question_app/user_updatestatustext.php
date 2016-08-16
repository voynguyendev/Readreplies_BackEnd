<?php
include('database/connection.php');
include('checktoken.php');

$statustext=isset($_REQUEST['statustext'])?$_REQUEST['statustext']:"";
$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";


if($user_id!='')
	{

		$query="UPDATE usersinfo SET statustext='".$statustext."' WHERE id=".$user_id."";
                $result = mysql_query($query) or die(mysql_error());
	        if($result)
					{					 
						echo json_encode(array("status"=>"1","message"=>"successfully updated"));
						exit;
					}

				else
					{   
						
						echo json_encode(array("status"=>"0","message"=>"could not be updated")) ;
						exit;
					}
				
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"please provide user id")) ;
		exit;
	}

?>

