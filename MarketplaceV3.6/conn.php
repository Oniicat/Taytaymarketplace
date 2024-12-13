<?php 

$conn = mysqli_connect("localhost", "root", "", "webdev2"); //server , username, password, database

if (!$conn){
    die("Connection failed " . mysqli_connect_error());
}

?>