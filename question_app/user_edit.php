<?php
include('database/connection.php');
include('checktoken.php');
define('UPLOAD_DIR', '../question_app/uploads/');
define('UPLOAD_DIR_THUMB', '../question_app/uploads/thumbs/');


$server=$_SERVER['HTTP_HOST'];
$name = isset($_REQUEST['name'])?$_REQUEST['name']:""; 
$email = isset($_REQUEST['email'])?$_REQUEST['email']:""; 
//$password = isset($_REQUEST['password'])?$_REQUEST['password']:""; 
//$mobile = isset($_REQUEST['mobile'])?$_REQUEST['mobile']:"";
$school = isset($_REQUEST['school'])?$_REQUEST['school']:"";
$grade = isset($_REQUEST['grade'])?$_REQUEST['grade']:"";
$city = isset($_REQUEST['city'])?$_REQUEST['city']:"";
$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
$timezone=isset($_REQUEST['timezone'])?$_REQUEST['timezone']:"America/Los_Angeles";
	 
//------------------------------------	
$lname = isset($_REQUEST['lname'])?$_REQUEST['lname']:"";
$dob = isset($_REQUEST['dob'])?$_REQUEST['dob']:"";
$gender = isset($_REQUEST['gender'])?$_REQUEST['gender']:"";
$state = isset($_REQUEST['state'])?$_REQUEST['state']:"";
$skill_and_interest = isset($_REQUEST['skill_and_interest'])?$_REQUEST['skill_and_interest']:"";	
//$id = isset($_REQUEST['id'])?$_REQUEST['id']:""; 
//$pic_url = isset($_REQUEST['profile_pic'])?$_REQUEST['profile_pic']:"";
$imageid=isset($_REQUEST['imageid'])?$_REQUEST['imageid']:"";
$action=isset($_REQUEST['action'])?$_REQUEST['action']:"";
$about=isset($_REQUEST['about'])?$_REQUEST['about']:"";
$workat=isset($_REQUEST['workat'])?$_REQUEST['workat']:"";
//$img=isset($_REQUEST['img'])?$_REQUEST['img']:"";
    
   // echo json_encode(array("status"=>"1","message"=>$action));
    //exit;
