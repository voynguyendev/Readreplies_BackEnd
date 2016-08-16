

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
        
      //  $date->setTimezone(new DateTimeZone($timezone));
        //echo $date->format('Y-m-d h:i:s A') . "\n";
        return nicetime($date->format('Y-m-d h:i:s A'));
    }
    ?>



<?php
 
include('database/connection.php');
include('checktoken.php');

//define('UPLOAD_DIR_THUMB', '../question_app/question_images/thumbs/');

$questionId=isset($_REQUEST['questionId'])?$_REQUEST['questionId']:'';
$userId=isset($_REQUEST['userId'])?$_REQUEST['userId']:'';


if($userId!="" && $questionId!="")      {
   $getviewquestionDetail=mysql_query("SELECT * FROM questions_view WHERE userid = '".$userId."'  and questionid='$questionId'") or die(mysql_error());
  //  if(mysql_num_rows($getviewquestionDetail) <= 0)
  //  {
        mysql_query("insert into questions_view (userid,questionid) values('$userId','$questionId')") or die(mysql_error());
  //  }
}

$getuserinfor=mysql_query("SELECT * FROM usersinfo WHERE id=".$userId."");

while($row=mysql_fetch_assoc($getuserinfor))
{
    $timezone=$row['timezone'];

}


//--------------------------------------------c-username ---------------------------------------

$sql = mysql_query("SELECT name FROM usersinfo WHERE id = '".$userId."'") or die(mysql_error());
$result = mysql_fetch_assoc($sql);

//---------------------------------------------------------------------------------------------


