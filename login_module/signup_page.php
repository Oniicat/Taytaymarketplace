<?php

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


$sql = "SELECT org_name FROM tb_org";
$result = $conn->query($sql);


$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['org_name'];
    }
} else {
    echo "No organization found.";
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Taytay Tiangge</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="signup.css" rel="stylesheet">

    <style>
        .back-btn {
        top: 1.5%;
        left: 1%;
        background-color: #712798;
        color: #ffffff; 
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
        background-color: rgb(255, 255, 255); 
        color: #712798;
        transform: scale(1.05); 
    }
    .input-container {
    display: flex;
    align-items: center;
    position: relative;
}

.input-container input {
    width: 100%;
    padding: 10px;
    padding-right: 40px; /* Adjust for icon spacing */
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    outline: none;
    box-sizing: border-box;
}

.input-icon {
    position: absolute;
    right: 10px;
    color: #aaa;
}

.input-container input:focus {
    border-color: #712798;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    display: block;
    margin-bottom: 5px;
}

.form-group select {
    width: 100%;
    height: 40px;
    padding: 5px 10px;
    font-size: 14px;
    color: #555;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
    appearance: none;
    cursor: pointer;
}

.form-group select:focus {
    outline: none;
    border-color: #007bff; /* Adjust to your theme's primary color */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.form-group select option {
    padding: 5px;
    background: #fff;
    color: #333;
}

.form-group select option:hover {
    background: #f7f7f7;
}

      </style>
</head>
<body>
    <a href="../MarketplaceV3.6/Market_Place_Dashboard.php" class="back-btn">Back</a>

    <form action="signup.php" method="POST">
    <div class="signup-panel">
        <div class="signup-logo">
            <img src="<?php echo (file_exists('logo_path.txt') && trim(file_get_contents('logo_path.txt'))) ? file_get_contents('logo_path.txt') : 'logo.png'; ?>" alt="Logo">
        </div>

        <div class="form-container">

            <!-- Left Side -->
            <div class="left-side">
               
                  <!-- UserName -->
                  <div class="form-group">
                    <label for="first-name">Username</label>
                    <div class="input-container">
                        <input type="text" id="user_name" name="user_name" required>
                        <span class="input-icon">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                </div>

                <!-- First Name -->
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <div class="input-container">
                        <input type="text" id="first-name" name="first_name" required>
                        <span class="input-icon">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                </div>


                <!-- Middle Name -->
                <div class="form-group">
                    <label for="middle-name">Middle Name</label>
                    <div class="input-container">
                        <input type="text" id="middle-name" name="middle_name">
                        <span class="input-icon">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                </div>
                <!-- Last Name -->
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <div class="input-container">
                        <input type="text" id="last-name" name="last_name" required>
                        <span class="input-icon">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="signup-org">Organization Name</label>
                    <select id="signup-org" name="organization" class="form-control" required>
                        <option value="">Select Organization</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
          
                 <!-- Payment -->
                
                
                


            <!-- Right Side -->
            <div class="right-side">
                 <!-- Email -->
                 <div class="form-group">
                    <label for="signup-email">Email</label>
                    <div class="email-container">
                        <input type="email" id="signup-email" name="email" required>
                        <span class="email-icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="form-group">
                    <label for="signup-password">Password</label>
                    <div class="password-container">
                        <input type="password" id="signup-password" name="password" required>
                        <span class="password-toggle" id="toggle-signup-password">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="password-container">
                        <input type="password" id="confirm-password" name="confirm_password" required>
                        <span class="password-toggle" id="toggle-confirm-password">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="signup-email">Payment Method</label>
                    <div class="email-container">
                        <input type="string" id="signup-email" name="email" required>
                        <span class="email-icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms & Privacy Links -->
        <div class="terms-links">
            <label>
                <input type="checkbox" id="agree-terms" name="terms" required>
                <a href="#" id="open-terms-signup">I agree to the Terms & Conditions</a>
            </label>
            <div style="margin-bottom: 15px;"></div> <!-- Adjust gap here -->
            <label>
                <input type="checkbox" id="agree-privacy" name="privacy" required>
                <a href="#" id="open-privacy-signup">I agree to the Data Privacy Policy</a>
            </label>
        </div>

        <button type="submit" id="signup-btn">Sign Up</button>
    </div>
</form>

    <div class="login-container">
        <div class="logintextsgroup">
        <h2>Have an account?</h2>
        <p>You may proceed by signing</p>
        <p>in your account.</p>  
        <form action="signin_page.php">
        <button><b>Log In</b></button>
    </form>
    </div>  
      </div>





    <script>
        // Toggle Password Visibility for Sign Up
        document.getElementById('toggle-signup-password').addEventListener('click', function() {
            const passwordField = document.getElementById('signup-password');
            const icon = this.querySelector('i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        document.getElementById('toggle-confirm-password').addEventListener('click', function() {
            const confirmPasswordField = document.getElementById('confirm-password');
            const icon = this.querySelector('i');

            if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                confirmPasswordField.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
        

        document.querySelector('form').addEventListener('submit', function(event) {
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    // Check if the passwords match
    if (password !== confirmPassword) {
        event.preventDefault(); // Prevent form submission
        alert('Passwords do not match. Please ensure both passwords are identical.');
    }
});



        // Open Terms Popup
        document.getElementById('open-terms-signup').addEventListener('click', function() {
            document.getElementById('terms-popup').style.display = 'block';
        });

        // Close Terms Popup
        function closeTermsPopup() {
            document.getElementById('terms-popup').style.display = 'none';
        }

        // Open Privacy Popup
        document.getElementById('open-privacy-signup').addEventListener('click', function() {
            document.getElementById('privacy-popup').style.display = 'block';
        });

        // Close Privacy Popup
        function closePrivacyPopup() {
            document.getElementById('privacy-popup').style.display = 'none';
        }

     


  document.querySelector('form').addEventListener('submit', function(event) {
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const termsAccepted = document.getElementById('agree-terms').checked;
    const privacyAccepted = document.getElementById('agree-privacy').checked;

    // Check if the passwords match
    if (password !== confirmPassword) {
        event.preventDefault(); // Prevent form submission
        alert('Passwords do not match. Please ensure both passwords are identical.');
        return;
    }

    // Check if Terms and Privacy checkboxes are checked
    if (!termsAccepted || !privacyAccepted) {
        event.preventDefault(); // Prevent form submission
        alert('You must agree to the Terms & Conditions and the Data Privacy Policy before signing up.');
        return;
    }
});


document.querySelector('form').addEventListener('submit', function(event) {
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const termsAccepted = document.getElementById('agree-terms').checked;
    const privacyAccepted = document.getElementById('agree-privacy').checked;

    // Check if the passwords match
    if (password !== confirmPassword) {
        event.preventDefault(); // Prevent form submission
        alert('Passwords do not match. Please ensure both passwords are identical.');
        return;
    }

    // Check if Terms and Privacy checkboxes are checked
    if (!termsAccepted || !privacyAccepted) {
        event.preventDefault(); // Prevent form submission
        alert('You must agree to the Terms & Conditions and the Data Privacy Policy before signing up.');
        return;
    }

    // Password Validation: At least 1 uppercase, 1 symbol, and 8 characters
    const passwordStrengthRegex = /^(?=.*[A-Z])(?=.*[\W_])(?=.{8,})/;
    if (!passwordStrengthRegex.test(password)) {
        event.preventDefault(); // Prevent form submission
        alert('Your password must be at least 8 characters long, contain at least one uppercase letter, and one special character.');
        return;
    }
});

    </script>
</body>
</html>
