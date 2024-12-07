<?php

include "../registration-process/conn.php";




if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $first_name =  $_POST['first_name'];
    $middle_name =  $_POST['middle_name'];
    $last_name =  $_POST['last_name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    // dapat  parehas yung password
    if ($password !== $confirm_password) {
        echo "<h2>Passwords do not match!</h2>";
    } else {
        // para halimaw yung password
        if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_])(?=.{8,})/', $password)) {
            echo "<h2>Password must be at least 8 characters long, include one uppercase letter, and one special character.</h2>";
        } else {
            // hashing sa database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // kung existing naba yung email
            $sql_check = "SELECT * FROM users WHERE email = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                echo "<h2>Email already registered!</h2>";
            } else {
                // emeng username from email hahaha
                $user_name = strstr($email, '@', true);

                // insert na lahat
                $sql = "INSERT INTO users (email, first_name, middle_name, last_name, password, user_name) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $email,$first_name, $middle_name, $last_name, $hashed_password, $user_name);


                //activity log ni josh mojica(nakikita ka nya, dapat masipag ka)
                $activityType = "Created an Account";
                $insert_sql = "INSERT INTO activity_log (user_name, activity_type, date_time) VALUES (?, ?, NOW())";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("ss", $email, $activityType);
                $insert_stmt->execute();
                $insert_stmt->close();


                if ($stmt->execute()) {

                    $last_id = $conn->insert_id;

                    // forda next page mossing
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
