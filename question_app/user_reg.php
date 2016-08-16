<?php

include('database/connection.php');


    $server=$_SERVER['HTTP_HOST'];
	$name = isset($_REQUEST['name'])?$_REQUEST['name']:""; 
    $email = isset($_REQUEST['email'])?$_REQUEST['email']:""; 
	$password = isset($_REQUEST['password'])?$_REQUEST['password']:""; 
	$confirmpassword = isset($_REQUEST['confirmpassword'])?$_REQUEST['confirmpassword']:"";
	/*$mobile = isset($_REQUEST['mobile'])?$_REQUEST['mobile']:"";
	$school = isset($_REQUEST['school'])?$_REQUEST['school']:"";
	$grade = isset($_REQUEST['grade'])?$_REQUEST['grade']:"";
	$city = isset($_REQUEST['city'])?$_REQUEST['city']:"";*/
	$action=isset($_REQUEST['action'])?$_REQUEST['action']:"";
	//---------------------------------------------------------------
	
	$lname = isset($_REQUEST['lname'])?$_REQUEST['lname']:"";
	
	//--------------------------------------------------------------
	//$id = isset($_REQUEST['id'])?$_REQUEST['id']:""; 



// password encrypt

  function encrypt_decrypt($action, $string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'This is my secret key';
    $secret_iv = 'This is my secret iv';
    $key = hash('sha256', $secret_key);
    
   
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}


 $encrypted_txt = encrypt_decrypt('encrypt', $password);

////////////////////////////////////////////////////////////////////////////


// mobile is already exist(email instead of mobile)

 
// $search_num=mysql_query("select * from usersinfo where mobile='$mobile' ");
 $search_num=mysql_query("select * from usersinfo where email='$email' ");

 if($action==1)
	{
		//$checkforlogin=mysql_query("select * from usersinfo where mobile='$mobile' AND (password='$encrypted_txt') ");
		$checkforlogin=mysql_query("select * from usersinfo where email='$email' AND (password='$encrypted_txt') and disabled=0");
		if(mysql_num_rows($checkforlogin)>0)
			{
				$userinfoarraay=mysql_fetch_array($checkforlogin);
							
				$userinfo['id']=$userinfoarraay['id'];
				$userinfo['name']=$userinfoarraay['name'];
				$userinfo['email']=$userinfoarraay['email'];
				//$userinfo['mobile']=$userinfoarraay['mobile'];
				//$userinfo['password']=encrypt_decrypt('decrypt', $userinfoarraay['password']);;
				//$userinfo['school']=$userinfoarraay['school'];
				//$userinfo['grade']=$userinfoarraay['grade'];
				//$userinfo['city']=$userinfoarraay['city'];
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
                
                
                $keytokent=$userinfo['id'].$userinfo['name'].$userinfo['email'].$userinfo['lname']."qboxpro";
                $keytokent=encrypt_decrypt("encrypt",$keytokent);
                
               // $tokenexpire = new DateTime('2006-12-12');
                //echo $tokenexpire;
                
                $tokenexpire =date('Y-m-d', strtotime("+2 days"));
                 //date_add($tokenexpire, date_interval_create_from_date_string('5 days'));
                //$tokenexpire->modify('+1 day');
                $tokenUpdate=mysql_query("UPDATE usersinfo SET TokentExpire='$tokenexpire',tokent='$keytokent' WHERE id=".$userinfo['id']."") or die(mysql_error());
                $userinfo['token']=$keytokent;
                $userinfo['tokenexpire']=$tokenexpire;
                
                
				///echo json_encode(array("status"=>"1","message"=>"login successful.","userdata"=>$userinfo,"additional_data"=>$question)); 
				echo json_encode(array("status"=>"1","message"=>"login successful.","userdata"=>$userinfo)); 
				exit;
			}
		else
			{
				
				//added lately for validations
				$search_num_for_validations = mysql_query("select id from usersinfo where email='$email' ");
				if(mysql_num_rows($search_num_for_validations) == 0)
				{
					echo json_encode(array("status"=>"0","message"=>"Incorrect Email Address")); 
					exit;
				}
				else
				{
					echo json_encode(array("status"=>"0","message"=>"Incorrect Password")); 
					exit;
				}
			}
				
	}
else
	{
		if(mysql_num_rows($search_num)>0)
			{	
				echo json_encode(array("status"=>"0","message"=>"Email already exists.")); 
				exit;
			}
		else	
			{
			   // registration user info
				if($password!=$confirmpassword)
					{
						echo json_encode(array("status"=>"0","message"=>"Passwords do not match.")) ;
						exit;
					}
				
				//if($name != "" && $email != "" && $password != ""  && $mobile != "")
				if($name != "" && $email != "" && $password != ""  && $lname != "")
					{
						
						//$query="INSERT INTO `usersinfo`(name,email,password,mobile,school,grade,city) VALUES('$name','$email','$encrypted_txt','$mobile','$school','$grade','$city')"; 
						
						$query="INSERT INTO `usersinfo`(name,lname,email,password) VALUES('$name','$lname','$email','$encrypted_txt')";
							
						$result = mysql_query($query) or die(mysql_error());
						$u_id=mysql_insert_id();	 
						if($result)
							{					 
								$userinfo['id']=$u_id;
								echo json_encode(array("status"=>"1","message"=>"Add successfully","userdata"=>$userinfo));
								exit;
							}
							  
					}
				else
					{   
						$data['success']=0;
						echo json_encode(array("status"=>"0","message"=>"insert proper user info")) ;
						exit;
					}

			}

	}
?>