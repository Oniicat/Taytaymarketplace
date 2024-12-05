<?php
session_start();


include('connection.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $entered_otp = $_POST['otp'];
  $email = $_POST['email'];

  //dito mareretrieve yung otp tsaka yung otp expiry sa database
  $sql = "SELECT otp, otp_expiry FROM users WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($stored_otp, $otp_expiry);
  $stmt->fetch();

  // check yung otp tsaka expiry
  if ($stored_otp == $entered_otp && strtotime($otp_expiry) > time()) {
    // dito generation ng password reset token(reset token yung pang access ng database para ma reset yung password)
    $reset_token = bin2hex(random_bytes(32)); // generate ng reset token 32bytes(64 characters)
    $reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));  // create ng token expiry time

    // insert nya yung reset token tsaka yung expiration nya na 1 hour
    $sql = "UPDATE users SET password_reset_token = ?, password_reset_token_expiry = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $reset_token, $reset_token_expiry, $email);
    $stmt->execute();

    // dito yung sa next page para ma reset na yung passwprd
    header("Location: reset_password.php?token=$reset_token");
    exit();
  } else {
    echo "Invalid OTP or OTP has expired.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OTP Verification - Taytay Tiangge</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="verify.css">
</head>

<body>

  <a href="forgot_password.php" class="back-button">
    <i class="fas fa-arrow-left"></i> Back 
  </a>

  <div class="login-panel">
    <div class="login-logo">
      <img src="images/TaytayTianggeIcon.png" alt="Taytay Tiangge Logo">
    </div>
    <h2>Enter OTP to Verify</h2>

    <form action="verify_otp.php" method="POST">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="form-group">
        <label for="otp">OTP:</label>
        <input type="text" id="otp" name="otp" placeholder="Enter the OTP" required>
      </div>
      <button type="submit">Verify OTP</button>
    </form>

    <p class="info-text">Please enter the 6-digit OTP sent to your email.</p>
  </div>
</body>

</html>