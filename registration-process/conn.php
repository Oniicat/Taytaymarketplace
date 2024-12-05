<?php
$servername = "localhost"; // Host name
$username = "root";        // Database username (use your username if it's not "root")
$password = "";            // Database password (use your password if set)
$dbname = "webdev2";       // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

