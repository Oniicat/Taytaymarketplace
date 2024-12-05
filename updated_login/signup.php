<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mini_db";

$conn = new mysqli($servername, $username, $password, $dbname);



if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // kung pareho ba yung password sa confirm password
    if ($password !== $confirm_password) {
        echo "<h2>Passwords do not match!</h2>";
    } else {
        // kailangan may isang capital lettter at symbol para sa strong password
        if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_])(?=.{8,})/', $password)) {
            echo "<h2>Password must be at least 8 characters long, include one uppercase letter, and one special character.</h2>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

           // checking kung yung email na isa-signup ay meron na para walang duplication
            $sql_check = "SELECT * FROM users WHERE email = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                echo "<h2>Email already registered!</h2>";
            } else {
                //username
                $user_name = strstr($email, '@', true); // Extracts portion before '@'

                // insert yung mga nasa textbox
                $sql = "INSERT INTO users (email, password, user_name) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $email, $hashed_password, $user_name);

                if ($stmt->execute()) {
                    // next page
                    header("Location: signup_INFO.html");
                    exit();
                } else {
                    echo "<h2>Sign-up failed. Please try again.</h2>";
                }
            }

            $stmt_check->close();
        }
    }
}

$conn->close();

?>
