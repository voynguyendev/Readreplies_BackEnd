<?php


        
        
        function nicetime($date)
        {
            if(empty($date)) {
                return "No date provided";
            }
            
            $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
            $lengths         = array("60","60","24","7","4.35","12","10");
            
            $now             = time();
            $unix_date         = strtotime($date);
            
            // check validity of date
            if(empty($unix_date)) {
                return "Bad date";
            }
            
            // is it future date or past date
            if($now > $unix_date) {
                $difference     = $now - $unix_date;
                $tense         = "ago";
                
            } else {
                $difference     = $unix_date - $now;
                $tense         = "from now";
            }
            
            for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
                $difference /= $lengths[$j];
            }
            
            $difference = round($difference);
            
            if($difference != 1) {
                $periods[$j].= "s";
            }
            
            return "$difference $periods[$j] {$tense}";
        }
        
        
        
        
        function GetDatetimefromtimezone($datetime,$timezone)
        {
            
            if($timezone=='null')
            {
                $timezone='America/Los_Angeles';
            }
            
            $date = new DateTime($datetime, new DateTimeZone(date_default_timezone_get()));
            //  echo $date->format('Y-m-d h:i:s A') . "\n";
            
           // $date->setTimezone(new DateTimeZone($timezone));
            //echo $date->format('Y-m-d h:i:s A') . "\n";
            return nicetime($date->format('Y-m-d h:i:s A'));
        }
        
        
        
   
    
    
include('database/connection.php');
include('checktoken.php');
$server = $_SERVER['HTTP_HOST'];
$demo_thumb_url = $server.'/question_app/question_images/thumbs/';

$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';
        
        $getuserinfor=mysql_query("SELECT * FROM usersinfo WHERE id=".$userId."");
        $categoriesId=isset($_REQUEST['categoriesId'])?$_REQUEST['categoriesId']:'';
        while($row=mysql_fetch_assoc($getuserinfor))
        {
            $timezone=$row['timezone'];
            
        }

if($categoriesId=="")
{
	$getQuestion=mysql_query("SELECT questions . *,favoriteQuestion.status as favourite,usersinfo.id as userid,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.email as email,usersinfo.profile_pic,categories.hashtag FROM  questions
	LEFT JOIN favoriteQuestion ON questions.userId=favoriteQuestion.userID AND questions.id=favoriteQuestion.questionId left join categories  on questions.categoryId=categories.id
	LEFT JOIN usersinfo ON questions.userId=usersinfo.id
	WHERE questions.entity=0 and questions.isblock=0 and   questions.userId=".$userId." order by questions.id desc") or die(mysql_error());
}
else
{
	$getQuestion=mysql_query("SELECT questions . *,favoriteQuestion.status as favourite,usersinfo.id as userid,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.email as email,usersinfo.profile_pic,categories.hashtag FROM  questions
	LEFT JOIN favoriteQuestion ON questions.userId=favoriteQuestion.userID AND questions.id=favoriteQuestion.questionId left join categories  on questions.categoryId=categories.id
	LEFT JOIN usersinfo ON questions.userId=usersinfo.id
	WHERE questions.entity=0 and questions.isblock=0 and    questions.userId=".$userId." and questions.id in (select questionid from categoryquestion where categoryid in (".$categoriesId.") ) order by questions.id desc") or die(mysql_error());
	
}
if(mysql_num_rows($getQuestion)>0)
	{
		$questionnumber=0;
		while($row=mysql_fetch_array($getQuestion))
			{
				$userinfo['userid']=$row['userid'];
				$userinfo['name']=$row['name'];
				$userinfo['email']=$row['email'];
				$userinfo['profile_pic']=$row['profile_pic'];
				
				$questionInfo[$questionnumber]['questionId']=$row['id'];
				$questionInfo[$questionnumber]['question']=$row['question'];
				$questionInfo[$questionnumber]['categoryId']=$row['categoryId'];
				$questionInfo[$questionnumber]['subjectId']=$row['subjectId'];
				$questionInfo[$questionnumber]['question_date']=GetDatetimefromtimezone($row['question_date'],$timezone);
				$questionInfo[$questionnumber]['attachment']=$row['attachment'];
				if($row['thumb'] == $demo_thumb_url ||  $row['thumb'] == "")
						{
							$questionInfo[$questionnumber]['thumb']= "";
						}
						else
						{
							$questionInfo[$questionnumber]['thumb']=$row['thumb'];
						}
				//$questionInfo[$questionnumber]['thumb']=$row['thumb'];
				
				$checkSavedOrNot=mysql_query("SELECT status FROM savequestions WHERE userId=".$userId." AND questionId=".$row['id']."");
				if(mysql_num_rows($checkSavedOrNot)>0)
					{
						$getStatus=mysql_fetch_assoc($checkSavedOrNot);
						$questionInfo[$questionnumber]['questionSaved']=$getStatus['status'];
					}
				else	
					{
						$questionInfo[$questionnumber]['questionSaved']="0";
					}
				
				if($row['favourite']==NULL)
					{
						$questionInfo[$questionnumber]['favourite']='0';
					}
				else	
					{
						$questionInfo[$questionnumber]['favourite']=$row['favourite'];
						
					}
				$getNumberOfAnswersQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answers WHERE questionId=".$row['id'].""));
				$numberOfAnswers=$getNumberOfAnswersQuery[0];
				$questionInfo[$questionnumber]['totalAnswers']=$numberOfAnswers;
				$questionnumber++;
				
			}
        $updatequestions=mysql_query("update questions set IsView=1 where userId=".$userId."");
		echo json_encode(array("success"=>"1","message"=>$questionnumber." questions founded.","userinfo"=>$userinfo,"questions"=>$questionInfo));
		exit;
	}	
else
	{
		echo json_encode(array("success"=>"0","message"=>"you have not posted any question so far."));
		exit;
	}
	
	
?>
?>