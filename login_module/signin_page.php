<?php
session_start(); // Start session at the top of the file


include '../registration-process/conn.php';
$content_keys = ['Terms', 'DataPrivacy'];

// Prepare an array to store the fetched content
$content_texts = [];

// Function to add a new line after each sentence
function addNewLinePerSentence($text) {
    // Ensure input is valid
    if (!$text) return '';
    // Add a `<br>` tag after sentence-ending punctuation (., ?, !)
    return preg_replace('/(?<=[!.?:])(?=\s|$)/', '<br>', $text);
}

// Loop through each content key and fetch its corresponding content
foreach ($content_keys as $content_key) {
    $query = "SELECT content_text FROM textchange WHERE content_key = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $content_key);
    $stmt->execute();
    $stmt->bind_result($content_text);
    $stmt->fetch();

    // Process the fetched content to add new lines between sentences
    $content_texts[$content_key] = addNewLinePerSentence($content_text);
    $stmt->close(); // Close the statement for the next iteration
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In - Taytay Tiangge</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="signin.css">

  <style>
    .back-btn {
    top: 3%;
    left: 3%;
    background-color: white; 
    color: #712798; 
    padding: 0.5rem 1rem; 
    border: 2px solid #712798; 
    border-radius: 5px; 
    font-size: 1rem; 
    position: absolute;
    text-decoration: none; 
    display: inline-block; 
    cursor: pointer;
    z-index: 1001; 
    transition: background-color 0.3s, transform 0.3s, color 0.3s;
}

/* Hover Effect */
.back-btn:hover {
    background-color: gray; 
    color: white;
    transform: scale(1.05); 
}
  </style>
</head>

<body>

  <form action="signin.php" method="POST">
    <div class="login-panel">
      <div class="login-logo">
        <img src="images/TaytayTianggeIcon.png" alt="Logo">
      </div>

      <!-- Email -->
      <div class="form-group">
        <label for="email">Email</label>
        <div class="email-container">
          <input type="email" id="email" name="email" required>
          <span class="email-icon">
            <i class="fas fa-envelope"></i>
          </span>
        </div>
      </div>

      <!-- Password -->
      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-container">
          <input type="password" id="password" name="password" required>
          <span class="password-toggle" id="toggle-password">
            <i class="fas fa-eye"></i>
          </span>
        </div>
      </div>

      <!-- Forgot Password Link -->
      <p class="forgot-password">
        <a href="forgot_password.php">Forgot Password?</a>
      </p>

      <button type="login" id="loginButton">Log In</button>

      <!-- Terms Links Below the Login Panel -->
      <div class="terms-links">
        <a href="javascript:void(0);" id="open-terms-signup">Terms & Conditions</a> |
        <a href="javascript:void(0);" id="open-privacy-signup">Data Privacy</a>
      </div>
    </div>
  </form>

  <!-- Error Popup -->
  <div class="popup" id="error-popup" style="display: none;">
    <div class="popup-content">
      <h2>Error</h2>
      <p id="error-message"></p>
      <button class="close-btn" onclick="closeErrorPopup()">Close</button>
    </div>
  </div>

  <!-- Terms Popup Modal -->
  <div class="popup" id="terms-popup">
        <div class="popup-content">
            <h2>Terms and Conditions</h2>
            <p class="details-text"><?php echo $content_texts['Terms']; ?></p>
           
            <button class="close-btn" onclick="closeTermsPopup()">Close</button>
        </div>
    </div>

    <!-- Privacy Popup Modal -->
    <div class="popup" id="privacy-popup">
        <div class="popup-content">
            <h2>Data Privacy Policy</h2>
            <p class="details-text"><?php echo $content_texts['DataPrivacy']; ?></p>
           
            <button class="close-btn" onclick="closePrivacyPopup()">Close</button>
        </div>
    </div>
  </div>

  <div class="signin-container">
  <a href="../MarketplaceV3.6/Market_Place_Dashboard.php" class="back-btn">Back</a>
    <div class="signintextsgroup">
      <h2>Doesn't have an account?</h2>
      <p>Please create an account before</p>
      <p>Logging in.</p>
      <form action="signup_page.">
        <button><b>Sign Up</b></button>
      </form>
    </div>
  </div>

  <script>
    // Show error message in popup if session error_message is set
    <?php if (isset($_SESSION['error_message'])): ?>
      // Set the error message
      const errorMessage = "<?php echo $_SESSION['error_message']; ?>";
      document.getElementById('error-message').textContent = errorMessage;
      document.getElementById('error-popup').style.display = 'block'; // Show error popup

      // Clear the error message from the session after displaying
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    // Function to close the error popup
    function closeErrorPopup() {
      document.getElementById('error-popup').style.display = 'none';
    }

    // Function to hide/show the password
    document.getElementById('toggle-password').addEventListener('click', function () {
      const passwordField = document.getElementById('password');
      const icon = this.querySelector('i');

      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        passwordField.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    });

    // Open Terms
    document.getElementById('open-terms-signup').addEventListener('click', function () {
      document.getElementById('terms-popup').style.display = 'block';
    });

    // Close Terms
    function closeTermsPopup() {
      document.getElementById('terms-popup').style.display = 'none';
    }

    // Open Privacy
    document.getElementById('open-privacy-signup').addEventListener('click', function () {
      document.getElementById('privacy-popup').style.display = 'block';
    });

    // Close Privacy
    function closePrivacyPopup() {
      document.getElementById('privacy-popup').style.display = 'none';
    }
  </script>

</body>

</html>
