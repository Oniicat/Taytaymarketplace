<?php
session_start();

function getDatabaseConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// open database connection
$conn = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if email exists in the users table
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the password is correct
        if (password_verify($password, $user['password'])) {
            // Log the last login time
            $updateSql = "UPDATE users SET lastlogin_time = CURRENT_TIMESTAMP WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("s", $email);
            $updateStmt->execute();
            $updateStmt->close();

            // Activity log of logins
            $activityType = "Logged In";
            $insert_sql = "INSERT INTO activity_log (user_name, activity_type, date_time) VALUES (?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ss", $user['email'], $activityType); // Use user's email as user_name
            $insert_stmt->execute();
            $insert_stmt->close();

            // Start the session and redirect to the next page
            $_SESSION['user_email'] = $user['email'];
            header("Location: signin_SETUP.html");
            exit();
        } else {
            // Invalid password, show simple popup
            header('location: signin_page.php');
            $_SESSION['error_message'] = "Invalid email or password!";
        }
    } else {
        // Invalid email, show simple popup
        header('location: signin_page.php');
        $_SESSION['error_message'] = "Invalid email or password!";
      }

    $stmt->close();
    $conn->close();
}
?>