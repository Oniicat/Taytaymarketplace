<?php

$servername = "localhost";
$email = "root";
$password = "";
$dbname = "webdev2";

$conn = new mysqli($servername, $email, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
