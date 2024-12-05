<?php
session_start();

include "../registration-process/conn.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check user credentials
    $sql = "SELECT users.*, shops.shop_id, shops.*, profiles.profiles_id 
            FROM users 
            INNER JOIN shops ON shops.seller_id = users.seller_id
            LEFT JOIN profiles ON profiles.shop_id = shops.shop_id
            WHERE users.email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Debugging output to check the user data
        var_dump($user); // Uncomment for debugging

        if (password_verify($password, $user['password'])) {
            // Update last login time
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

            // Redirect based on profile status
            $_SESSION['shop_id'] = $user['shop_id'];
            $_SESSION['seller_id'] = $user['seller_id'];
            $_SESSION['shop_name'] = $user['shop_name'];
            $_SESSION['user_email'] = $user['email'];

            if ($user['user_type'] === 'Admin') {
                header("Location: ../admin final na to/main.php");
                exit();
            } else {
            if (!empty($user['profiles_id'])) {
                header("Location: ../MarketplaceV3.6/MarketPlace(Seller).php");
                exit();
            } else {
                header("Location: signin_SET.PHP");
                exit();
            }
        }
        } else {
            $_SESSION['error_message'] = "Invalid password!";
            header('Location: signin_page.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Invalid email or password!";
        header('Location: signin_page.php');
        exit();
    }


}
?>
