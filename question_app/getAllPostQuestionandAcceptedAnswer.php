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

$server = $_SERVER['HTTP_HOST'];
$demo_thumb_url = $server.'/question_app/question_images/thumbs/';

$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';
    
    $getuserinfor=mysql_query("SELECT * FROM usersinfo WHERE id=".$user_id."");
    
    while($row=mysql_fetch_assoc($getuserinfor))
    {
        $timezone=$row['timezone'];

	$userinfo['userid']=$row['id'];
				$userinfo['name']=$row['name'];
				$userinfo['email']=$row['email'];
				$userinfo['user_image_thumb']=$row['thumb'];
				$userinfo['profile_pic']=$row['profile_pic'];
				$userinfo['StatusText']=$row['StatusText'];
               
                
                $userinfo['dob']=$row['dob'];
                $userinfo['city']=$row['city'];
                $userinfo['state']=$row['state'];
                $userinfo['school']=$row['school'];
                $userinfo['skill_and_interest']=$row['skill_and_interest'];
                $userinfo['aboutme']=$row['aboutme'];
                $userinfo['workat']=$row['workat'];
               
               
                
                //images of user
                
                $getimagesuser=mysql_query("select * from questions  where questions.entity=1  and  userid=".$userinfo['userid']);


                if(mysql_num_rows($getimagesuser)>0)
                {
                {
                    $arrimagesuser=array();
                }

                $userinfo['imagesuser']=$arr
                    $i=0;

                    while($row1=mysql_fetch_array($getimagesuser))
                    {
                        $arrimagesuser[$i]['id']=$row1['id'];
                        $arrimagesuser[$i]['userid']=$row1['userId'];
                        $arrimagesuser[$i]['attachment']=$row1['attachment']
                    // $arrimagesuser=mysql_fetch_assoc($getimagesuser);
;
                        $arrimagesuser[$i]['isselected']=$row1['isselected'];
                        $arrimagesuser[$i++]['thumb']=$row1['thumb'];


                    }




                }
                elseimagesuser;


        
    }
    
    

$login_id=isset($_REQUEST['login_id'])?$_REQUEST['login_id']:'';


$relation=mysql_query("SELECT *,status as relation FROM friendRequests WHERE (sender_id='$user_id' AND receiver_id='$login_id') OR (sender_id='$login_id' AND receiver_id='$user_id')") or die(mysql_error());
if(mysql_num_rows($relation)>0)
{
	//$usersRelationship=mysql_fetch_assoc($relation);
	//$usersRelation=$usersRelationship['relation'];
	
	
	while($usersRelationship=mysql_fetch_assoc($relation) and $usersRelation!="1")
	{
		//$friendRel=$friendRelation['relation'];
		$usersRelation=$usersRelationship['relation'];
		#user is sender or reciever
		if($usersRelationship['sender_id']==$login_id)
		{
			$usertype='sender';	
		}
		else
		{
			$usertype='receiver';	
		}
		
	}
	
}
else{
	//for no relation
	$usersRelation=3;
	$usertype='none';
}


$checkfollowerData=mysql_query("SELECT * FROM followers WHERE user_id=".$user_id." and follower_id=".$login_id."") or die(mysql_error());

    if(mysql_num_rows($checkfollowerData)>0)
    {
        $statusfollow="1";
    }
    else
    {
        $statusfollow="0";
    }
    
    
    
#follower count
$followerData=mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as follower_count FROM followers WHERE user_id=".$login_id."")) or die(mysql_error());


#follow count
$followData=mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as follow_count FROM followers WHERE follower_id=".$login_id."")) or die(mysql_error());

    

//$getQuestion=mysql_query("SELECT questions.*,favoriteQuestion.status as favourite,usersinfo.id as userid,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.email as email,usersinfo.thumb as user_image_thumb,usersinfo.StatusText as StatusText FROM questions LEFT JOIN favoriteQuestion ON questions.userId=favoriteQuestion.userID AND questions.id=favoriteQuestion.questionId LEFT JOIN usersinfo ON questions.userId=usersinfo.id WHERE questions.userId=".$user_id." order by questions.id desc") or die(mysql_error());
    
    

$getQuestion=mysql_query("SELECT questions.*,favoriteQuestion.status as favourite,usersinfo.id as userid,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.email as email,usersinfo.thumb as user_image_thumb, usersinfo.profile_pic as profile_pic ,usersinfo.StatusText as StatusText,usersinfo.city as city,usersinfo.dob as dob,usersinfo.state as state,usersinfo.school as school,usersinfo.skill_and_interest as skill_and_interest, usersinfo.workat as workat,usersinfo.aboutme as aboutme FROM questions LEFT JOIN favoriteQuestion ON questions.userId=favoriteQuestion.userID AND questions.id=favoriteQuestion.questionId LEFT JOIN usersinfo ON questions.userId=usersinfo.id WHERE questions.entity=0 and questions.isblock=0 and questions.userId=".$user_id." order by questions.id desc") or die(mysql_error());
    
    //echo "ok";
    
      //echo mysql_num_rows($getQuestion);


 $userinfo['follower_count']=$followerData['follower_count'];
				$userinfo['follow_count']=$followData['follow_count'];
				$userinfo['relation']=$usersRelation;
				$userinfo['usertype']=$usertype;
				
 				$userinfo['statusfollow']=$statusfollow;


				

$questionInfo=array();
$data=array();
if(mysql_num_rows($getQuestion)>0)
	{
		$questionnumber=0;
		while($row=mysql_fetch_array($getQuestion))
			{
				
                if($questionnumber==0)
                {
               
                    
                   // echo json_encode( $userinfo['imagesuser']);
             }
                
                
                
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
			
		$getCount=mysql_query("SELECT * from answers WHERE status='accepted' AND userId=".$user_id." ORDER BY accept_date DESC");
		if(mysql_num_rows($getCount) > 0)
		{
			while($answersCount=mysql_fetch_assoc($getCount))
			{
                $answersCount["answer_date"]=GetDatetimefromtimezone($answersCount["answer_date"],$timezone);
				$data[]=$answersCount;
			}
		}
		else{
			$data=array();
		}
		
				
		echo json_encode(array("success"=>"1","message"=>$questionnumber." questions founded.","userinfo"=>$userinfo,"questions"=>$questionInfo,"answer"=>$data));
		exit;
	}
	else{
echo json_encode(array("success"=>"1","message"=>"0 questions founded.","userinfo"=>$userinfo,"questions"=>$questionInfo,"answer"=>$data));
		exit;


}

?>