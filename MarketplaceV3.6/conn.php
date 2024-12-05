<?php 

$conn = mysqli_connect("localhost", "root", "1234", "db_market"); //server , username, password, database

if (!$conn){
    die("Connection failed " . mysqli_connect_error());
}

?>