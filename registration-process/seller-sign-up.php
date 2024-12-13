<?php
include 'conn.php'; // Connect to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare the SQL statement using MySQLi
    $stmt = $conn->prepare("INSERT INTO accounts (email, first_name, last_name, password) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    // Bind the parameters
    $stmt->bind_param("ssss", $email, $first_name, $last_name, $hashed_password); // "ss" indicates two strings

    // Execute the statement
    if ($stmt->execute()) {
        // Get the last inserted ID
        $last_id = $conn->insert_id;

        // Redirect to seller-info page with the seller ID
        header("Location: add-shop.php?seller_id=" . $last_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Sign-Up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <form method="POST">
            <label for="email">Email Address:</label>
            <input type="email" name="email" required><br>

            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" required><br>

            <label for="last_name">Last name:</label>
            <input type="text" name="last_name" required><br>
            
            <label for="password">Password:</label>
            <input type="password" name="password" required><br>
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" required><br>
            
            <button type="submit">Sign Up</button>
        </form>
    </div>

    <script>
    document.querySelector('form').addEventListener('submit', function() {
        const button = this.querySelector('button[type="submit"]');
        button.disabled = true; // Disable the button
        button.textContent = "Processing..."; // Optional: Change button text
    });
</script>

</body>
</html>