// $encrypted_txt = encrypt_decrypt('encrypt', $password);
if($user_id!='')
	{
        
        if($action=="delete")
        {
          $query="update  usersinfo set thumb='',profile_pic='' where thumb=(select thumb from imagesuser where id=".$imageid.")";
           $result = mysql_query($query) or die(mysql_error());

            $query="delete from imagesuser where id=".$imageid;

            $result = mysql_query($query) or die(mysql_error());
            
            $getimagesuser=mysql_query("select * from imagesuser where userid=".$user_id);

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


             echo json_encode(array("status"=>"1","message"=>"delete photos successfully","imagesuse"=>$arrimagesuser)) ;
             exit;
        }
        else if($action=="markprofile")
        {
            $query="update  usersinfo set thumb=(select thumb from imagesuser where id=".$imageid."),profile_pic=(select attachment from imagesuser where id=".$imageid.") where id= ".$user_id;
            $result = mysql_query($query) or die(mysql_error());

            $query="update  imagesuser set isselected=0";
            $result = mysql_query($query) or die(mysql_error());

            $query="update  imagesuser set isselected=1 where id=".$imageid ;
            $result = mysql_query($query) or die(mysql_error());

            echo json_encode(array("status"=>"1","message"=>"mark as profile successfully")) ;
            exit;

        }

       else if($action=="saveprofile")
        {
            $query="UPDATE usersinfo SET name='".$name."',lname='".$lname."',dob='".$dob."',gender='".$gender."',state='".$state."',skill_and_interest='".$skill_and_interest."',email='".$email."',school='".$school."',timezone='".$timezone."',grade='".$grade."',city ='".$city."',aboutme ='".$about."',workat ='".$workat."' WHERE id=".$user_id."";

            $result = mysql_query($query) or die(mysql_error());
            if($result)
            {
                echo json_encode(array("status"=>"1","message"=>"successfully updated"));
                exit;
            }
            
            else
            {
                
                echo json_encode(array("status"=>"0","message"=>"could not be updated")) ;
                exit;
            }
        }
        
		////////////////////////////////////////////////////////////////////////////
		if(isset($_POST['img']))
			{
				$img = $_POST['img'];
				$img = str_replace('data:image/jpg;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$data = base64_decode($img);
				$newFileName = uniqid().'.jpg';
				$file = UPLOAD_DIR.$newFileName;
				$success = file_put_contents($file, $data);
				$success ? $file : 'Unable to save the file.';		
				$serverName=$_SERVER['SERVER_NAME']."/question_app/uploads/";
				$url="http://".$serverName.$newFileName;
				
				$thumb=createImage($file,120,$newFileName);
				
                if($action=="addphoto")
                {
                    $query="insert into imagesuser(userid,attachment,thumb) values(".$user_id .",'".$url."','".$_SERVER['SERVER_NAME']."/question_app/uploads/thumbs/".$thumb."')";
                    $result = mysql_query($query) or die(mysql_error());
                    
                    
                    $getimagesuser=mysql_query("select * from imagesuser where userid=".$user_id);
                    
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

                    

                    echo json_encode(array("status"=>"1","message"=>"add photos successfully","imagesuse"=>$arrimagesuser)) ;
                    exit;
                    
                }
                
                
                
				//$query="UPDATE usersinfo SET name='".$name."',email='".$email."',school='".$school."',grade='".$grade."',city ='".$city."',profile_pic='".$url."',thumb='".$_SERVER['SERVER_NAME']."/question_app/uploads/thumbs/".$thumb."' WHERE id=".$user_id."";
				
                
                $query="UPDATE usersinfo SET name='".$name."',lname='".$lname."',dob='".$dob."',gender='".$gender."',state='".$state."',skill_and_interest='".$skill_and_interest."',email='".$email."',school='".$school."',timezone='".$timezone."',grade='".$grade."',city ='".$city."',profile_pic='".$url."',thumb='".$_SERVER['SERVER_NAME']."/question_app/uploads/thumbs/".$thumb."' WHERE id=".$user_id."";
				 
				$result = mysql_query($query) or die(mysql_error());
				if($result)
					{					 
						echo json_encode(array("status"=>"1","message"=>"successfully updated"));
						exit;
					}

				else
					{   
						
						echo json_encode(array("status"=>"0","message"=>"could not be updated")) ;
						exit;
					}
			}
		else
			{
				
				 
			}
 
	}
else
	{
		echo json_encode(array("status"=>"0","message"=>"please provide user id")) ;
		exit;
	}

?>

<?php
function  createImage( $upload, $newWidth , $file_name){
	
	if (!$info = getimagesize($upload) )
	return false;
	
	//create crude aspect ratio:
	$aspect = $info[0] / $info[1];
	//$newHeight = round( $newWidth/$aspect );
	
	
	$newHeight = floor( $info[1] * ( $newWidth / $info[0] ) );
	$mimetype=$info['2'];
	
	if( $mimetype == 2 )
	$src = @imagecreatefromjpeg("$upload");
	elseif( $mimetype == 1 )
	$src = @imagecreatefromgif("$upload");
	elseif( $mimetype == 3 )
	$src = @imagecreatefrompng("$upload");
	
	if ( !$src )
	return false;
	//echo $src;
	
	$tmp = @imagecreatetruecolor( $newWidth, $newHeight );
	imagecopyresampled( $tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $info[0], $info[1] );
	 // save thumbnail into a file
			$t = 'thumb_'.$file_name;
			imagejpeg( $tmp, UPLOAD_DIR_THUMB.$t );
			//$b = "INSERT INTO `tb_thumbnail` (`album_id`,`thumbnail`) VALUES ('".$aid."','".$t."')";
			//$datathumb = mysql_query($b);
			return $t;
	}

?>