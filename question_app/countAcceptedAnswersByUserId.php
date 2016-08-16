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
            
            //$date->setTimezone(new DateTimeZone($timezone));
            //echo $date->format('Y-m-d h:i:s A') . "\n";
            return nicetime($date->format('Y-m-d h:i:s A'));
        }
    


    
    
include('database/connection.php');
include('checktoken.php');

$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';
$categoriesId=isset($_REQUEST['categoriesId'])?$_REQUEST['categoriesId']:'';    


if($user_id!='')
	{
        $getuserinfor=mysql_query("SELECT * FROM usersinfo WHERE id=".$user_id."");
        
        while($row=mysql_fetch_assoc($getuserinfor))
        {
            $timezone=$row['timezone'];
            $profile_pic=$row['profile_pic'];
        }
		$data=array();
        
		if($categoriesId=="")
			$getCount=mysql_query("SELECT a.*,q.categoryId  from answers a inner join questions q on a.questionid=q.id WHERE   a.status='accepted' AND a.userId=".$user_id." ORDER BY accept_date DESC");
		else
			$getCount=mysql_query("SELECT a.*,q.categoryId  from answers a inner join questions q on a.questionid=q.id WHERE   a.status='accepted' AND a.userId=".$user_id." and questions.id in (select questionid from categoryquestion where categoryid in (".$categoriesId.") ) ORDER BY accept_date DESC");
     	while($answersCount=mysql_fetch_assoc($getCount))
			{
                $answersCount["accept_date"]=GetDatetimefromtimezone($answersCount["accept_date"],$timezone);
				$data[]=$answersCount;
               
                
			}
        $update=mysql_query("update answers set IsView=1 WHERE  userId=".$user_id."");

		echo json_encode(array("status"=>"1","message"=>$data,"profile_pic"=>$profile_pic));
		exit;
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"Please provide user id."));
		exit;
	}

?>