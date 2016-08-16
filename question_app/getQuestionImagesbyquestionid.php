<?php

include('database/connection.php');
include('checktoken.php');

$questionid=isset($_REQUEST['questionid'])?$_REQUEST['questionid']:'';


if($questionid!="")
    {

                $getquestionImages=mysql_query("SELECT * FROM imagesquestion WHERE  questionid ='".$questionid."'") or die(mysql_error());
                $data=array();
			    if(mysql_num_rows($getquestionImages)>0)
				{
				        $i=0;
        				while($row=mysql_fetch_array($getquestionImages))
        					{
        						$data[$i]['imagequestionid']=$row['imagequestionid'];
        						$data[$i]['imagethumb']=$row['imagethumb'];
        						$data[$i]['image']=$row['image'];
        						$data[$i]['questionid']=$row['questionid'];
                                 $i++;

        					}
				}
              	echo json_encode(array("status"=>"1","message"=>"","data"=>$data));
                exit;
    }
else
    {
           echo json_encode(array("status"=>"0","message"=>"please sent questionid"));
           exit;
    }


?>