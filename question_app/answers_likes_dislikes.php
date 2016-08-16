<?php



/*include("database/connection.php");



if($_REQUEST['answer_id'] !="" && $_REQUEST['verdict'] !="")
{
	$a_id = $_REQUEST['answer_id'];
	$getA_id = mysql_query("SELECT * FROM answers WHERE id = '".$a_id."'") or die(mysql_error());
	if(mysql_num_rows($getA_id) > 0)
	{
		$verdict = $_REQUEST['verdict'];
		if($verdict == 0)
		{
			$updateLikes = mysql_query("UPDATE answers SET likes=likes + 1 WHERE id = '".$a_id."'") or die(mysql_error());
			if($updateLikes)
			{
				echo json_encode(array("message"=>"likes updated","success"=>1));
				exit;
			}
		}
		else if($verdict == 1)
		{
			$updateDislikes = mysql_query("UPDATE answers SET dislikes=dislikes + 1 WHERE id = '".$a_id."'") or die(mysql_error());
			if($updateDislikes)
			{
				echo json_encode(array("message"=>"dislikes updated","success"=>1));
				exit;
			}
		}
		else
		{
			echo json_encode(array("message"=>"garbage value","success"=>0));
			exit;	
		}
	}
	else{
		echo json_encode(array("message"=>"Incorrect info","success"=>0));
		exit;
	}
}
else{
	echo json_encode(array("message"=>"Incorrect info","success"=>0));
	exit;	
}*/
?>
<?php

include("database/connection.php");
include('checktoken.php');

if(trim($_REQUEST['answer_id'])!="" && trim($_REQUEST['verdict'])!="" && trim($_REQUEST['user_id'])!='')
{
	$u_id = $_REQUEST['user_id'];
	$a_id = $_REQUEST['answer_id'];
    
    $sql = "SELECT questionid FROM answers WHERE id = ".$a_id."";
    $result = mysql_query($sql) or die(mysql_error());
    
    while($row = mysql_fetch_assoc($result))
    {
        $question_id = $row['questionid'];
        
        
    }
    
    $update="update questions set question_date_update=now(),point=point+1 where id=".$question_id;
    mysql_query($update) or die(mysql_error());

    
    
	$getA_id = mysql_query("SELECT * FROM answers WHERE id = '".$a_id."'") or die(mysql_error());
	if(mysql_num_rows($getA_id) > 0)
	{
		$verdict = $_REQUEST['verdict'];  
		if($verdict == 1)
		{
			$updateLikes = mysql_query("UPDATE answers SET likes=likes + 1 WHERE id = '".$a_id."'") or die(mysql_error());
			if($updateLikes)
			{
				$checklike_dislike=mysql_query("select id from answer_like_dislike where user_id='".$u_id."' AND answer_id ='".$a_id."'") or die(mysql_error());
				if(mysql_num_rows($checklike_dislike) == 0)
				{
					$insertlike_dislike=mysql_query("insert into answer_like_dislike(user_id,answer_id,status) values('$u_id','$a_id','$verdict')") or die(mysql_error());
					if($insertlike_dislike)
					{
						echo json_encode(array("message"=>"likes updated","success"=>1));
						exit;
					}
				}
				else{
					echo json_encode(array("message"=>"Already liked or disliked","success"=>0));
					exit;	
					
				}
			}
		}
		else if($verdict == 0)
		{
			$updateDislikes = mysql_query("UPDATE answers SET dislikes=dislikes + 1 WHERE id = '".$a_id."'") or die(mysql_error());
			if($updateDislikes)
			{
				$checklike_dislike=mysql_query("select id from answer_like_dislike where user_id='".$u_id."' AND answer_id ='".$a_id."'") or die(mysql_error());
				if(mysql_num_rows($checklike_dislike) == 0)
				{
					$insertlike_dislike=mysql_query("insert into answer_like_dislike(user_id,answer_id,status) values('$u_id','$a_id','$verdict')") or die(mysql_error());
					if($insertlike_dislike)
					{
						echo json_encode(array("message"=>"dislikes updated","success"=>1));
						exit;
					}
				}
				else{
					echo json_encode(array("message"=>"Already liked or disliked","success"=>0));
					exit;	
					
				}
			}
		}
		else
		{
			echo json_encode(array("message"=>"garbage value","success"=>0));
			exit;	
		}
	}
	else{
		echo json_encode(array("message"=>"Incorrect info","success"=>0));
		exit;
	}
}
else{
	echo json_encode(array("message"=>"Incorrect info","success"=>0));
	exit;	
}


?>