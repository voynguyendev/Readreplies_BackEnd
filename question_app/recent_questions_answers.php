<?php

/*include('database/connection.php');

$gQdetail = array();
$gAdetail = array();
$gAAdetail = array();

//question data
$getQuestions = mysql_query("SELECT * FROM questions WHERE question_date >= SUBDATE(timestamp(now()), INTERVAL 24 HOUR)") or die(mysql_error());
if(mysql_num_rows($getQuestions) > 0)
{
	$i=0;
	while($getQuestionsData=mysql_fetch_assoc($getQuestions))
	{
		
		$gQdetail[$i] = $getQuestionsData;
		$i++;
	}
}
else{
		$gQdetail =  array();
}

//answer data
$getAnswers = mysql_query("SELECT * FROM answers WHERE answer_date >= SUBDATE(timestamp(now()), INTERVAL 24 HOUR)") or die(mysql_error());
if(mysql_num_rows($getAnswers ) > 0)
{
	$j=0;
	while($getAnswersData=mysql_fetch_assoc($getAnswers))
	{
		$gAdetail[$j] = $getAnswersData;
		$j++;
	}
}
else{
		$gAdetail =  array();
}

//acceptedanswer data
$getAcceptedanswers = mysql_query("SELECT * FROM answers WHERE accept_date >= SUBDATE(timestamp(now()), INTERVAL 24 HOUR)") or die(mysql_error());
if(mysql_num_rows($getAcceptedanswers) > 0)
{
	$k=0;	
	while($getAcceptedanswersData=mysql_fetch_assoc($getAcceptedanswers))
	{
		$gAAdetail[$k] = $getAcceptedanswersData;
		$k++;
	}
}
else{
		$gAAdetail =  array();
}

echo json_encode(array("question_data"=>$gQdetail,"answer_data"=>$gAdetail,"accepted_answer_data"=>$gAAdetail));
exit;
*/
?>
<?php

include('database/connection.php');
include('checktoken.php');

$gQdetail = array();
//$gAdetail = array();
//$gAAdetail = array();
$gQstdetail = array();

$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';


//question data(29 hours bcoz of server time difference)
//echo "SELECT * FROM questions WHERE question_date >= SUBDATE(timestamp(now()), INTERVAL 24 HOUR) AND userId=$user_id";

$getQuestions = mysql_query("SELECT * FROM questions WHERE question_date >= SUBDATE(timestamp(now()), INTERVAL 72 HOUR) AND userId=".$user_id."") or die(mysql_error());
if(mysql_num_rows($getQuestions) > 0)
{
	$i=0;
	while($getQuestionsData=mysql_fetch_assoc($getQuestions))
	{
		
		//$gQdetail[$i] = $getQuestionsData;
		$gQdetail[$i]['id'] = $getQuestionsData['id'];
		$gQdetail[$i]['question'] = $getQuestionsData['question'];
		$gQdetail[$i]['categoryId'] = $getQuestionsData['categoryId'];
		$gQdetail[$i]['subjectId'] = $getQuestionsData['subjectId'];
		$gQdetail[$i]['userId'] = $getQuestionsData['userId'];
		$gQdetail[$i]['likes'] = $getQuestionsData['likes'];
		$gQdetail[$i]['dislikes'] = $getQuestionsData['dislikes'];
		$gQdetail[$i]['attachment'] = $getQuestionsData['attachment'];
		$gQdetail[$i]['question_date'] = $getQuestionsData['question_date'];
		$gQdetail[$i]['thumb'] = $getQuestionsData['thumb'];
		
		$userNameData=mysql_query("SELECT name FROM usersinfo WHERE id = '".$getQuestionsData['userId']."'") or die(mysql_error());
		$userName=mysql_fetch_assoc($userNameData);
		$gQdetail[$i]['name'] = $userName['name'];
		$i++;
	}
	
	//echo "<pre>";print_r($gQdetail);
}
else{
		$gQdetail =  array();
}

//answer data
//echo "<br>";
//echo "SELECT questionId FROM answers WHERE (answer_date >= SUBDATE(timestamp(now()), INTERVAL 24 HOUR) AND userId=$user_id) OR accept_date >= SUBDATE(timestamp(now()), INTERVAL 24 HOUR) ";

$getAnswers = mysql_query("SELECT questionId FROM answers WHERE (answer_date >= SUBDATE(timestamp(now()), INTERVAL 72 HOUR) AND userId=".$user_id.") OR accept_date >= SUBDATE(timestamp(now()), INTERVAL 72 HOUR) ") or die(mysql_error());
if(mysql_num_rows($getAnswers ) > 0)
{
	$j=0;
	while($getAnswersData=mysql_fetch_assoc($getAnswers))
	{
		//$gAdetail[$j] = $getAnswersData['questionId'];
		
		//echo "SELECT * FROM questions WHERE id = '".$getAnswersData['questionId']."'";
		$selectQst = mysql_query("SELECT * FROM questions WHERE id = '".$getAnswersData['questionId']."'") or die(mysql_error());
		if(mysql_num_rows($getQuestions) > 0)
		{
			while($getQstData=mysql_fetch_assoc($selectQst))
			{
				//$gQstdetail[$j] = $getQstData;
				$gQstdetail[$j]['id'] = $getQstData['id'];
				$gQstdetail[$j]['question'] = $getQstData['question'];
				$gQstdetail[$j]['categoryId'] = $getQstData['categoryId'];
				$gQstdetail[$j]['subjectId'] = $getQstData['subjectId'];
				$gQstdetail[$j]['userId'] = $getQstData['userId'];
				$gQstdetail[$j]['likes'] = $getQstData['likes'];
				$gQstdetail[$j]['dislikes'] = $getQstData['dislikes'];
				$gQstdetail[$j]['attachment'] = $getQstData['attachment'];
				$gQstdetail[$j]['question_date'] = $getQstData['question_date'];
				$gQstdetail[$j]['thumb'] = $getQstData['thumb'];
				
				$userNameData=mysql_query("SELECT name FROM usersinfo WHERE id = '".$getQstData['userId']."'") or die(mysql_error());
				$userName=mysql_fetch_assoc($userNameData);
				$gQstdetail[$j]['name'] = $userName['name'];
			}	
		}
		$j++;
	}
	
	//echo "<pre>";print_r($gQstdetail);
	//echo "hii";
}
else{
		$gQstdetail =  array();
}

//$data = array_unique(array($gQdetail,$gQstdetail));

$data = array_merge(array_unique($gQdetail),array_unique($gQstdetail));
//echo "<pre>";print_r($data);exit;

echo json_encode(array("question_data"=>$data,"success"=>1));
exit;


?>