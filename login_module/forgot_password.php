<?php
session_start();

$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Form - Taytay Tiangge</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="forgot_password.css">
</head>

<body>

    <a href="signin_page.html" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Sign In
    </a>

    <div class="login-panel">
        <div class="login-logo">
            <img src="images/TaytayTianggeIcon.png" alt="Taytay Tiangge Logo">
        </div>
        <h2>Enter Your Email</h2>

        <form action="send.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <button type="submit">Submit</button>
        </form>

        <p class="info-text">A 6-digit OTP will be sent to your email.</p>
    </div>
</body>

</html>
