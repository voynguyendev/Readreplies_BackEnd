<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

</head>

<body>
			               <form  method="post" enctype="multipart/form-data">
   					 Select image to upload:
	
				<input type="text" style="display:none;" name="upksdtpid" class="upksdtpid">
    			<input type="file" name="fileToUpload" id="fileToUpload">
   			    <input type="submit" value="Upload Image" name="submit">
				</form> 
</body>
</html>
<?php
define('UPLOAD_DIR', '../question_app/question_images/');
define('UPLOAD_DIR_THUMB', '../question_app/question_images/thumbs/');

$target_dir =UPLOAD_DIR;
$name = time()."_image_test.png";
$upksdtpid=isset($_REQUEST['upksdtpid'])?$_REQUEST['upksdtpid']:"";

$target_file = $target_dir . $name;

echo $target_file;
if(!is_writable(UPLOAD_DIR))
	echo 'permission error';

if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
 
	   $file = UPLOAD_DIR.$name;
       $serverName=$_SERVER['SERVER_NAME']."/question_app_test/question_images/";
	   $filename=$serverName.$name;
	   $thumb= $serverName."thumbs/".createImage($file,200,$name);
	   
	  // $checkpicture=mysql_query("update tblkhaosatdetailpicture set IsEdit='1',thumb='$thumb',pictureurl='$filename' where id='$upksdtpid'") or die(mysql_error());
	   
	   
	   
	   
	    
    } else {
        
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
	$src = @imagecreatefromjpeg($upload);
	elseif( $mimetype == 1 )
	$src = @imagecreatefromgif($upload);
	elseif( $mimetype == 3 )
	$src = @imagecreatefrompng($upload);
	
	if ( !$src )
	return false;
	//echo $src;
	
	$tmp = @imagecreatetruecolor( $newWidth, $newHeight );
	imagecopyresampled( $tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $info[0], $info[1] );
	 // save thumbnail into a file
			$t = 'thumb_'.$file_name;
			imagejpeg( $tmp, UPLOAD_DIR_THUMB.$t );
			return $t;
	}

?>