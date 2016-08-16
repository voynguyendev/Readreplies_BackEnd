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

$userId=isset($_REQUEST['userid'])?$_REQUEST['userid']:"0";
    
    $getuserinfor=mysql_query("SELECT * FROM usersinfo WHERE id=".$userId."");
    
    while($row=mysql_fetch_assoc($getuserinfor))
    {
        $timezone=$row['timezone'];

    }


    
    
$getPushNotifycation=mysql_query("SELECT PushNotifycation . *,usersinfo.profile_pic as picturesender  FROM  PushNotifycation left JOIN usersinfo ON PushNotifycation.SenderId=usersinfo.id  WHERE   Isdelete =0 and PushNotifycation.ReceiverId=".$userId." order by Id DESC") or die(mysql_error());
if(mysql_num_rows($getPushNotifycation)>0)
	{
        $questionnumber=0;
		while($row=mysql_fetch_array($getPushNotifycation))
			{
                $PushNotifycation[$questionnumber]['id']=$row['Id'];
				$PushNotifycation[$questionnumber]['DateCreate']=GetDatetimefromtimezone($row['DateCreate'],$timezone);
				$PushNotifycation[$questionnumber]['Message']=$row['Message'];
				$PushNotifycation[$questionnumber]['Parmaster']=$row['Parmaster'];
                $PushNotifycation[$questionnumber]['PushNotifycationType']=$row['PushNotifycationType'];
                $PushNotifycation[$questionnumber]['SenderId']=$row['SenderId'];
                $PushNotifycation[$questionnumber]['picturesender']=$row['picturesender'];
                $PushNotifycation[$questionnumber]['ReceiverId']=$row['ReceiverId'];
                $questionnumber++;
								
			}
		$updatepushnotifycation=mysql_query("update PushNotifycation set IsView=1 where PushNotifycation.ReceiverId=".$userId."");
        echo json_encode(array("status"=>"1","message"=>"Successfully","PushNotifycation"=>$PushNotifycation));
        
		exit;
	}	
else
	{
		echo json_encode(array("status"=>"0","message"=>"no PushNotification"));
		exit;
	}
	
	
?>