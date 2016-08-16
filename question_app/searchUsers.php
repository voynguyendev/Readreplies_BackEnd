<?php
include('database/connection.php');

include('checktoken.php');

$search_keyword=$_REQUEST['search_keyword'];
$user_id=$_REQUEST['user_id'];


$findUsersQuery=mysql_query("SELECT id,CONCAT(name,' ',lname) as name,email,mobile,school,grade,city,profile_pic,thumb,StatusText FROM usersinfo WHERE email LIKE '%".$search_keyword."%' OR  mobile LIKE '%".$search_keyword."%' OR  name LIKE '%".$search_keyword."%'") or die(mysql_error());

if(mysql_num_rows($findUsersQuery)>0)
	{
		$searchedUsers=array();
		$i=0;
		while($row=mysql_fetch_assoc($findUsersQuery))
			{				
				
				$checkForFriend=mysql_query("SELECT * FROM friendRequests WHERE ((sender_id=".$user_id." AND receiver_id=".$row['id'].") OR (receiver_id=".$user_id." AND sender_id=".$row['id']."))");
				$checkForFriendArr=mysql_fetch_assoc($checkForFriend);
				$searchedUsers[$i]=$row;
				
				if(mysql_num_rows($checkForFriend)>0)
					{
						$searchedUsers[$i]['friendStatus']=(string)$checkForFriendArr['status'];
						$searchedUsers[$i]['pastRequest']='1';
					}
				else
					{
						$searchedUsers[$i]['friendStatus']='0';
						$searchedUsers[$i]['pastRequest']='0';
					}
				
				$i++;
			}
		echo json_encode(array("success"=>"1","message"=>$searchedUsers));
		exit;
	}
else
	{
		echo json_encode(array("success"=>"0","message"=>"No user found matching to your keyword, please try with different keyword."));
		exit;
	}
