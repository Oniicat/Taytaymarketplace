<?php

include "../registration-process/conn.php";




if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($password !== $confirm_password) {
        echo "<h2>Passwords do not match!</h2>";
    } else {
        // Validate password strength
        if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_])(?=.{8,})/', $password)) {
            echo "<h2>Password must be at least 8 characters long, include one uppercase letter, and one special character.</h2>";
        } else {
            // Hash the password for storage
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Check if email already exists in the database
            $sql_check = "SELECT * FROM users WHERE email = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                echo "<h2>Email already registered!</h2>";
            } else {
                // Extract the username from the email (portion before '@')
                $user_name = strstr($email, '@', true);

                // Insert new user data into the database
                $sql = "INSERT INTO users (email, password, user_name) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $email, $hashed_password, $user_name);

                if ($stmt->execute()) {
                    // Get the last inserted user ID
                    $last_id = $conn->insert_id;

                    // Redirect to shop info page with seller ID
                    header("Location: ../registration-process/shop-info.php?seller_id=" . $last_id);
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
