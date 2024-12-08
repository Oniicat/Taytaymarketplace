<?php
session_start();

include "../registration-process/conn.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Query to check if the email exists and has a seller_id in the users table
    $sql = "SELECT * FROM users WHERE email = ? AND seller_id IS NOT NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the provided password
        if (password_verify($password, $user['password'])) {
            // Update the last login time
            $updateSql = "UPDATE users SET lastlogin_time = CURRENT_TIMESTAMP WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("s", $email);
            $updateStmt->execute();
            $updateStmt->close();

            // Log the activity
            $activityType = "Logged In";
            $insertSql = "INSERT INTO activity_log (user_name, activity_type, date_time) VALUES (?, ?, NOW())";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ss", $user['email'], $activityType); // Use user's email as user_name
            $insertStmt->execute();
            $insertStmt->close();

            // Set session variables
            $_SESSION['seller_id'] = $user['seller_id'];
            $_SESSION['user_email'] = $user['email'];

            // Redirect to the seller's marketplace
            header("Location: ../MarketplaceV3.6/MarketPlace(Seller).php");
            exit();
        } else {
            // Invalid password
            $_SESSION['error_message'] = "Invalid password!";
            header('Location: signin_page.php');
            exit();
        }
    } else {
        // Invalid email or seller_id does not exist
        $_SESSION['error_message'] = "Invalid email or seller account!";
        header('Location: signin_page.php');
        exit();
    }
}


?>
