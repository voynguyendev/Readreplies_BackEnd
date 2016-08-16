<?php
include('database/connection.php');
include('checktoken.php');

$catId=isset($_REQUEST['catId'])?$_REQUEST['catId']:'';
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
$hastags=isset($_REQUEST['action'])?$_REQUEST['hastags']:'';
$userid=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';
$tabfriend=isset($_REQUEST['tabfriend'])?$_REQUEST['tabfriend']:'';





if(!isset($_REQUEST['category_name']))
	{
		$category_name='';
	}
else	
	{
		$category_name=trim($_REQUEST['category_name']);
	}
	
///// CHECK WHETHER THE ACTION IS ADD OR EDIT OR DELETE  CATEGORY ///////////////
	
if($catId!='')
	{
		if($action=='delete')
			{			
				$checkdeletecategorysystem=mysql_query("select * from   categories  WHERE userid=0 and id='".$catId. "'");
				$checkdeletecategory=mysql_query("select id from questions where id in (select questionid  FROM  categoryquestion  WHERE categoryid=".$catId.")");
			    if(mysql_num_rows($checkdeletecategory)>0)
				{
						echo json_encode(array("status"=>"0","message"=>"you can't delete category because it have questions"));
						exit;
					
				}
				else if(mysql_num_rows($checkdeletecategorysystem)>0)
				{
					
						echo json_encode(array("status"=>"0","message"=>"you can't delete category because it is system's category"));
						exit;
					
				}
				else
				{
					$updateCategory=mysql_query("DELETE FROM  categories  WHERE id=".$catId."");
					echo json_encode(array("status"=>"1","message"=>"Category successfully deleted."));
					exit;
				}
				
					
			}
		else
			{
				if($category_name != "")
					{
						$updateCategory=mysql_query("UPDATE categories set category_name='".$category_name."' WHERE id=".$catId."");
						echo json_encode(array("status"=>"1","message"=>"Category successfully updated."));
						exit;	
					}
				else	
					{
						echo json_encode(array("status"=>"0","message"=>"Category could not be updated."));
						exit;
					}
			}
	}
else
	{
		$checkCategory=mysql_query("SELECT * FROM categories WHERE category_name='".$category_name."' and ( userid =0 or userid= ". $userid .")" );
		if(mysql_num_rows($checkCategory)>0)
			{
				echo json_encode(array("status"=>"0","message"=>"Category already exists."));
				exit;
			}
		else
			{
				if($category_name != "")
					{
						$addCategory=mysql_query("INSERT INTO categories(category_name,hashtag,TagFriends,userid) VALUES('".$category_name."','".$hastags."','".$tabfriend."','".$userid.  "')");
						echo json_encode(array("status"=>"1","message"=>"Category successfully added."));
						exit;
					}
				else	
					{
						echo json_encode(array("status"=>"0","message"=>"Category could not be added."));
						exit;
					}
			}
	}






?>