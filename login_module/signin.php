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

            // Check if the user has a registration record
            $checkRegistrationSql = "SELECT COUNT(*) as count FROM registration WHERE seller_id = ?";
            $checkRegistrationStmt = $conn->prepare($checkRegistrationSql);
            if ($checkRegistrationStmt === false) {
                echo json_encode(['success' => false, 'message' => 'Failed to check registration status']);
                exit();
            }
            $checkRegistrationStmt->bind_param("i", $user['seller_id']);
            $checkRegistrationStmt->execute();
            $checkResult = $checkRegistrationStmt->get_result();

            if ($checkResult && $checkResult->num_rows > 0) {
                $row = $checkResult->fetch_assoc();

                // Set session variables and redirect based on registration count
                $_SESSION['seller_id'] = $user['seller_id'];
                $_SESSION['user_email'] = $user['email'];

                if ($row['count'] == 0) {
                    // No registration found, redirect to the Add Shop page
                    header("Location: ../registration-process/add-shop.php");
                    exit();
                } else {
                    // Redirect to the seller dashboard
                    header("Location: ../registration-process/seller-dashboard.php");
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error checking registration']);
                exit();
            }
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
} else {
    // Handle case when the request method is not POST
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Close the connection at the end of the script
$conn->close();
?>
