<?php

include('database/connection.php'); 

    $server=$_SERVER['HTTP_HOST'];
	$name = isset($_REQUEST['name'])?$_REQUEST['name']:""; 
	$gmailid = isset($_REQUEST['gmailid'])?$_REQUEST['gmailid']:""; 
    $email = isset($_REQUEST['email'])?$_REQUEST['email']:""; 
	$action=isset($_REQUEST['action'])?$_REQUEST['action']:"";
	$lname = isset($_REQUEST['lname'])?$_REQUEST['lname']:""; 
	
//$search_num=mysql_query("select * from usersinfo where email='$email'");


	
		//$checkforlogin=mysql_query("select * from usersinfo where mobile='$mobile' AND (password='$encrypted_txt') ");
		$checkforlogin=mysql_query("select * from usersinfo where gmailid= '$gmailid'");
		if(mysql_num_rows($checkforlogin)>0)
			{
				$userinfoarraay=mysql_fetch_array($checkforlogin);
							
				$userinfo['id']=$userinfoarraay['id'];
				$userinfo['name']=$userinfoarraay['name'];
				$userinfo['email']=$userinfoarraay['email'];
				$userinfo['lname']=$userinfoarraay['lname'];
				
				
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
				echo json_encode(array("status"=>"1","message"=>"login successful.","userdata"=>$userinfo)); 
				exit;
			}
		else
			{ 
				
				//added lately for validations
				//$search_num_for_validations = mysql_query("select id from usersinfo where email='$email' ");
				//if(mysql_num_rows($search_num_for_validations) == 0)
				//{
					$query="INSERT INTO `usersinfo`(name,lname,email,gmailid) VALUES('$name','$lname','$email','$gmailid')";
							
					$result = mysql_query($query) or die(mysql_error());
					$u_id=mysql_insert_id();	 
					if($result)
						{					 
							$userinfo['id']=$u_id;
							
							$userinfo['name']=$name;
							$userinfo['email']=$email;
							$userinfo['lname']=$lname;
							echo json_encode(array("status"=>"1","message"=>"Add successfully","userdata"=>$userinfo));
							exit; 
						} 
				//}
				/*else
				{
					echo json_encode(array("status"=>"0","message"=>"Incorrect Password")); 
					exit;
				}*/
			}
				

	
	
?>	