<?php
/*

*/
$username = "freev_16095465";
$password = "12345678";
$hostname = "sql202.freevnn.com";

error_reporting(0);
$conn = mysql_connect($hostname, $username, $password) ;
if($conn) {
$db = mysql_select_db("freev_16095465_qbox");

}
?>