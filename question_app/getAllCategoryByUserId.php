<?php
include('database/connection.php');

//echo json_encode(array("status"=>"0","message"=>"No categories found."));
//exit;
include('checktoken.php');

$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';



if($user_id!='')
	{
		$getCategories=mysql_query("SELECT * FROM categories WHERE userid=0 or userid='".$user_id."'") or die(mysql_error());
				
				$categories_data=array();
				$i=0;		
				if(mysql_num_rows($getCategories)>0)
					{
						while($row=mysql_fetch_array($getCategories))
							{
								$categories_data[$i]['id']=$row['id'];
								$categories_data[$i]['userid']=$row['userid'];
								$categories_data[$i]['category_name']=$row['category_name'];
								$categories_data[$i]['hashtag']=$row['hashtag'];
								$categories_data[$i++]['TagFriends']=$row['TagFriends'];						
		
							}
					}
				
				if(count($categories_data)>0)
					{
						echo json_encode(array("status"=>"1","message"=>"successfully","data"=>$categories_data));
						exit;
					}
				else	
					{
						echo json_encode(array("status"=>"0","message"=>"No categories found."));
						exit;
					}
		
		
		
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"Please provide user id."));
		exit;
	}


?>

