<?php
include 'conn.php'; // Connect to the database
session_start(); // Start the session


// Check if the seller_id is present in the URL query parameters
// if (isset($_GET['seller_id'])) {
//     // Store the seller_id in the session
//     $_SESSION['seller_id'] = $_GET['seller_id'];
// } else {
//     // Handle the case where seller_id is not provided
//     echo "Error: seller_id not provided.";
//     exit;
// }

$seller_id = $_SESSION['seller_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate `seller_id` from either URL or form data
    if (isset($_GET['seller_id']) && is_numeric($_GET['seller_id'])) {
        $seller_id = intval($_GET['seller_id']);
    } elseif (isset($_POST['seller_id']) && is_numeric($_POST['seller_id'])) {
        $seller_id = intval($_POST['seller_id']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid seller ID.']);
        exit();
    }

    // Fetch first_name and last_name from accounts table
    $stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE seller_id = ?");
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Seller not found in accounts table.']);
        exit();
    }

    $stmt->close();

    // Concatenate first_name and last_name to create seller_name
    $seller_name = $first_name . ' ' . $last_name;

    // Sanitize and validate form data
    $shop_name = htmlspecialchars(trim($_POST['shop_name']));
    $stall_number = htmlspecialchars(trim($_POST['stall_number']));
    $business_permit_number = htmlspecialchars(trim($_POST['business_permit_number']));
    $contact_number = htmlspecialchars(trim($_POST['contact_number']));
    $shop_description = htmlspecialchars(trim($_POST['shop_description']));
    $lazada_link = htmlspecialchars(trim($_POST['lazada_link']));
    $shopee_link = htmlspecialchars(trim($_POST['shopee_link']));

    // Handle business permit upload
    $permit_image_path = '';
    if (isset($_FILES['permit_image']) && $_FILES['permit_image']['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES['permit_image']['name']);
        $fileTmpName = $_FILES['permit_image']['tmp_name'];
        $uploadDir = 'uploads/permits/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $permit_image_path = $uploadDir . uniqid('permit_', true) . '_' . $fileName;
        if (!move_uploaded_file($fileTmpName, $permit_image_path)) {
            die(json_encode(['success' => false, 'message' => 'Error uploading the business permit image.']));
        }
    } else {
        die(json_encode(['success' => false, 'message' => 'Business permit image is required.']));
    }

    // Handle shop profile picture upload
    $shop_profile_pic_path = '';
    if (isset($_FILES['shop_profile_pic']) && $_FILES['shop_profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES['shop_profile_pic']['name']);
        $fileTmpName = $_FILES['shop_profile_pic']['tmp_name'];
        $uploadDir = 'uploads/profile_pics/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $shop_profile_pic_path = $uploadDir . uniqid('profile_', true) . '_' . $fileName;
        if (!move_uploaded_file($fileTmpName, $shop_profile_pic_path)) {
            die(json_encode(['success' => false, 'message' => 'Error uploading the shop profile picture.']));
        }
    }

    // Insert data into the database
    $query = "
        INSERT INTO registration (
            seller_id, seller_name, shop_name, stall_number, business_permit_number, permit_image,
            shop_profile_pic, contact_number, shop_description, lazada_link, shopee_link, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param(
            "issssssssss",
            $seller_id, $seller_name, $shop_name, $stall_number, $business_permit_number, $permit_image_path,
            $shop_profile_pic_path, $contact_number, $shop_description, $lazada_link, $shopee_link
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Shop profile created successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Shop Profile</title>
    <link rel="stylesheet" href="add-shop.css">

    <style>

        .profile-container img {
            width: 150px;
            height: 150px;
            border: 1px solid white;
            object-fit: cover;
        }

        #editprofimg {
            text-align: center;
        }

    </style>
</head>


<body>
<!-- Navbar --> 
<div class="custom-navbar">
    <div class="navbar-center">
        <img src="Content/New Logo.png" alt="Logo" class="navbar-logo">
    </div>
</div>


<form id="sellerForm" method="POST" enctype="multipart/form-data">
<input type="hidden" name="seller_id" value="<?php echo isset($_GET['seller_id']) ? $_GET['seller_id'] : ''; ?>">
<div class="form-container">
    <h2>Shop Information</h2>
    <div class="shop-info">
        <!-- Shop Name -->
        <div class="form-group">
            <label for="shop_name">Shop Name:</label>
            <input type="text" id="shop_name" name="shop_name" placeholder="Enter Shop Name" required>
        </div>

        <!-- 2x2 Grid for Stall Number and Contact Number -->
        <div class="form-group">
            <label for="stall_number">Stall Number:</label>
            <input type="text" id="stall_number" name="stall_number" placeholder="Enter Stall Number" required>
        </div>

        <!-- Business Permit Number -->
        <div class="form-group">
            <label for="business_permit_number">Business Permit Number:</label>
            <input type="text" id="business_permit_number" name="business_permit_number" placeholder="Enter Business Permit Number" required>
        </div>

         <!-- Upload Business Permit -->
         <div class="form-group">
            <label for="permit_image">Upload Business Permit (Required):</label>
            <input type="file" id="permit_image" name="permit_image" accept="image/*,application/pdf" required>
        </div>

        <div class="form-group">
            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" placeholder="Enter Contact Number" required>
        </div>
    </div>
</div>

<!-- Form Container for Shop Profile -->
<div class="form-container-Shop">
    <h2>Set Up Shop Profile</h2>
    <div class="shop-profile">
        <!-- User Profile Section -->
        <div class="profile-container">
            <div id="shop_profile_pic" class="profile-image" onclick="document.getElementById('user_profile').click();">
                <!-- Default Profile Image (if no image is selected) -->
                <span>Click to upload image</span>
            </div>
            <input type="file" id="user_profile" name="shop_profile_pic" accept="image/*" class="file-input" onchange="displayImage(event)" style="display: none;"required>
        </div>


        <!-- Shop Description -->
        <div class="form-group">
            <label for="shop_description">Shop Description:</label>
            <textarea id="shop_description" name="shop_description" rows="4" placeholder="Enter Shop Description" required></textarea>
        </div>

        <!-- Shop Links -->
        <div class="shop-links">
        <div class="form-group">
            <label for="shop_description">Lazada Link:</label>
                <input type="text" id="lazada_link" name="lazada_link" placeholder="Enter Link" required>
            </div>
            <div class="form-group">
            <label for="shop_description">Shopee Link:</label>
                <input type="text" id="shopee_link" name="shopee_link" placeholder="Enter Link" required>
            </div>
        </div>
    </div>
</div>

<div class="action-buttons">
        <button type="button" class="cancel-button" onclick="cancelForm()">Cancel</button>
        <button type="submit" class="submit-button">Submit for Verification</button>
    </div>
    </form>

<!-- Notification Pop-up -->
<div class="notification-popup" id="notificationPopup">
    <div class="notification-content">
        <h3>Shop Registration Complete!</h3>
        <p>Your information has been successfully submitted. It is now under review by the admin.</p>
        <p>You will be notified once your account is approved or if any further action is required.</p>
    </div>
</div>

<script>
    const form = document.getElementById("sellerForm");
    const submitButton = document.querySelector(".submit-button");
    const sellerId = <?php echo json_encode($seller_id); ?>;

    form.addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent the default form submission
        
        // Disable the submit button to prevent multiple clicks
        submitButton.disabled = true;

        const formData = new FormData(form);

        if (sellerId) {
        formData.append('seller_id', sellerId);
    }

        // Log the seller_id and URL being used
        console.log("Submitting with seller_id from session:", sellerId);
        console.log("Form URL: ?seller_id=" + formData.get('seller_id'));

        // Perform the form submission via fetch
        fetch("?seller_id=" + sellerId, {
        method: "POST",
        body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification();
                setTimeout(() => logOutAndRedirect(), 5000);
            } else {
                alert(data.message || "Error submitting your form. Please try again.");
                submitButton.disabled = false; // Re-enable button on error
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An unexpected error occurred. Please try again.");
            submitButton.disabled = false; // Re-enable button on error
        });
    });

    function cancelForm() {
        form.reset();
        window.location.href = "seller-dashboard.php"; // Redirect to home page
    }

    function showNotification() {
        // Show the notification pop-up
        document.getElementById("notificationPopup").style.display = "flex";
        
        // Timer function to hide the notification and redirect after 6 seconds
        setTimeout(function() {
            // Hide the notification
            document.getElementById("notificationPopup").style.display = "none";
            
            // Redirect to the Seller Shop Dashboard
            fetch('set_seller_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ sellerId: sellerId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect after setting the session variable
                    window.location.href = "seller-dashboard.php";
                } else {
                    alert("Failed to set session.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }, 5000); // Set to 6 seconds
    }


     // Function to display the selected image
     function displayImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const profileImage = document.getElementById('shop_profile_pic');
            profileImage.style.backgroundImage = `url(${e.target.result})`;
            profileImage.innerHTML = "";  // Clear the default text
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    </script>
</html>
