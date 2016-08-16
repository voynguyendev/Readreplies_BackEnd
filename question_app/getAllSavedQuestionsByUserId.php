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
        
        $date->setTimezone(new DateTimeZone($timezone));
        //echo $date->format('Y-m-d h:i:s A') . "\n";
        return nicetime($date->format('Y-m-d h:i:s A'));
    }
    
    
    
    
include('database/connection.php');
include('checktoken.php');
$demo_thumb_url = $server.'/question_app/question_images/thumbs/';

$userId=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';
    $categoriesId=isset($_REQUEST['categoriesId'])?$_REQUEST['categoriesId']:'';
    $getuserinfor=mysql_query("SELECT * FROM usersinfo WHERE id=".$userId."");
    
    while($row=mysql_fetch_assoc($getuserinfor))
    {
        $timezone=$row['timezone'];
        
    }


if($userId!='')
	{
		$savedQuestions=array();
		//questions.id in (select questionid from categoryquestion where categoryid in (".$categoriesId.") )
        if($categoriesId=="")
			$getAllSavedQuestion=mysql_query("SELECT questions.*,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.profile_pic,categories.hashtag FROM savequestions  LEFT JOIN questions ON savequestions.questionId=questions.id left join categories  on questions.categoryId=categories.id  LEFT JOIN usersinfo ON usersinfo.id = questions.userId WHERE savequestions.userId=".$userId." AND status=1   order by questions.id desc") or die(mysql_error());
		else
			$getAllSavedQuestion=mysql_query("SELECT questions.*,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.profile_pic,categories.hashtag FROM savequestions  LEFT JOIN questions ON savequestions.questionId=questions.id left join categories  on questions.categoryId=categories.id  LEFT JOIN usersinfo ON usersinfo.id = questions.userId WHERE savequestions.userId=".$userId." AND status=1 AND  savequestions.questionId in (select questionid from categoryquestion where categoryid in (".$categoriesId.") )  order by questions.id desc") or die(mysql_error());
		if(mysql_num_rows($getAllSavedQuestion)>0)
			{	
				while($row=mysql_fetch_assoc($getAllSavedQuestion))
					{	
						$getFavouriteStatus=mysql_fetch_row(mysql_query("SELECT status FROM favoriteQuestion WHERE questionId=".$row['id']." AND userId=".$userId.""));
					if($getFavouriteStatus[0])
						$row['fvtStatus']=$getFavouriteStatus[0];
					else
						$row['fvtStatus']="0";
					
					$getNumberOfAnswersQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answers WHERE questionId=".$row['id'].""));
					$numberOfAnswers=$getNumberOfAnswersQuery[0];
					$row['totalAnswers']=$numberOfAnswers;
					$row['questionSaved']="1";
                    
                        
                    $row['question_date']=GetDatetimefromtimezone($row['question_date'],$timezone);
  
                        
						$savedQuestions[]=$row;	 
						
					}
					
				
                $updateAllSavedQuestion=mysql_query("update savequestions set IsView=1 where savequestions.userId=".$userId."") or die(mysql_error());

				echo json_encode(array("success"=>"1","message"=>mysql_num_rows($getAllSavedQuestion)." question found.","data"=>$savedQuestions));	
				exit;
			}
		else
			{
				echo json_encode(array("success"=>"0","message"=>"No saved question found."));	
				exit;
			}
	
	}
else
	{
		echo json_encode(array("success"=>"0","message"=>"Error"));	
	}

?>