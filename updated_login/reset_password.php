<?php
if (isset($_GET['token'])) {
  $token = $_GET['token'];

  
  include('connection.php');

  // checking ng reset token, kung expire na ba
  $sql = "SELECT id, password_reset_token_expiry FROM users WHERE password_reset_token = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($user_id, $token_expiry);
  $stmt->fetch();

  if ($stmt->num_rows == 1) {
    // kung expire na
    if (strtotime($token_expiry) > time()) {
      // kung hindi pa
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);// naka hash

        //update yung password tapos icclear yung reset token
        $sql = "UPDATE users SET password = ?, password_reset_token = NULL, password_reset_token_expiry = NULL WHERE password_reset_token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $token);
        $stmt->execute();

        echo "Password reset successfully!";
        //next is balik sa login para mag login uli
        header("Location: signin_page.html");
        exit();
      }
    } else {
      echo "Password reset token has expired.";
    }
  } else {
    echo "Invalid reset token.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - Taytay Tiangge</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="reset.css">
</head>

<body>

  <a href="forgot_password.php" class="back-button">
    <i class="fas fa-arrow-left"></i> Back 
  </a>

  <div class="login-panel">
    <div class="login-logo">
      <img src="images/TaytayTianggeIcon.png" alt="Taytay Tiangge Logo">
    </div>
    <h2>Reset Your Password</h2>

    <form action="reset_password.php?token=<?php echo $token; ?>" method="POST">
      <div class="form-group">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your new password" required>
      </div>
      <button type="submit">Reset Password</button>
    </form>

    <p class="info-text">Enter a secure new password to reset your account.</p>
  </div>
</body>

</html>