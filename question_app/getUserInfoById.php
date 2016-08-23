<?php
include('database/connection.php');
include('checktoken.php');

$mobile = isset($_REQUEST['mobile'])?$_REQUEST['mobile']:"";

$friend_id = isset($_REQUEST['friend_id'])?$_REQUEST['friend_id']:"";

$qCount=array();
$aCount=array();

if($mobile!='' && $friend_id!='')
	{	
	
		#friend_data
        
		$getAllQusCount=mysql_query("SELECT count(*) FROM questions");
		$getAllQusCountArr=mysql_fetch_array($getAllQusCount);

		
		$getSAvedQusCount=mysql_query("SELECT count(*) FROM savequestions WHERE IsView=0 and userId=".$friend_id." AND status=1");
		$getSAvedQusCountArr=mysql_fetch_array($getSAvedQusCount);
		
		$getAllAcceptedAnsCount=mysql_query("SELECT count(*) FROM answers  WHERE IsView=0 and userId=".$friend_id." AND status='accepted'");
		$getAllAcceptedAnsCountArr=mysql_fetch_array($getAllAcceptedAnsCount);
		
		$getAllFrds=mysql_query("SELECT * FROM friendRequests WHERE (sender_id=".$friend_id." OR receiver_id=".$friend_id.") AND status=1");
		if(mysql_num_rows($getAllFrds)>0)
			{
				$frds=array();
				while($nrow=mysql_fetch_assoc($getAllFrds))
					{
						if($nrow['sender_id']==$friend_id)
							{
								$frds[]=$nrow['receiver_id'];
							}
						else
							{
								$frds[]=$nrow['sender_id'];
							}
					}
			}
		if(count($frds)>0)
			{
			
				$getAllFriendQusCount=mysql_query("SELECT count(*) FROM questions WHERE IsView=0 and userId IN (".implode(',',$frds).")");
				$getAllFriendQusCountArr=mysql_fetch_array($getAllFriendQusCount);
				$friendsQus=$getAllFriendQusCountArr[0];
			}
		else	
			{
				$friendsQus="0";
			}
		
		
		$recentUpQs=mysql_query("SELECT id FROM questions WHERE IsView=0 and question_date >= SUBDATE(timestamp(now()), INTERVAL 72 HOUR) AND userId=".$friend_id."") or die(mysql_error());
		if(mysql_num_rows($recentUpQs) > 0)
		{
			$Q=0;
			while($getQusId=mysql_fetch_assoc($recentUpQs))
			{
				$qqCount[$Q]=$getQusId['id'];
				$Q++;
			}
		}
		else{
			$qqCount=array();
		}
		
		
		$getAns = mysql_query("SELECT questionId FROM answers WHERE IsView=0 and answer_date >= SUBDATE(timestamp(now()), INTERVAL 72 HOUR) OR accept_date >= 			SUBDATE(timestamp(now()), INTERVAL 72 HOUR) AND userId=".$friend_id."") or die(mysql_error());
		if(mysql_num_rows($getAns) > 0)
		{
			$A=0;
			while($getAnswersQusId=mysql_fetch_assoc($getAns))
			{
				$aaCount[$A]=$getAnswersQusId['questionId'];
				$A++;
			}
			
		}
		else{
			$aaCount=array();
		}
		
		$updateUArr = array_unique(array($qCount,$aCount));
		$upCount=count($updateUArr[0]);
		
		
				
		$friendsdata=mysql_query("select * from usersinfo where id='$friend_id'") or die(mysql_error());
		if(mysql_num_rows($friendsdata) > 0)
		{
			$friendinfoarraay=mysql_fetch_array($friendsdata);							
			$friendinfo['id']=$friendinfoarraay['id'];
			$friendinfo['name']=$friendinfoarraay['name'];
			$friendinfo['lname']=$friendinfoarraay['lname'];
			$friendinfo['email']=$friendinfoarraay['email'];
			//$userinfo['mobile']=$userinfoarraay['mobile'];
			$friendinfo['school']=$friendinfoarraay['school'];
			//$userinfo['grade']=$userinfoarraay['grade'];
			$friendinfo['state']=$friendinfoarraay['state'];
			$friendinfo['dob']=$friendinfoarraay['dob'];
			$friendinfo['gender']=$friendinfoarraay['gender'];
			$friendinfo['skill_and_interest']=$friendinfoarraay['skill_and_interest'];
			$friendinfo['city']=$friendinfoarraay['city'];
			$friendinfo['profile_pic']=$friendinfoarraay['profile_pic'];
			$friendinfo['StatusText']=$friendinfoarraay['StatusText'];
			
			$friendinfo['AllQuestionsCount']=$getAllQusCountArr[0];
			$friendinfo['SavedQuestionsCount']=$getSAvedQusCountArr[0];
			$friendinfo['AllAcceptedAnswersCount']=$getAllAcceptedAnsCountArr[0];
			
			$friendinfo['AllQuestionsByFriendsCount']=$friendsQus;
			$friendinfo['RecentUpdateCount']=$upCount;
			
			$getAllQusByThisUSer=mysql_query("SELECT id,question FROM questions WHERE userId=".$friendinfo['id']."");
				if(mysql_num_rows($getAllQusByThisUSer)>0)
					{
						while($singleQus=mysql_fetch_array($getAllQusByThisUSer))
							{
								$getNoOfAnswers=mysql_fetch_array(mysql_query("SELECT count(*) as no_of_answers FROM answers WHERE IsView=0 and questionID=".$singleQus['id'].""));
							
								$question[]['question']=$singleQus['question'];
								$question[]['no_of_answers']=$singleQus['no_of_answers'];
							}
					}
				
				
			$friendinfo['no_of_ques']=mysql_num_rows($getAllQusByThisUSer);
			
			
		}
		else
		{
			$friendinfo=array();
		}
		
		
		#friends data end
		
		# updates recent questions count
        
        
        $newgetrecentQuestionCount=mysql_query("SELECT count(*) FROM PushNotifycation WHERE IsView=0 and ReceiverId=".$mobile." AND Isdelete=0 AND DateCreate >SUBDATE(timestamp(now()), INTERVAL 72 HOUR) ");
        $newupdateCount=mysql_fetch_array($newgetrecentQuestionCount);

        
        
        
        $getAllMessagesCount=mysql_query("SELECT count(*) FROM messaging m inner join usersinfo u on m.sender_id=u.id WHERE IsView=0 and m.receiver_id=".$mobile." AND m.date_time >SUBDATE(timestamp(now()), INTERVAL 72 HOUR)");
        
        
        $getAllMessagesCountArr=mysql_fetch_array($getAllMessagesCount);

        
        
		$recentUpdatesQs=mysql_query("SELECT id FROM questions WHERE IsView=0 and question_date >= SUBDATE(timestamp(now()), INTERVAL 72 HOUR)") or die(mysql_error());
		if(mysql_num_rows($recentUpdatesQs) > 0)
		{
			$Q=0;
			while($getQuestionsId=mysql_fetch_assoc($recentUpdatesQs))
			{
				$qCount[$Q]=$getQuestionsId['id'];
				$Q++;
			}
		}
		else{
			$qCount=array();
		}
		
		# updates recent answers(related to questions) count
		$getAnswers = mysql_query("SELECT questionId FROM answers WHERE IsView=0 and answer_date >= SUBDATE(timestamp(now()), INTERVAL 72 HOUR) OR accept_date >= 			SUBDATE(timestamp(now()), INTERVAL 72 HOUR) AND userId=".$mobile."") or die(mysql_error());
		if(mysql_num_rows($getAnswers) > 0)
		{
			$A=0;
			while($getAnswersQuestionsId=mysql_fetch_assoc($getAnswers))
			{
				$aCount[$A]=$getAnswersQuestionsId['questionId'];
				$A++;
			}
			
		}
		else{
			$aCount=array();
		}
		
		$updateUniqueArr = array_unique(array($qCount,$aCount));
		$updateCount=count($updateUniqueArr[0]);
		#end
		
		$checkforlogin=mysql_query("select * from usersinfo where id='$mobile'") or die(mysql_error());
		
		$getAllQuestionsCount=mysql_query("SELECT count(*) FROM questions");
		$getAllQuestionsCountArr=mysql_fetch_array($getAllQuestionsCount);
		
		
		$friendRel=mysql_query("select *,status as relation from  friendRequests where (sender_id='$mobile' AND receiver_id='$friend_id') OR (sender_id='$friend_id' AND receiver_id='$mobile')") or die(mysql_error());
		if(mysql_num_rows($friendRel) > 0)
		{
			while($friendRelation=mysql_fetch_assoc($friendRel))
			{
				$friendRel=$friendRelation['relation'];
				#user is sender or reciever
				if($friendRelation['sender_id']==$mobile)
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
			$friendRel=3;
			$usertype='none';
		}
		#end
		
		$getSAvedQuestionsCount=mysql_query("SELECT count(*) FROM savequestions WHERE IsView=0 and userId=".$mobile." AND status=1");
		$getSAvedQuestionsCountArr=mysql_fetch_array($getSAvedQuestionsCount);
		
		$getAllAcceptedAnswersCount=mysql_query("SELECT count(*) FROM answers WHERE IsView=0 and userId=".$mobile." AND status='accepted'");
		$getAllAcceptedAnswersCountArr=mysql_fetch_array($getAllAcceptedAnswersCount);
		
		//$getAllFriends=mysql_query("SELECT * FROM friendRequests WHERE (sender_id=".$mobile." OR receiver_id=".$mobile.") AND status=1");
		$getAllFriends=mysql_query("SELECT * FROM friendRequests WHERE (sender_id=".$mobile." OR receiver_id=".$mobile.") AND status=1");
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
			
				$getAllFriendQuestionsCount=mysql_query("SELECT count(*) FROM questions WHERE IsView=0 and userId IN (".implode(',',$friends).")");
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
				$userinfo['lname']=$userinfoarraay['lname'];
				$userinfo['email']=$userinfoarraay['email'];
				//$userinfo['mobile']=$userinfoarraay['mobile'];
				$userinfo['school']=$userinfoarraay['school'];
				//$userinfo['grade']=$userinfoarraay['grade'];
				$userinfo['state']=$userinfoarraay['state'];
				$userinfo['dob']=$userinfoarraay['dob'];
				$userinfo['gender']=$userinfoarraay['gender'];
				
				$userinfo['skill_and_interest']=$userinfoarraay['skill_and_interest'];
				$userinfo['city']=$userinfoarraay['city'];
				$userinfo['profile_pic']=$userinfoarraay['profile_pic'];
                $userinfo['thumb']=$userinfoarraay['thumb'];
				$userinfo['AllQuestionsCount']=$getAllQuestionsCountArr[0];
				$userinfo['SavedQuestionsCount']=$getSAvedQuestionsCountArr[0];
				$userinfo['AllAcceptedAnswersCount']=$getAllAcceptedAnswersCountArr[0];
                
                $userinfo['AllMessagesCount']=$getAllMessagesCountArr[0];
				
				$userinfo['AllQuestionsByFriendsCount']=$friendsQuest;
				$userinfo['RecentUpdateCount']=$newupdateCount[0];
				
				$userinfo['StatusText']=$userinfoarraay['StatusText'];
				$userinfo['friendRel']=$friendRel;
				$userinfo['usertype']=$usertype;
				
				$getAllQuestionByThisUSer=mysql_query("SELECT id,question FROM questions WHERE userId=".$userinfo['id']."");
				if(mysql_num_rows($getAllQuestionByThisUSer)>0)
					{
						while($singleQuestion=mysql_fetch_array($getAllQuestionByThisUSer))
							{
								$getNoOfAnswers=mysql_fetch_array(mysql_query("SELECT count(*) as no_of_answers FROM answers WHERE IsView=0 and questionID=".$singleQuestion['id'].""));
							
								$question[]['question']=$singleQuestion['question'];
								$question[]['no_of_answers']=$getNoOfAnswers['no_of_answers'];
							}
					}
				
				
				$userinfo['no_of_ques']=mysql_num_rows($getAllQuestionByThisUSer);
				///echo json_encode(array("status"=>"1","message"=>"login successful.","userdata"=>$userinfo,"additional_data"=>$question)); 
				echo json_encode(array("status"=>"1","userdata"=>$userinfo,"frienddata"=>$friendinfo)); 
				//echo json_encode(array("userdata"=>$userinfo,"success"=>1)); 
				exit;
			}
		else
			{
				echo json_encode(array("status"=>"0","message"=>"No information found.")); 
				exit;
			}
	}
else	
	{
		echo json_encode(array("status"=>"0","message"=>"Please provide userid.")); 
		exit;
	}
?>