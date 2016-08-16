<?php
/*

*/
$username = "qbox2015";
$password = "tpcXAscU7m3GsUj9";
$hostname = "localhost";

error_reporting(0);
$conn = mysql_connect($hostname, $username, $password) ;
if($conn) {
$db = mysql_select_db("Qbox2015");

}
else
{
echo "error";
}
?>