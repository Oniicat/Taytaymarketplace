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
            $sellerId = $user['seller_id'];

        // Query to check the seller ID in the registration table
            $checkRegistrationSql = "SELECT COUNT(*) as count FROM registration WHERE seller_id = ?";
            $checkRegistrationStmt = $conn->prepare($checkRegistrationSql);
            if ($checkRegistrationStmt === false) {
                echo json_encode(['success' => false, 'message' => 'Failed to check registration status']);
                exit();
            }
            $checkRegistrationStmt->bind_param("i", $sellerId);
            $checkRegistrationStmt->execute();
            $registrationResult = $checkRegistrationStmt->get_result();
            $registrationCount = ($registrationResult && $registrationResult->num_rows > 0) ? $registrationResult->fetch_assoc()['count'] : 0;

            // Query to check the seller ID in the shops table
            $checkShopSql = "SELECT COUNT(*) as count FROM shops WHERE seller_id = ?";
            $checkShopStmt = $conn->prepare($checkShopSql);
            if ($checkShopStmt === false) {
                echo json_encode(['success' => false, 'message' => 'Failed to check shop status']);
                exit();
            }
            $checkShopStmt->bind_param("i", $sellerId);
            $checkShopStmt->execute();
            $shopResult = $checkShopStmt->get_result();
            $shopCount = ($shopResult && $shopResult->num_rows > 0) ? $shopResult->fetch_assoc()['count'] : 0;

            $_SESSION['seller_id'] = $user['seller_id'];
            $_SESSION['user_email'] = $user['email'];

            // Check if the seller ID exists in either table
            if ($registrationCount > 0 || $shopCount > 0) {
                // Set session variables and redirect to the seller dashboard
                header("Location: ../registration-process/seller-dashboard.php");
                exit();
            } else {
                // Redirect to the Add Shop page if the seller ID is not found in both tables
                header("Location: ../registration-process/add-shop.php");
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
