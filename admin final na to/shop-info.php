<?php
include 'dbcon.php'; // Connect to the database

// Check if the form is submitted
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

    // Sanitize and validate form data
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $middle_name = htmlspecialchars(trim($_POST['middle_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $contact_number = htmlspecialchars(trim($_POST['contact_number']));
    $municipality = htmlspecialchars(trim($_POST['municipality']));
    $baranggay = htmlspecialchars(trim($_POST['baranggay']));
    $shop_name = htmlspecialchars(trim($_POST['shop_name']));
    $stall_number = htmlspecialchars(trim($_POST['stall_number']));
    $business_permit_number = htmlspecialchars(trim($_POST['business_permit_number']));

    // Handle business permit upload
    $fileDestination = '';
    if (isset($_FILES['permit_image']) && $_FILES['permit_image']['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES['permit_image']['name']);
        $fileTmpName = $_FILES['permit_image']['tmp_name'];
        $uploadDir = 'uploads/';

        // Ensure uploads folder exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileDestination = $uploadDir . uniqid('permit_', true) . '_' . $fileName;
        if (!move_uploaded_file($fileTmpName, $fileDestination)) {
            die(json_encode(['success' => false, 'message' => 'Error uploading the file.']));
        }
    } else {
        die(json_encode(['success' => false, 'message' => 'Business permit image is required.']));
    }

    // Insert data into the database
    $query = "
        INSERT INTO registration (
            seller_id, first_name, middle_name, last_name, contact_number,
            municipality, baranggay, shop_name, stall_number,
            business_permit_number, permit_image
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    // Prepare and execute the SQL statement
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param(
            "issssssssss",
            $seller_id, $first_name, $middle_name, $last_name, $contact_number,
            $municipality, $baranggay, $shop_name, $stall_number,
            $business_permit_number, $fileDestination
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Data inserted successfully.']);
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
    <title>Seller Information</title>
    <link rel="stylesheet" href="shop-info.css">
</head>
<body>
    <!-- Navbar -->
    <div class="custom-navbar">
        <div class="navbar-center">
            <img src="Content/New Logo.png" alt="Logo" class="navbar-logo">
        </div>
    </div>

    <!-- Form -->
    <form id="sellerForm" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="seller_id" value="<?php echo isset($_GET['seller_id']) ? $_GET['seller_id'] : ''; ?>">
        <div class="form-container">
            <h2>Shop Owner Information</h2>
            <div class="shopowner-info">
                <div>
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                <div>
                    <label for="middle_name">Middle Name:</label>
                    <input type="text" name="middle_name" required>
                </div>
                <div>
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
                <div>
                    <label for="contact_number">Contact Number:</label>
                    <input type="text" name="contact_number" required>
                </div>
                <div>
                    <label for="municipality">Municipality:</label>
                    <input type="text" name="municipality" required>
                </div>
                <div>
                    <label for="baranggay">Barangay:</label>
                    <input type="text" name="baranggay" required>
                </div>
            </div>

            <h2>Shop Information</h2>
            <div class="shop-info">
                <div>
                    <label for="shop_name">Shop Name:</label>
                    <input type="text" name="shop_name" required>
                </div>
                <div>
                    <label for="stall_number">Stall Number:</label>
                    <input type="text" name="stall_number" required>
                </div>
                <div>
                    <label for="business_permit_number">Business Permit Number:</label>
                    <input type="text" name="business_permit_number" required>
                </div>
                <div>
                    <label for="permit_image">Upload Business Permit:</label>
                    <input type="file" name="permit_image" accept=".png, .jpg, .jpeg, .pdf" required>
                </div>
            </div>
            <div class="form-buttons">
                <button type="button" class="cancel-button" onclick="cancelForm()">Cancel</button>
                <button type="submit" class="submit-button">Submit for Verification</button>
            </div>
        </div>
    </form>

    <div class="notification-popup" id="notificationPopup" style="display: none;">
        <div class="notification-content">
            <h3>Shop Registration Complete!</h3>
            <p>Your information has been successfully submitted. It is now under review by the admin.</p>
        </div>
    </div>

    <script>
    const form = document.getElementById("sellerForm");
    const submitButton = document.querySelector(".submit-button");

    form.addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent the default form submission
        
        // Disable the submit button to prevent multiple clicks
        submitButton.disabled = true;

        const formData = new FormData(form);

        // Log the seller_id and URL being used
        console.log("Submitting with seller_id: " + formData.get('seller_id'));
        console.log("Form URL: ?seller_id=" + formData.get('seller_id'));

        // Perform the form submission via fetch
        fetch("?seller_id=" + formData.get('seller_id'), {  
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
        window.location.href = "home.php"; // Redirect to home page
    }

    function showNotification() {
        document.getElementById("notificationPopup").style.display = "flex";
    }

    function logOutAndRedirect() {
        fetch("logout.php")
            .then(() => window.location.href = "../login_module/signin_page.php")
            .catch(() => window.location.href = "../login_module/signin_page.php");
    }
</script>

</body>
</html>






