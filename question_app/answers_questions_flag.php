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

if(trim($_REQUEST['entityid'])!=""  && trim($_REQUEST['entity'])!=""&& trim($_REQUEST['userid'])!='')
{
	$u_id = $_REQUEST['userid'];
	$entity_id = $_REQUEST['entityid'];
    $entity = $_REQUEST['entity'];
    $date = date('Y-m-d h:i:s A');
	$getA_id = mysql_query("SELECT * FROM flaganswerquestion WHERE userid = '".$u_id."' and entity='".$entity."' and entityid='".$entity_id."'") or die(mysql_error());
	if(mysql_num_rows($getA_id) <= 0)
	{
        $insertflag=mysql_query("insert into flaganswerquestion(userid,entityid,entity,dateflag) values('$u_id','$entity_id','$entity','$date')") or die(mysql_error());
        if($insertflag)
        {
            echo json_encode(array("message"=>"flag successfully","success"=>1));
            exit;
        }
	}
	else{
        echo json_encode(array("message"=>"Already Flag","success"=>0));
        exit;

	}
}
else{
	echo json_encode(array("message"=>"Incorrect info","success"=>0));
	exit;	
}


?>