$questioninfo_arr=array();
$getQuestion=mysql_query("SELECT questions.*, CONCAT(usersinfo.name,' ',usersinfo.lname) as name ,usersinfo.profile_pic,questions.thumb as thumb  FROM questions INNER JOIN usersinfo ON
questions.userId = usersinfo.id WHERE  questions.id=".$questionId." order by questions.id desc") or die(mysql_error());

if(mysql_num_rows($getQuestion)==0)  {

       	echo json_encode(array("success"=>"0","message"=>"No question found"));
		exit;

}

$getFavtStatus=mysql_query("SELECT status  FROM favoriteQuestion  WHERE  questionId=".$questionId." AND userId=".$userId."") or die(mysql_error());

$questioninfo_arr=mysql_fetch_assoc($getQuestion);

                         
                         $getCategories=mysql_query("SELECT * FROM categories WHERE userid<>0 and id in (select categoryid from categoryquestion where questionid='".$questioninfo_arr['id']."') ") or die(mysql_error());
                         
                         
                         
                         $tagfriends_data=array();
                         $sqltagfriends="";
                         $j=0;
                         $categories_data=array();
                         $i=0;
                         $hashtagarrar=array();
                         $indexhashtag=0;
                         if(mysql_num_rows($getCategories)>0)
                         {
                         while($row1=mysql_fetch_array($getCategories))
                         {
                         $categories_data[$i]['id']=$row1['id'];
                         $categories_data[$i]['userid']=$row1['userid'];
                         $categories_data[$i]['category_name']=$row1['category_name'];
                         $categories_data[$i]['lablehashtag']="#".$row1['hashtag'];
                         $categories_data[$i]['hashtag']=$row1['hashtag'];
                         
                         
                         $categoryhashtagArr=explode(' ',$row1['hashtag']);
                         // echo "kkkkk".$row1['hashtag'];

                         foreach($categoryhashtagArr as $hashtag)
                         {
                         if($hashtag!="")
                         $hashtagarray[$indexhashtag++]["hashtag"]=$hashtag;
                         
                         }
                         
                         
                         $categories_data[$i++]['TagFriends']=$row1['TagFriends'];
                         
                         if($row1['TagFriends']!="")
                         $sqltagfriends=$sqltagfriends." or id IN(".$row1['TagFriends'].")";
                         
                         
                         
                         }
                         }
                         
                         $categoryhashtagArr=explode(',',$questioninfo_arr['hashtag']);
                         
                         
                         foreach($categoryhashtagArr as $hashtag)
                         {
                         $hashtagarray[$indexhashtag++]["hashtag"]=$hashtag;
                         
                         }
                         $questioninfo_arr['hashtagarr']=$hashtagarray;
                         
                         
                         if($questioninfo_arr['tagfriend']!="")
                         {
                         $getTabfriend=mysql_query("SELECT * FROM usersinfo WHERE  id IN(".$questioninfo_arr['tagfriend'].") ".$sqltagfriends) or die(mysql_error());
                         
                         
                         if(mysql_num_rows($getTabfriend)>0)
                         {
                         while($row2=mysql_fetch_array($getTabfriend))
                         {
                         $tagfriends_data[$j]['id']=$row2['id'];
                         $tagfriends_data[$j++]['username']=$row2['name']." ".$row2['lname'];

                         
                         }
                         }
                         }

                         $questioninfo_arr['tagfriends']=$tagfriends_data;

                         $getquestionImages=mysql_query("SELECT * FROM imagesquestion WHERE  questionid ='".$questionId."'") or die(mysql_error());
                         $dataimages=array();
            			    if(mysql_num_rows($getquestionImages)>0)
            				{
            				        $m=0;
                    				while($row=mysql_fetch_array($getquestionImages))
                    					{
                    						$dataimages[$m]['imagequestionid']=$row['imagequestionid'];
                    						$dataimages[$m]['imagethumb']=$row['imagethumb'];
                    						$dataimages[$m]['image']=$row['image'];
                    						$dataimages[$m]['questionid']=$row['questionid'];
                                             $m++;

                    					}
            			    }

                          $questioninfo_arr['questionImages']=$dataimages;
                    


$likeDislikeStatusCheck=mysql_query("SELECT status FROM question_like_dislike WHERE question_id=".$questionId." AND user_id=".$userId."") or die(mysql_error());

if(mysql_num_rows($likeDislikeStatusCheck)>0)
	{
		$likeDislikeStatus=mysql_fetch_assoc($likeDislikeStatusCheck);
		$questioninfo_arr['like_dislike_status']=$likeDislikeStatus['status'];
	}
else
	{
		$questioninfo_arr['like_dislike_status']='2';
		
	}

   $getNumberOflikeQuestionQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM question_like_dislike WHERE status=1 and question_id=".$questionId.""));
    $numberOflikes=$getNumberOflikeQuestionQuery[0];
   $questioninfo_arr['likecount']=$numberOflikes;



$checkSavedOrNot=mysql_query("SELECT status FROM savequestions WHERE userId=".$userId." AND questionId=".$questionId."");
if(mysql_num_rows($checkSavedOrNot)>0)
	{
		$getStatus=mysql_fetch_assoc($checkSavedOrNot);
		$questioninfo_arr['questionSaved']=$getStatus['status']."";

	}
else
	{
		$questioninfo_arr['questionSaved']="0";

	}

$categoryofquestion=mysql_query("SELECT categories.* FROM categoryquestion inner join categories on categoryquestion.categoryid=categories.id WHERE questionId=".$questionId."");

            if(mysql_num_rows($categoryofquestion)>0)
                   {
                    $data=array();
                    $i=0;
                    while($row=mysql_fetch_assoc($categoryofquestion))
                   {
                         
                         $data[$i]=$row;
                         
                         $i++;
                    }

                         
                 }
                $questioninfo_arr["categories"]=$data;

                         
                         
$questioninfo_arr["question_date"]=GetDatetimefromtimezone($questioninfo_arr["question_date"],$timezone);


$getFavtStatus_arr=mysql_fetch_assoc($getFavtStatus);
if(mysql_num_rows($getFavtStatus)<=0)
	{
		$getFavtStatus_a="0";
	}
else
	{
		$getFavtStatus_a=$getFavtStatus_arr['status'];
	}
/*$getAnswers=mysql_query("SELECT *  FROM answers  WHERE  questionId=".$questionId." order by answers.answer_date desc")*/
$getAnswers=mysql_query("SELECT answers.* ,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.thumb as user_image_thumb FROM answers  join usersinfo   on usersinfo.id=answers.userId   WHERE (answers.isblock=0 or answers.userId='$userId') and   questionId=".$questionId."  order by answers.orderindex desc ") or die(mysql_error());

/*$getAnswers=mysql_query("SELECT answers.id, answers.questionId, answers.answer, answers.userId, answers.answer_date, answers.accept_date, answers.attachment, answers.status, answers.thumb as answer_thumb, usersinfo.name FROM answers  join usersinfo   on usersinfo.id=answers.userId   WHERE  questionId=".$questionId."  order by answers.id desc ") or die(mysql_error());*/
  	$data=array();
if(mysql_num_rows($getAnswers)>0)
	{
		

		$i=0;
		while($row=mysql_fetch_assoc($getAnswers))
			{
				
				$data[$i]=$row;
				
				$checkAnswerRating=mysql_query("SELECT rating as status FROM answers_rating WHERE userId=".$userId." AND answerId=".$row['id']."");
				if(mysql_num_rows($checkAnswerRating)>0)
					{
						$getStatus=mysql_fetch_assoc($checkAnswerRating);
						$data[$i]['rating']=$getStatus['status'];
					}
				else	
					{
						$data[$i]['rating']="0";
					}

				
				$checkAnswerLikeDislikestatus=mysql_query("SELECT status FROM answer_like_dislike WHERE user_id=".$userId." AND answer_id=".$row['id']."");
				if(mysql_num_rows($checkAnswerLikeDislikestatus)>0)
					{
						$getLikeDislikestatus=mysql_fetch_assoc($checkAnswerLikeDislikestatus);
						$data[$i]['like_dislike_status']=$getLikeDislikestatus['status'];
					}
				else	
					{
						$data[$i]['like_dislike_status']="2";
					}

                    $getNumberOflikeAnswerQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answer_like_dislike WHERE status=1 and answer_id=".$row['id'].""));
                    $numberOflikes=$getNumberOflikeAnswerQuery[0];
                  	$data[$i]['likecount']=$numberOflikes;

				   $data[$i]['answer_date']=GetDatetimefromtimezone($data[$i]['answer_date'],$timezone);
					
					
			       $i++;
			}
			
			
			
		echo json_encode(array("question_info"=>$questioninfo_arr,"favourite"=>$getFavtStatus_a,"success"=>"1","data"=>$data));
		exit;
	}	
else
	{
		echo json_encode(array("question_info"=>$questioninfo_arr,"favourite"=>$getFavtStatus_a,"success"=>"1","message"=>"No answers found","data"=>$data));
		exit;
	}


?>

