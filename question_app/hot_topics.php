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
    
    $userId=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';
    $categoriesId=isset($_REQUEST['categoriesId'])?$_REQUEST['categoriesId']:'';
    $hashtag=isset($_REQUEST['hashtag'])?$_REQUEST['hashtag']:'';
    $rowget=isset($_REQUEST['rowget'])?$_REQUEST['rowget']:'50';
    
    $getuserinfor=mysql_query("SELECT * FROM usersinfo WHERE id=".$userId."");
    
    while($row=mysql_fetch_assoc($getuserinfor))
    {
        $timezone=$row['timezone'];
        
    }
    
    $queryupdatepoint="CREATE TEMPORARY TABLE questionstemplate(id VARCHAR( 50 ) NOT NULL ,addpoint INT);INSERT INTO questionstemplate(id,addpoint) SELECT id, (TIMESTAMPDIFF( HOUR , question_date_update,NOW( ) )) /4 FROM questions WHERE (POINT - ( (TIMESTAMPDIFF( HOUR ,question_date_update ,NOW()) ) /4)) >=0  and ( (TIMESTAMPDIFF( HOUR,question_date_update ,NOW()) ) /4)>0;UPDATE questions SET POINT = POINT - ( SELECT addpoint FROM questionstemplate WHERE id = questions.id ) ;";

    mysql_query($queryupdatepoint);

    
    if($userId!='')
    {
        mysql_set_charset("UTF8");
        if($hashtag=="")
        {
            if($categoriesId=='')
            {
                
                $getQuestion=mysql_query("SELECT questions . *,usersinfo.id as userid,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.email as email,usersinfo.thumb as userthumb FROM  questions INNER JOIN usersinfo ON questions.userId=usersinfo.id where questions.isblock=0  order by questions.question_date_update desc,questions.point desc,questions.id desc LIMIT ".$rowget) or die(mysql_error());
            }
            else
            {
                $getQuestion=mysql_query("SELECT questions . *,usersinfo.id as userid,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.email as email,usersinfo.thumb as userthumb FROM  questions INNER JOIN usersinfo ON questions.userId=usersinfo.id  where questions.isblock=0 and  questions.id in (select questionid from categoryquestion where categoryid in ($categoriesId) ) order by questions.question_date_update desc,questions.point desc,questions.id desc LIMIT ".$rowget) or die(mysql_error());
                
            }
        }
        else
        {
            $hashtag="%".$hashtag."%";
            $getQuestion=mysql_query("SELECT questions . *,usersinfo.id as userid,CONCAT(usersinfo.name,' ',usersinfo.lname) as name,usersinfo.email as email,usersinfo.thumb as userthumb FROM  questions INNER JOIN usersinfo ON questions.userId=usersinfo.id  where questions.isblock=0 and  questions.id in (select questionid from categoryquestion inner join categories on categoryquestion.categoryid=categories.id where  categories.hashtag like '$hashtag' ) or questions.hashtag like '$hashtag' order by questions.question_date_update desc,questions.point desc,questions.id desc LIMIT ".$rowget) or die(mysql_error());
            
            
            
        }
        
        if(mysql_num_rows($getQuestion)>0)
        {
            $questionnumber=0;
            while($row=mysql_fetch_array($getQuestion))
            {
                
                $getCategories=mysql_query("SELECT * FROM categories WHERE userid<>0 and id in (select categoryid from categoryquestion where questionid=".$row['id'].") ") or die(mysql_error());
                
                
                $categories_data=array();
                $i=0;
                $sqltagfriends="";
                $hashtagarray=array();
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
                
                $categoryhashtagArr=explode(' ',$row['hashtag']);
                
                
                foreach($categoryhashtagArr as $hashtag)
                {
                    $hashtagarray[$indexhashtag++]["hashtag"]=$hashtag;
                    
                }
                
                $questionInfo[$questionnumber]['hashtagarr']=$hashtagarray;
                
                
                
                $tagfriends_data=array();
                if($row['tagfriend']!="")
                {
                    
                    $getTabfriend=mysql_query("SELECT * FROM usersinfo WHERE  id IN(".$row['tagfriend'].") ".$sqltagfriends) or die(mysql_error());
                    
                    $i=0;
                    if(mysql_num_rows($getTabfriend)>0)
                    {
                        while($row2=mysql_fetch_array($getTabfriend))
                        {
                            $tagfriends_data[$i]['id']=$row2['id'];
                            $tagfriends_data[$i++]['username']=$row2['name']." ".$row2['lname'];
                            
                            
                        }
                    }
                    
                }
                
                $questionInfo[$questionnumber]['tagfriends']=$tagfriends_data;
                $questionInfo[$questionnumber]['categoiesId']=$categories_data;
                $getquestionImages=mysql_query("SELECT * FROM imagesquestion WHERE  questionid ='".$row['id']."'") or die(mysql_error());
                $questionInfo[$questionnumber]['questionImages']=mysql_num_rows($getquestionImages)."";
                
                $questionInfo[$questionnumber]['userid']=$row['userid'];
                $questionInfo[$questionnumber]['name']=$row['name'];
                $questionInfo[$questionnumber]['email']=$row['email'];
                $questionInfo[$questionnumber]['questionId']=$row['id'];
                $questionInfo[$questionnumber]['question']=$row['question'];
                $questionInfo[$questionnumber]['categoryId']=$row['categoryId'];
                $questionInfo[$questionnumber]['subjectId']=$row['subjectId'];
                $questionInfo[$questionnumber]['question_date']=GetDatetimefromtimezone($row['question_date'],$timezone);
                $questionInfo[$questionnumber]['attachment']=$row['attachment'];
                $questionInfo[$questionnumber]['userthumb']=$row['userthumb'];
                $questionInfo[$questionnumber]['hashtag']=$row['hashtag'];
                
                if($row['thumb'] == $demo_thumb_url ||  $row['thumb'] == "")
                {
                    $questionInfo[$questionnumber]['thumb']= "";
                }
                else
                {
                    $questionInfo[$questionnumber]['thumb']=$row['thumb'];
                }
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
                $checkFvt=mysql_query("SELECT status FROM favoriteQuestion WHERE userId=".$userId." AND questionId=".$row['id']."");
                if(mysql_num_rows($checkFvt)>0)
                {
                    $getStatus=mysql_fetch_assoc($checkFvt);
                    $questionInfo[$questionnumber]['favourite']=$getStatus['status'];
                }
                else
                {
                    $questionInfo[$questionnumber]['favourite']="0";
                }
                
                $getNumberOfAnswersQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM answers WHERE questionId=".$row['id'].""));
                $numberOfAnswers=$getNumberOfAnswersQuery[0];
                $questionInfo[$questionnumber]['answercount']=$numberOfAnswers;

		       $getNumberOflikeQuestionQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM question_like_dislike WHERE status=1 and question_id=".$row['id'].""));
                $numberOflikes=$getNumberOflikeQuestionQuery[0];
                $questionInfo[$questionnumber]['likecount']=$numberOflikes;


                $likeDislikeStatusCheck=mysql_query("SELECT status FROM question_like_dislike WHERE question_id=".$row['id']." AND user_id=".$userId."") or die(mysql_error());

                if(mysql_num_rows($likeDislikeStatusCheck)>0)
            	{
            		$likeDislikeStatus=mysql_fetch_assoc($likeDislikeStatusCheck);
            	   	$questionInfo[$questionnumber]['like_dislike_status']=$likeDislikeStatus['status'];
            	}
                else
            	{
            	   	$questionInfo[$questionnumber]['like_dislike_status']='2';

            	}

            	$getNumberOfViewsQuery=mysql_fetch_row(mysql_query("SELECT count(*) FROM questions_view WHERE questionid=".$row['id'].""));
                $numberOfViews=$getNumberOfViewsQuery[0];
                $questionInfo[$questionnumber]['viewcount']=$numberOfViews;


                $questionnumber++;
                
            }
            echo json_encode(array("success"=>"1","message"=>$questionnumber." questions founded.","data"=>$questionInfo));
            exit;
        }
        else
        {
            echo json_encode(array("success"=>"0","message"=>"you have not posted any question so far."));
            exit;
        }
    }
    else
    {
        echo json_encode(array("success"=>"0","message"=>"you have not posted any question so far."));
        exit;
    }
    ?>