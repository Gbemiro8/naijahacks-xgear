<?php
$server = "localhost";
$username = "root";
$password = "mysql";
$database = "team_foobaa";
//connect to database
$conn = mysqli_connect($server, $username, $password, $database);
if(!$conn){
    die("connection not sucessful". mysqli_connect_error());
}
?>