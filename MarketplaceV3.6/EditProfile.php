<?php
include '../registration-process/conn.php';

$user_id = 1; // naka manual set yung user id | hindi pa naka base kung sino nag log in

$sql = "SELECT * FROM tb_users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="EditProfile.css">
    <title>Edit Shop Profile</title>
<style>
.custom-navbar {
    background-color: white ;
    padding: 15px 30px;
    position: fixed;
    top: 0px;
    width: 100%;
    z-index: 1000;
    display: flex;
    justify-content: center;
    /* Center the content horizontally */
    align-items: center;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    /* Subtle shadow */
}

.navbar-center {
    display: flex;
    align-items: center;
    gap: 20px;
    /* Space between elements */
    max-width: 900px;
    /* Optional: limit max width */
    width: 100%;
    /* Allow it to fill available space */
}

/* Logo Image */
.navbar-center .navbar-logo {
    width: 110px; /* Default size */
    height: auto; /* Maintain aspect ratio */
    margin-right: 40px; /* Push logo to the left by creating space to its right */
    margin-left: -210px; /* Add slight spacing from the edge */
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .navbar-center .navbar-logo {
        width: 50px; /* Slightly smaller size for smaller screens */
        margin-left: 10px; /* Adjust spacing from the edge */
    }
}

@media (max-width: 480px) {
    .navbar-center .navbar-logo {
        width: 40px; /* Even smaller size for very small screens */
        margin-left: 8px; /* Further adjust spacing */
    }
}

/* Back Button Styles */
.back-btn {
    margin-left: 860px; /* Positioning adjustment */
    background-color: white; /* Button background color */
    color: #712798; /* Text color to match the outline */
    padding: 0.5rem 1rem; /* Padding for button size */
    border: 2px solid #712798; /* Add outline with #712798 */
    border-radius: 5px; /* Rounded corners */
    font-size: 1rem; /* Adjust font size */
    text-decoration: none; /* Remove underline */
    display: inline-block; /* Ensure it behaves like a button */
    cursor: pointer; /* Pointer cursor on hover */
    z-index: 1001; /* Ensure it is above other elements */
    transition: background-color 0.3s, transform 0.3s, color 0.3s;
}

/* Hover Effect */
.back-btn:hover {
    background-color: #712798; /* Darker background on hover */
    color: white; /* Text becomes white on hover */
    transform: scale(1.05); /* Slight scaling effect */
}


/* Responsive Adjustments */
@media (max-width: 768px) {
    .back-btn {
        padding: 0.4rem 0.8rem; /* Smaller padding for small screens */
        font-size: 0.9rem; /* Adjust font size */
    }
}
</style>
</head>
<body>
<!-- Navbar -->
<div class="custom-navbar">
    <div class="navbar-center">
        <a href="Seller_Dashboard.php">
        <img src="Content/New Logo.png" alt="Logo" class="navbar-logo">
        </a>
        <a href="UserProfile.php" class="back-btn">Back</a>
    </div>
</div>

<!-- Edit Shop Profile Container -->
<div class="edit-shop-profile-container">
    <div class="edit-shop-profile-header">
        <h2>Edit Shop Profile</h2>
    </div>
    <div class="edit-shop-profile-content">
        <!-- Profile Image -->
        <div class="profile-image-container" onclick="changeProfileImage()">
            <img id="profile-image" src="<?php echo $user['profile_pic']; ?>" alt="Shop Profile" class="profile-image">
            <input type="file" id="image-input" style="display: none;" accept="image/*" onchange="previewImage(event)">
        </div>

        <!-- Shop Description -->
        <textarea class="shop-description" placeholder="Enter your shop description..."><?php echo $user['shop_desc']; ?></textarea>

        <!-- Contact Number and Shop Link -->
        <div class="contact-shop-links">
            <input type="text" class="contact-number" placeholder="Contact Number" value= "<?php echo $user['contact_num']; ?>">
            <input type="url" class="shopee-link" placeholder="Shop Link (Website)" value= "<?php echo $user['shopee_link']; ?>">
        </div>

        <!-- Shop Link -->
        <div class="contact-shop-links">
            <input type="url" class="lazada-link" placeholder="Shop Link (Website)" value= "<?php echo $user['lazada_link']; ?>">
        </div>

        <!-- Save and Cancel Buttons -->
        <div class="buttons-container">
            <a href="UserProfile.php"><button class="cancel-btn" onclick="cancelChanges()">Cancel</button></a>
            <button class="save-btn" onclick="saveChanges()">Save</button>
        </div>
    </div>
</div>

<!-------------------------------------------------------JS Functions------------------------------------------------------------------------>
<script>
    
   function saveChanges() {
    const formData = new FormData(); //retrieving data from textboxes
    formData.append('profile_pic', document.getElementById('image-input').files[0]);
    formData.append('shop_desc', document.querySelector('.shop-description').value);
    formData.append('contact_num', document.querySelector('.contact-number').value);
    formData.append('shopee_link', document.querySelector('.shopee-link').value);
    formData.append('lazada_link', document.querySelector('.lazada-link').value);

    

    // You can use AJAX to send the form data to the server for saving
    fetch('update_user.php', { //calling out update function php
        method: 'POST',
        body: formData
    }).then(response => response.json())
      .then(data => {
        if (data.success) {
            alert("Changes saved!");
            window.location.href = 'UserProfile.php'; //redirect back
        } else {
            alert("Error saving changes.");
        }
    }).catch(error => {
        console.error("Error:", error);
        alert("An error occurred while saving the changes.");
    });
}

// Function to toggle the category dropdown visibility
function toggleDropdown() {
    const dropdown = document.querySelector('.dropdown');
    dropdown.classList.toggle('show');
}

// Close category dropdown when clicking outside of it
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.dropdown');
    if (!dropdown.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Profile DropDown Toggle
function toggleUserProfileMenu() {
    const menu = document.getElementById('user-profile-dropdown');
    menu.classList.toggle('show');
}

// Close profile dropdown when clicking outside of it
document.addEventListener('click', function(event) {
    const menu = document.getElementById('user-profile-dropdown');
    if (!menu.contains(event.target) && !event.target.closest('.user-profile')) {
        menu.classList.remove('show');
    }
});

// Function to change profile image
function changeProfileImage() {
    document.getElementById('image-input').click();
}

// Function to preview image after file selection
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const profileImage = document.getElementById('profile-image');
        profileImage.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}


// Function to cancel changes (Reset form or revert any unsaved changes)
function cancelChanges() {
     // Reset all textboxes
     document.querySelector('.shop-description').value = '';
    document.querySelector('.contact-number').value = '';
    document.querySelector('.shopee-link').value = '';
    document.querySelector('.lazada-link').value = '';
    
    // Reset the image preview
    document.querySelector('#image-input').src = '';
    document.querySelector('#image-input').value = ''; // Clear the file input

    window.location.href = 'UserProfile.php'; //redirect back
}
</script>
</body>
</html>
