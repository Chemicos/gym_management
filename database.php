<?php
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "gym_management";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn){
    die("Something went wrong");
}
?>
