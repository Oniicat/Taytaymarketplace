<?php
include "dbcon.php";

// Insert new user
if (isset($_POST["submit"])) {
    // Clean and escape input data
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $mname = mysqli_real_escape_string($conn, $_POST['mname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into `users` table for managing credentials
    $stmt = $conn->prepare("INSERT INTO `users` (`email`, `password`, `first_name`, `middle_name`, `last_name`, `created_at`, `lastlogin_time`) 
                            VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("sssss", $email, $hashed_password, $fname, $mname, $lname);

    if ($stmt->execute()) {
        $_SESSION['message_type'] = 'success';
        $_SESSION['message'] = 'New user account created successfully.';
    } else {
        // Log and set error if the `users` table insert fails
        error_log("Error inserting into `users`: " . $stmt->error);
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = 'Error creating user account.';
    }
}


// Update user
if (isset($_POST['update'])) {
    $seller_id = mysqli_real_escape_string($conn, $_POST['seller_id']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = !empty($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : null;

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Update `users` table for name and email
        if ($password) {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE `users` SET `first_name`=?, `middle_name`=?, `last_name`=?, `email`=?, `password`=? WHERE `seller_id`=?");
            $stmt->bind_param("sssssi", $first_name, $middle_name, $last_name, $email, $hashed_password, $seller_id);
        } else {
            // Only update name and email, without changing the password
            $stmt = $conn->prepare("UPDATE `users` SET `first_name`=?, `middle_name`=?, `last_name`=?, `email`=? WHERE `seller_id`=?");
            $stmt->bind_param("ssssi", $first_name, $middle_name, $last_name, $email, $seller_id);
        }

        $update_users = $stmt->execute();

        // Commit or rollback based on query success
        if ($update_users) {
            $conn->commit();
            $_SESSION['message_type'] = 'success';
            $_SESSION['message'] = 'User updated successfully.';
        } else {
            throw new Exception("Error updating user.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = 'Error updating user: ' . $e->getMessage();
    }
}

// Delete user
if (isset($_GET['id'])) {
    $seller_id = $_GET['id'];

    // Begin a transaction
    mysqli_begin_transaction($conn);

    // Fetch the user account details
    $sql = "SELECT `first_name`, `middle_name`, `last_name`, `email`, `password`, `created_at` FROM `users` WHERE `seller_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        // Insert data into archive_users table
        $insert_archive_accounts_sql = "INSERT INTO `archive_users` 
            (`first_name`, `middle_name`, `last_name`, `seller_id`, `email`, `password`, `created_at`, `archived_at`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $insert_archive_accounts_stmt = $conn->prepare($insert_archive_accounts_sql);
        $insert_archive_accounts_stmt->bind_param(
            "sssisss", // Corrected number of placeholders
            $row['first_name'],
            $row['middle_name'],
            $row['last_name'],
            $seller_id,
            $row['email'],
            $row['password'],
            $row['created_at']
        );
        $insert_archive_accounts_stmt->execute();

        // Check if the insert was successful
        if ($insert_archive_accounts_stmt->affected_rows > 0) {
            // Delete the user account from the `users` table
            $delete_account_sql = "DELETE FROM `users` WHERE `seller_id` = ?";
            $delete_account_stmt = $conn->prepare($delete_account_sql);
            $delete_account_stmt->bind_param("i", $seller_id);
            $delete_account_stmt->execute();

            // Check if the delete was successful
            if ($delete_account_stmt->affected_rows > 0) {
                // Commit the transaction
                mysqli_commit($conn);

                // Success message
                $_SESSION['message_type'] = 'success';
                $_SESSION['message'] = 'User account archived and deleted successfully.';
            } else {
                // Rollback if delete failed
                mysqli_rollback($conn);
                $_SESSION['message_type'] = 'error';
                $_SESSION['message'] = 'Failed to delete the user account.';
            }
        } else {
            // Rollback if insert failed
            mysqli_rollback($conn);
            $_SESSION['message_type'] = 'error';
            $_SESSION['message'] = 'Failed to archive the user account.';
        }
    } else {
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = 'User account not found.';
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
    <table class="table table-hover text-center" id="usersTable">
        <thead>
            <tr>
                <th>Seller ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch data from users table
            $sql = "SELECT seller_id, first_name, middle_name, last_name, email, created_at FROM users";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>
                            <td>' . htmlspecialchars($row["seller_id"]) . '</td>
                            <td>' . htmlspecialchars($row["first_name"]) . '</td>
                            <td>' . htmlspecialchars($row["middle_name"]) . '</td>
                            <td>' . htmlspecialchars($row["last_name"]) . '</td>
                            <td>' . htmlspecialchars($row["email"]) . '</td>
                            <td>' . htmlspecialchars($row["created_at"]) . '</td>
                            <td>
                                <button class="btn" onclick="openEditModal(' . $row["seller_id"] . ', \'' . htmlspecialchars($row["first_name"]) . '\', \'' . htmlspecialchars($row["middle_name"]) . '\', \'' . htmlspecialchars($row["last_name"]) . '\', \'' . htmlspecialchars($row["email"]) . '\')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="accounts.php?id=' . $row["seller_id"] . '" class="btn" onclick="return confirm(\'Are you sure?\')">
                                    <i class="fas fa-archive"></i>
                                </a>
                            </td>
                        </tr>';
                }
            } else {
                echo '<tr><td colspan="7">No records found</td></tr>';
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
            <!-- User Personal Information -->
            <h3>User Information</h3>
            <input type="text" name="fname" placeholder="First Name" required>
            <input type="text" name="mname" placeholder="Middle Name" required>
            <input type="text" name="lname" placeholder="Last Name" required>
            
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
if (isset($_GET['seller_id'])) {
    $user_id = $_GET['seller_id'];

    // Fetch user details from the `users` table
    $sql = "SELECT seller_id, first_name, middle_name, last_name, email FROM users WHERE seller_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Bind the seller_id from the URL
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        // Populate modal fields with fetched data
        $seller_id = htmlspecialchars($row['seller_id']);
        $first_name = htmlspecialchars($row['first_name']);
        $middle_name = htmlspecialchars($row['middle_name']);
        $last_name = htmlspecialchars($row['last_name']);
        $email = htmlspecialchars($row['email']);
    }
}
?>
<div id="editCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeEditModal()">&times;</span>
        <h3>Edit User</h3>
        <form method="post">
            <!-- Hidden field for seller_id -->
            <input type="hidden" name="seller_id" id="editId" value="<?php echo isset($seller_id) ? $seller_id : ''; ?>">

            <!-- First Name -->
            <input type="text" name="first_name" id="editFirstName" placeholder="First Name" 
                   value="<?php echo isset($first_name) ? $first_name : ''; ?>" required>

            <!-- Middle Name -->
            <input type="text" name="middle_name" id="editMiddleName" placeholder="Middle Name" 
                   value="<?php echo isset($middle_name) ? $middle_name : ''; ?>">

            <!-- Last Name -->
            <input type="text" name="last_name" id="editLastName" placeholder="Last Name" 
                   value="<?php echo isset($last_name) ? $last_name : ''; ?>" required>

            <!-- Email -->
            <input type="email" name="email" id="editEmail" placeholder="Email" 
                   value="<?php echo isset($email) ? $email : ''; ?>" required>

            <!-- Password (Optional) -->
            <input type="password" name="password" id="editPassword" placeholder="Password (leave blank to keep unchanged)">

            <!-- Buttons -->
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

    function openEditModal(seller_id, first_name, middle_name, last_name, email) {
    // Set the modal input fields with the fetched data
    document.getElementById('editId').value = seller_id; // Use seller_id as the unique identifier
    document.getElementById('editFirstName').value = first_name;
    document.getElementById('editMiddleName').value = middle_name;
    document.getElementById('editLastName').value = last_name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPassword').value = ''; // Default to empty for password field

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
