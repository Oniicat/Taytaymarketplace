<?php
include "dbcon.php";

// Insert new user
if (isset($_POST["submit"])) {
    // Clean and escape input data
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $mname = mysqli_real_escape_string($conn, $_POST['mname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $municipality = mysqli_real_escape_string($conn, $_POST['municipality']);
    $baranggay = mysqli_real_escape_string($conn, $_POST['baranggay']);
    $shop = mysqli_real_escape_string($conn, $_POST['shop']);
    $stall_number = mysqli_real_escape_string($conn, $_POST['stall_number']);
    $business_permit_number = mysqli_real_escape_string($conn, $_POST['business_permit_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $uname = mysqli_real_escape_string($conn, $_POST['user_name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle file upload
    if (isset($_FILES['permit_image']) && $_FILES['permit_image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['permit_image']['tmp_name'];
        $file_type = mime_content_type($file_tmp);
        $file_ext = pathinfo($_FILES['permit_image']['name'], PATHINFO_EXTENSION);
        
        // Allowed image types
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed_types) && in_array(strtolower($file_ext), $allowed_exts)) {
            // Read the image file as binary data
            $permit_image = file_get_contents($file_tmp);

            // Insert into `accounts` table for managing credentials
            $stmt1 = $conn->prepare("INSERT INTO `users` (`email`, `user_name`,`password`, `created_at`,`lastlogin_time`) VALUES (?, ?,?, NOW(),NOW())");
            $stmt1->bind_param("sss", $email,$uname, $hashed_password);

            if ($stmt1->execute()) {
                // Get the auto-generated ID (seller_id) from the `accounts` table
                $seller_id = $stmt1->insert_id;

                // Insert into `shops` table with the same `seller_id`
                $stmt2 = $conn->prepare("INSERT INTO `shops` (`seller_id`, `first_name`, `middle_name`, `last_name`, `contact_number`, `municipality`, `baranggay`, `shop_name`, `stall_number`, `business_permit_number`, `permit_image`, `created_at`) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt2->bind_param("isssssssssb", $seller_id, $fname, $mname, $lname, $contact, $municipality, $baranggay, $shop, $stall_number, $business_permit_number, $permit_image);

                if ($stmt2->execute()) {
                    $_SESSION['message_type'] = 'success';
                    $_SESSION['message'] = 'New shop and account created successfully.';
                } else {
                    // Log and set error if the `shops` table insert fails
                    error_log("Error inserting into `shops`: " . $stmt2->error);
                    $_SESSION['message_type'] = 'error';
                    $_SESSION['message'] = 'Error adding shop details.';
                }
            } else {
                // Log and set error if the `accounts` table insert fails
                error_log("Error inserting into `accounts`: " . $stmt1->error);
                $_SESSION['message_type'] = 'error';
                $_SESSION['message'] = 'Error creating account for the shop.';
            }
        } else {
            // File is not an image
            $_SESSION['message_type'] = 'error';
            $_SESSION['message'] = 'Uploaded file is not a valid image.';
        }
    } else {
        // Handle file upload errors
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = 'No file uploaded or invalid upload.';
    }
}



// Update user
if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($conn, $_POST['shop_id']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = !empty($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : null;

    // Begin transaction
    $conn->begin_transaction();

    // Update `users` table for name and contact details
    $stmt = $conn->prepare("UPDATE `shops` SET `first_name`=?, `middle_name`=?, `last_name`=?, `contact_number`=? WHERE `shop_id`=?");
    $stmt->bind_param("ssssi", $first_name, $middle_name, $last_name, $contact, $id);
    $update_users = $stmt->execute();

    // Update `accounts` table for email and password
    if ($password) {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE `users` SET `email`=?, `password`=? WHERE `seller_id`=?");
        $stmt->bind_param("ssi", $email, $hashed_password, $id);
        $update_accounts = $stmt->execute();
    } else {
        // Only update email
        $stmt = $conn->prepare("UPDATE `users` SET `email`=? WHERE `seller_id`=?");
        $stmt->bind_param("si", $email, $id);
        $update_accounts = $stmt->execute();
    }

    // Commit or rollback based on query success
    if ($update_users && $update_accounts) {
        $conn->commit();
        $_SESSION['message_type'] = 'success';
        $_SESSION['message'] = 'User and account updated successfully.';
    } else {
        $conn->rollback();
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = 'Error updating user and account.';
    }
}

// Delete user
if (isset($_GET['id'])) {
    $shop_id = $_GET['id'];

    // Begin a transaction
    mysqli_begin_transaction($conn);

    // Fetch the shop details from the shops table
    $sql = "SELECT * FROM `shops` WHERE `shop_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        // Fetch the corresponding account details using the shop id (seller_id)
        $account_sql = "SELECT `email`, `password`, `created_at` FROM `users` WHERE `seller_id` = ?";
        $account_stmt = $conn->prepare($account_sql);
        $account_stmt->bind_param("i", $row['seller_id']);  // Using seller_id from the shops table
        $account_stmt->execute();
        $account_result = $account_stmt->get_result();

        if ($account_result && $account_row = $account_result->fetch_assoc()) {
            // Insert data into user_archive table (including the shop_id)
            $insert_user_archive_sql = "INSERT INTO `archive_shops` (`shop_id`,`seller_id`, `first_name`,`middle_name`,`last_name`, `shop_name`, `stall_number`, `business_permit_number`, `permit_image`, `municipality`,`baranggay`, `contact_number`) 
                                        VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
            $insert_user_archive_stmt = $conn->prepare($insert_user_archive_sql);
            $insert_user_archive_stmt->bind_param(
                "iissssssssss",
                $row['shop_id'],
                $row['seller_id'],  // Use the shop_id from the `shops` table
                $row['first_name'],
                $row['middle_name'],
                $row['last_name'], // Assuming seller_name is a combination of first, middle, and last name
                $row['shop_name'],
                $row['stall_number'],
                $row['business_permit_number'],
                $row['permit_image'], 
                $row['municipality'],
                $row['baranggay'],
                $row['contact_number'], // Assuming 'contact_number' is the contact info field
              
            );
            $insert_user_archive_stmt->execute();

            // Insert data into archive_accounts table
            $insert_archive_accounts_sql = "INSERT INTO `archive_accounts` (`seller_id`, `email`, `password`, `created_at`, `archived_at`) 
                                            VALUES (?, ?, ?, ?, ?)";
            $insert_archive_accounts_stmt = $conn->prepare($insert_archive_accounts_sql);
            $archived_at = date("Y-m-d H:i:s"); // Current timestamp for archived_at field
            $insert_archive_accounts_stmt->bind_param(
                "issss",
                $row['seller_id'], // Use the seller_id from the `shops` table
                $account_row['email'],
                $account_row['password'],
                $account_row['created_at'],
                $archived_at
            );
            $insert_archive_accounts_stmt->execute();

            // Check if both inserts were successful
            if ($insert_user_archive_stmt->affected_rows > 0 && $insert_archive_accounts_stmt->affected_rows > 0) {
                // Delete the shop from the `shops` table
                $delete_shop_sql = "DELETE FROM `shops` WHERE `shop_id` = ?";
                $delete_shop_stmt = $conn->prepare($delete_shop_sql);
                $delete_shop_stmt->bind_param("i", $shop_id);
                $delete_shop_stmt->execute();

                // Delete the account from the `accounts` table
                $delete_account_sql = "DELETE FROM `users` WHERE `seller_id` = ?";
                $delete_account_stmt = $conn->prepare($delete_account_sql);
                $delete_account_stmt->bind_param("i", $row['seller_id']);
                $delete_account_stmt->execute();

                // Delete the product details from the `products` tab
                if (
                    $delete_shop_stmt->affected_rows > 0 &&
                    $delete_account_stmt->affected_rows > 0 
                ) {
                    // Commit the transaction
                    mysqli_commit($conn);

                    // Success message
                    $_SESSION['message_type'] = 'success';
                    $_SESSION['message'] = 'Shop, account, and products deleted successfully.';
                } else {
                    // Rollback if delete failed
                    mysqli_rollback($conn);
                    $_SESSION['message_type'] = 'error';
                    $_SESSION['message'] = 'Failed to delete shop, account, or products.';
                }
            } else {
                // Rollback if any insert failed
                mysqli_rollback($conn);
                $_SESSION['message_type'] = 'error';
                $_SESSION['message'] = 'Failed to archive the shop or account.';
            }
        } else {
            $_SESSION['message_type'] = 'error';
            $_SESSION['message'] = 'Account details not found for the shop.';
            mysqli_rollback($conn);
        }
    } else {
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = 'Shop not found.';
        mysqli_rollback($conn);
    }

    header('Location: main.php?page=accounts');
    exit();
}

?>



    <link rel="stylesheet" href="accounts.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modal background */
        .modal {
            display: none; /* Initially hidden */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        /* Modal content box */
        .modal-content {
            overflow-y: auto;
            max-height: 80%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px; /* Max width for the modal */
            width: 90%; /* Full width but with a max limit */
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            align-items: stretch; /* Stretch children to fill container */
            box-sizing: border-box; /* Include padding in width calculation */
            animation: fadeIn 0.3s ease-in-out; /* Smooth fade-in effect */
        }

        /* Close button */
        .close-btn {
            font-size: 24px;
            color: #333;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
            transition: color 0.3s;
        }

        .close-btn:hover {
            color: #ff5733; /* Change color on hover */
        }

        /* Cancel button */
        .cancel-btn {
            background-color: #f1f1f1;
            color: #333;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .cancel-btn:hover {
            background-color: #ddd; /* Lighten on hover */
        }

        /* Submit button */
        .btn-success {
            background-color: #712798;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #3D1055;
        }

        /* Input fields inside modal */
        .modal-content input,
        .modal-content select { /* Add styles for dropdown */
            width: 100%; /* Full width */
            padding: 10px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Include padding in width calculation */
            margin-bottom: 10px; /* Add spacing between input fields */
        }

        /* Styling for the dropdown (select) */
        .modal-content select {
            background-color: #fff;
            color: #333;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .modal-content select:focus {
            border-color: #712798; /* Change border color on focus */
            outline: none; /* Remove default focus outline */
        }

        /* Modal fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .search-container {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-container input {
            width: 250px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-container select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>

<div class="container">
    <h1>Accounts</h1>
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search" onkeyup="filterTable()">
        <button id="openAddModalBtn" class="btnn" onclick="openAddModal()"><i class="fas fa-plus"></i> Add New User</button>
    </div>
    <table class="table table-hover text-center" id="categoryTable">
        <thead>
            <tr>
                <th>Shop ID</th>
                <th>Seller ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Municipality</th>
                <th>Barangay</th>
                <th>Shop Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch data from shops table and join with accounts table to get email
            $sql = "SELECT s.shop_id, s.seller_id, s.first_name, s.middle_name, s.last_name, s.contact_number, 
                           s.municipality, s.baranggay, s.shop_name, a.email 
                    FROM shops s
                    LEFT JOIN users a ON s.seller_id = a.seller_id";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>
                            <td>' . htmlspecialchars($row["shop_id"]) . '</td>
                            <td>' . htmlspecialchars($row["seller_id"]) . '</td>
                            <td>' . htmlspecialchars($row["first_name"]) . '</td>
                            <td>' . htmlspecialchars($row["middle_name"]) . '</td>
                            <td>' . htmlspecialchars($row["last_name"]) . '</td>
                            <td>' . htmlspecialchars($row["email"]) . '</td>
                            <td>' . htmlspecialchars($row["contact_number"]) . '</td>
                            <td>' . htmlspecialchars($row["municipality"]) . '</td>
                            <td>' . htmlspecialchars($row["baranggay"]) . '</td>
                            <td>' . htmlspecialchars($row["shop_name"]) . '</td>
                            <td>
                                <button class="btn" onclick="openEditModal(' . $row["shop_id"] . ', \'' . htmlspecialchars($row["seller_id"]) . '\', \'' . htmlspecialchars($row["first_name"]) . '\', \'' . htmlspecialchars($row["middle_name"]) . '\', \'' . htmlspecialchars($row["last_name"]) . '\', \'' . htmlspecialchars($row["email"]) . '\', \'' . htmlspecialchars($row["contact_number"]) . '\', \'' . htmlspecialchars($row["municipality"]) . '\', \'' . htmlspecialchars($row["baranggay"]) . '\', \'' . htmlspecialchars($row["shop_name"]) . '\')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="accounts.php?id=' . $row["shop_id"] . '" class="btn" onclick="return confirm(\'Are you sure?\')">
                                    <i class="fas fa-archive"></i>
                                </a>
                            </td>
                        </tr>';
                }
            } else {
                echo '<tr><td colspan="11">No records found</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
<!-- Add Modal -->
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeAddModal()">&times;</span>
        <h3>Add New User</h3>
        <br>
        <form method="post" enctype="multipart/form-data">
            <!-- Shop Owner Information -->
            <h3>Shop Owner Information</h3>
            <input type="text" name="fname" placeholder="First Name" required>
            <input type="text" name="mname" placeholder="Middle Name" required>
            <input type="text" name="lname" placeholder="Last Name" required>
            <input type="text" name="user_name" placeholder="Username" required>
            <input type="text" name="contact_number" placeholder="Contact (e.g., 09123456789)"  title="Enter a valid contact number" required>
            
            <!-- Shop Information -->
            <h3>Shop Information</h3>
            <input type="text" name="shop" placeholder="Shop Name" required>
            <input type="text" name="stall_number" placeholder="Stall Number" required>
            <input type="text" name="business_permit_number" placeholder="Business Permit Number" required>
            <input type="text" name="baranggay" placeholder="Barangay" required>
            <input type="text" name="municipality" placeholder="Municipality" required>
            <input type="file" name="permit_image" id="permit_image" accept="image/*" required>
            <small>Accepted file types: JPG, PNG (Max size: 1MB)</small>

            <!-- Shop Links -->
            <h3>Shop Links</h3>
            <input type="url" name="shopee_link" placeholder="Shopee Link (e.g., https://shopee.ph/yourshop)" required>
            <input type="url" name="lazada_link" placeholder="Lazada Link (e.g., https://lazada.com/yourshop)" required>
            
            <!-- Login Credentials -->
            <h3>Login Credentials</h3>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password (Min. 8 characters)" minlength="8" required>
            
            
            <!-- Submit and Cancel Buttons -->
            <button type="submit" name="submit" class="btn btn-success">Save</button>
            <button type="button" class="cancel-btn" onclick="closeAddModal()">Cancel</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<?php
// Check if an ID is passed through the URL to edit the user
if (isset($_GET['shop_id'])) {
    $user_id = $_GET['shop_id'];

    // Fetch user details along with email and password
    $sql = "SELECT s.shop_id, s.first_name, s.middle_name, s.last_name, s.contact_number, a.email, a.password 
            FROM shops s
            JOIN accounts a ON s.shop_id = a.seller_id
            WHERE s.shop_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Bind the user_id from the URL
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        // Populate modal fields with fetched data
        $first_name = htmlspecialchars($row['first_name']);
        $middle_name = htmlspecialchars($row['middle_name']);
        $last_name = htmlspecialchars($row['last_name']);
        $contact = htmlspecialchars($row['contact_number']);
        $email = htmlspecialchars($row['email']);
        $password = htmlspecialchars($row['password']); // Not used directly, for updating password if needed
    }
}
?>
<div id="editCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeEditModal()">&times;</span>
        <h3>Edit User</h3>
        <form method="post">
            <input type="hidden" name="shop_id" id="editId">
            <input type="text" name="first_name" id="editFirstName" placeholder="First Name" required>
            <input type="text" name="middle_name" id="editMiddleName" placeholder="Middle Name">
            <input type="text" name="last_name" id="editLastName" placeholder="Last Name" required>
            <input type="text" name="contact_number" id="editContact" placeholder="Contact" required>
            <input type="email" name="email" id="editEmail" placeholder="Email" required>
            <input type="password" name="password" id="editPassword" placeholder="Password (leave blank to keep unchanged)">
            <button type="submit" name="update" class="btn btn-success">Update</button>
            <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addCategoryModal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('addCategoryModal').style.display = 'none';
    }

    function openEditModal(shop_id, seller_id, first_name, middle_name, last_name, email, contact, municipality, baranggay, shop_name) {
    // Set the modal input fields with the fetched data
    document.getElementById('editId').value = shop_id;  // or seller_id if that's the unique identifier
    document.getElementById('editFirstName').value = first_name;
    document.getElementById('editMiddleName').value = middle_name;
    document.getElementById('editLastName').value = last_name;
    document.getElementById('editContact').value = contact;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPassword').value = '';  // Default to empty for password field

    // Show the modal
    document.getElementById('editCategoryModal').style.display = 'flex';
}
function closeEditModal() {
    // Close the modal
    document.getElementById('editCategoryModal').style.display = 'none';
}

function filterTable() {
    var input = document.getElementById('searchInput');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('categoryTable');
    var tr = table.getElementsByTagName('tr');

    // Loop through all table rows and hide those that do not match the search query
    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName('td');
        var showRow = false;
        
        // Check if any cell in the row matches the search input
        for (var j = 0; j < td.length; j++) {
            if (td[j]) {
                var cellValue = td[j].textContent || td[j].innerText;
                if (cellValue.toLowerCase().indexOf(filter) > -1) {
                    showRow = true;
                    break;
                }
            }
        }

        // Show/hide rows based on matching search criteria
        if (showRow) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}
</script>
