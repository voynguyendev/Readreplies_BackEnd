<?php
    
    include('database/connection.php');   $server=$_SERVER['HTTP_HOST'];
    require 'PHPmailer/class.phpmailer.php';
    $email = isset($_REQUEST['email'])?$_REQUEST['email']:"";
    $search_num=mysql_query("select * from usersinfo where email='".$email."'");
    
				if(mysql_num_rows($search_num)==0)
				{
                    echo json_encode(array("status"=>"-1","message"=>"Incorrect Email Address"));
                    exit;
                }
                else
				{
                    
                    $mail = new PHPMailer;
                    
                    //$mail->SMTPDebug = 3;                               // Enable verbose debug output
                    
                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = 'mail.shootback.info';  // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = 'mailuser@shootback.info';                 // SMTP username
                    $mail->Password = 'ynynyn1234';                           // SMTP password
                                              // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = 26;                                    // TCP port to connect to
                    
                    $mail->From = 'mailuser@shootback.info';
                    $mail->FromName = 'Qbox';
                    $mail->addAddress($email, $email);     // Add a recipient
                    
                    // Optional name
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $randomnumber=mt_rand(100000, 999999);
                    $mail->Subject = 'Reset Your Password';
                    $mail->Body    = 'This is your code '.$randomnumber ;
                    $mail->AltBody = 'This is your code '.$randomnumber;
                    
                    if(!$mail->send()) {
                        echo json_encode(array("status"=>"0","message"=>"Error send mail")); 
                        //echo "Mailer Error: " . $mail->ErrorInfo;
                        exit;
                    } else {
                        echo json_encode(array("status"=>"1","message"=>" send mail successfully","code"=>$randomnumber)); 
                        exit;
                    }
                    
                } 
    
    ?>