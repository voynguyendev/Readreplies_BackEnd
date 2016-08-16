<?php 
include('database/connection.php');
include('checktoken.php');

$mobile = isset($_REQUEST['mobile'])?$_REQUEST['mobile']:"";
$user_id = isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";

if($mobile!='' && $user_id !='')
	{	
		//$getstatus = mysql_query("SELECT status FROM friendRequests WHERE (sender_id = '".$mobile."' AND receiver_id='".$user_id."') OR (sender_id = '".$user_id."' AND receiver_id='".$mobile."')") or die(mysql_error());
		$getstatus = mysql_query("SELECT sender_id,receiver_id,status FROM friendRequests WHERE (sender_id = '".$mobile."' AND receiver_id='".$user_id."') OR (sender_id = '".$user_id."' AND receiver_id='".$mobile."')") or die(mysql_error());
		
        
        
        
		if(mysql_num_rows($getstatus) > 0)
		{
			$getStatusArr=mysql_fetch_array($getstatus);
			if($getStatusArr['status'] == 0)
			{
				$friend = '0';
				
			}
			else{
				$friend = '1';
			}
		}
		else
		{
			$friend = '3';
		}
		
        //images of user
        
        $getimagesuser=mysql_query("select * from imagesuser where userid=".$mobile);
        
        if(mysql_num_rows($getimagesuser)>0)
        {
           // $arrimagesuser=mysql_fetch_assoc($getimagesuser);
            
            $i=0;
            
                while($row1=mysql_fetch_array($getimagesuser))
                {
                    $arrimagesuser[$i]['id']=$row1['id'];
                    $arrimagesuser[$i]['userid']=$row1['userid'];
                    $arrimagesuser[$i]['attachment']=$row1['attachment'];
                    $arrimagesuser[$i]['isselected']=$row1['isselected'];
                    $arrimagesuser[$i++]['thumb']=$row1['thumb'];
                    
                    
                }
            

            
        
        }
        else
        {
            $arrimagesuser=array();
        }
        
		//------------------------------ user identification ----------------------------------------
		
		$userinfo['sender_id'] = $getStatusArr['sender_id'];
		$userinfo['receiver_id'] = $getStatusArr['receiver_id'];
		
		//-------------------------------------------------------------------------------------------
		
		$checkforlogin=mysql_query("select * from usersinfo where id='$mobile'") or die(mysql_error());
		
		$getAllQuestionsCount=mysql_query("SELECT count(*) FROM questions");
		$getAllQuestionsCountArr=mysql_fetch_array($getAllQuestionsCount);
		
		$getSAvedQuestionsCount=mysql_query("SELECT count(*) FROM savequestions WHERE userId=".$mobile." AND status=1");
		$getSAvedQuestionsCountArr=mysql_fetch_array($getSAvedQuestionsCount);
		
		$getAllAcceptedAnswersCount=mysql_query("SELECT count(*) FROM answers WHERE userId=".$mobile." AND status='accepted'");
		$getAllAcceptedAnswersCountArr=mysql_fetch_array($getAllAcceptedAnswersCount);
		
		//$getAllFriends=mysql_query("SELECT * FROM friendRequests WHERE (sender_id=".$mobile." OR receiver_id=".$mobile.") AND status=1");
		$getAllFriends=mysql_query("SELECT * FROM friendRequests WHERE (sender_id=".$mobile." OR receiver_id=".$mobile.")");
		if(mysql_num_rows($getAllFriends)>0)
			{
				$friends=array();
				while($row=mysql_fetch_assoc($getAllFriends))
					{
						if($row['sender_id']==$mobile)
							{
								$friends[]=$row['receiver_id'];
							}
						else
							{
								$friends[]=$row['sender_id'];
							}
					}
			}
		if(count($friends)>0)
			{
				$getAllFriendQuestionsCount=mysql_query("SELECT count(*) FROM questions WHERE userId IN (".implode(',',$friends).")");
				$getAllFriendQuestionsCountArr=mysql_fetch_array($getAllFriendQuestionsCount);
				$friendsQuest=$getAllFriendQuestionsCountArr[0];
			}
		else	
			{
				$friendsQuest="0";
			}
		
		
		if(mysql_num_rows($checkforlogin)>0)
			{
				$userinfoarraay=mysql_fetch_array($checkforlogin);							
				$userinfo['id']=$userinfoarraay['id'];
				$userinfo['name']=$userinfoarraay['name'];
				$userinfo['email']=$userinfoarraay['email'];
				$userinfo['mobile']=$userinfoarraay['mobile'];
				$userinfo['school']=$userinfoarraay['school'];
				$userinfo['status']=$friend;
				$userinfo['grade']=$userinfoarraay['grade'];
				$userinfo['city']=$userinfoarraay['city'];
                $userinfo['skill_and_interest']=$userinfoarraay['skill_and_interest'];
                $userinfo['state']=$userinfoarraay['state'];
                $userinfo['lname']=$userinfoarraay['lname'];
                $userinfo['dob']=$userinfoarraay['dob'];
                $userinfo['timezone']=$userinfoarraay['timezone'];
                $userinfo['aboutme']=$userinfoarraay['aboutme'];
                $userinfo['workat']=$userinfoarraay['workat'];

                
                
				$userinfo['profile_pic']=$userinfoarraay['profile_pic'];
				$userinfo['AllQuestionsCount']=$getAllQuestionsCountArr[0];
				$userinfo['SavedQuestionsCount']=$getSAvedQuestionsCountArr[0];
				$userinfo['AllAcceptedAnswersCount']=$getAllAcceptedAnswersCountArr[0];
				
				$userinfo['AllQuestionsByFriendsCount']=$friendsQuest;
                $userinfo['imagesuser']=$arrimagesuser;
				
				
				$getAllQuestionByThisUSer=mysql_query("SELECT id,question FROM questions WHERE userId=".$userinfo['id']."");
				if(mysql_num_rows($getAllQuestionByThisUSer)>0)
					{
						while($singleQuestion=mysql_fetch_array($getAllQuestionByThisUSer))
							{
								$getNoOfAnswers=mysql_fetch_array(mysql_query("SELECT count(*) as no_of_answers FROM answers WHERE questionID=".$singleQuestion['id'].""));					
							
								$question[]['question']=$singleQuestion['question'];
								$question[]['no_of_answers']=$getNoOfAnswers['no_of_answers'];
							}
					}
				
				
				$userinfo['no_of_ques']=mysql_num_rows($getAllQuestionByThisUSer);
				///echo json_encode(array("status"=>"1","message"=>"login successful.","userdata"=>$userinfo,"additional_data"=>$question)); 
				
			
				echo json_encode(array("userdata"=>$userinfo,"success"=>1));
				//echo json_encode(array("userdata"=>$userinfo,"success"=>1)); 
				exit;
			}
		else
			{
				echo json_encode(array("message"=>"No information found.","success"=>0));
				exit;
			}
	}
else	
	{
		echo json_encode(array("message"=>"Please provide userid.","success"=>0));
		exit;
	}
?>