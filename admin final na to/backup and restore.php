<?php
require 'dbcon.php';
// Fetch archived shops and accounts
$accounts_result = $conn->query("SELECT * FROM `archive_users`");

// Handle account retrieval
if (isset($_POST['retrieve'])) {
    $seller_id = filter_input(INPUT_POST, 'seller_id', FILTER_VALIDATE_INT);
    if ($seller_id) {
        // Start a transaction
        $conn->begin_transaction();

        // Retrieve the archived account details
        $archive_account_sql = "SELECT * FROM `archive_users` WHERE `seller_id` = ?";
        $archive_account_stmt = $conn->prepare($archive_account_sql);
        
        if (!$archive_account_stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $archive_account_stmt->bind_param("i", $seller_id);
        $archive_account_stmt->execute();
        $archive_account_result = $archive_account_stmt->get_result();

        if ($archive_account_result && $archive_account_row = $archive_account_result->fetch_assoc()) {
            // Insert the data into the `users` table
            $insert_account_sql = "INSERT INTO `users` (`seller_id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `created_at`) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insert_account_stmt = $conn->prepare($insert_account_sql);

            if (!$insert_account_stmt) {
                die("Insert prepare failed: " . $conn->error);
            }

            $insert_account_stmt->bind_param(
                "issssss",
                $archive_account_row['seller_id'],
                $archive_account_row['first_name'],
                $archive_account_row['middle_name'],
                $archive_account_row['last_name'],
                $archive_account_row['email'],
                $archive_account_row['password'],
                $archive_account_row['created_at']
            );

            if ($insert_account_stmt->execute()) {
                // Delete the account from the archive
                $delete_archive_account_sql = "DELETE FROM `archive_users` WHERE `seller_id` = ?";
                $delete_archive_account_stmt = $conn->prepare($delete_archive_account_sql);

                if (!$delete_archive_account_stmt) {
                    die("Delete prepare failed: " . $conn->error);
                }

                $delete_archive_account_stmt->bind_param("i", $seller_id);

                if ($delete_archive_account_stmt->execute()) {
                    // Commit the transaction
                    $conn->commit();
                    $_SESSION['message_type'] = 'success';
                    $_SESSION['message'] = 'Account restored successfully.';
                } else {
                    $conn->rollback();
                    die("Delete failed: " . $conn->error);
                }
            } else {
                $conn->rollback();
                die("Insert failed: " . $conn->error);
            }
        } else {
            $_SESSION['message_type'] = 'error';
            $_SESSION['message'] = 'Account not found in the archive.';
        }
    } else {
        die("Invalid or missing seller ID.");
    }
    header('Location: main.php?page=backup and restore');
    exit();
}

// if (isset($_POST['retrieve_shop'])) {
//     $shop_id = filter_input(INPUT_POST, 'shop_id', FILTER_VALIDATE_INT);

//     if ($shop_id) {
//         // Start a transaction
//         $conn->begin_transaction();

//         // Retrieve the archived shop details
//         $archive_shop_sql = "SELECT * FROM `archive_shops` WHERE `shop_id` = ?";
//         $archive_shop_stmt = $conn->prepare($archive_shop_sql);
//         $archive_shop_stmt->bind_param("i", $shop_id);
//         $archive_shop_stmt->execute();
//         $archive_shop_result = $archive_shop_stmt->get_result();

//         if ($archive_shop_result && $archive_shop_row = $archive_shop_result->fetch_assoc()) {
//             // Insert the data into the `shops` table (remove the account insertion)
//             $insert_shop_sql = "INSERT INTO `shops` (`shop_id`, `seller_id`, `first_name`, `middle_name`, `last_name`, 
//                                                   `shop_name`, `stall_number`, `business_permit_number`, `permit_image`, 
//                                                   `municipality`, `baranggay`, `contact_number`) 
//                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//             $insert_shop_stmt = $conn->prepare($insert_shop_sql);

//             $insert_shop_stmt->bind_param(
//                 "iissssssssss",
//                 $archive_shop_row['shop_id'],
//                 $archive_shop_row['seller_id'],
//                 $archive_shop_row['first_name'],
//                 $archive_shop_row['middle_name'],
//                 $archive_shop_row['last_name'],
//                 $archive_shop_row['shop_name'],
//                 $archive_shop_row['stall_number'],
//                 $archive_shop_row['business_permit_number'],
//                 $archive_shop_row['permit_image'],
//                 $archive_shop_row['municipality'],
//                 $archive_shop_row['baranggay'],
//                 $archive_shop_row['contact_number']
//             );
//             $insert_shop_stmt->execute();

//             if ($insert_shop_stmt->affected_rows > 0) {
//                 // Delete the shop from the archive
//                 $delete_archive_shop_sql = "DELETE FROM `archive_shops` WHERE `shop_id` = ?";
//                 $delete_archive_shop_stmt = $conn->prepare($delete_archive_shop_sql);
//                 $delete_archive_shop_stmt->bind_param("i", $shop_id);
//                 $delete_archive_shop_stmt->execute();

//                 // Commit the transaction
//                 $conn->commit();
//                 $_SESSION['message_type'] = 'success';
//                 $_SESSION['message'] = 'Shop restored successfully without account.';
//             } else {
//                 $conn->rollback();
//                 $_SESSION['message_type'] = 'error';
//                 $_SESSION['message'] = 'Failed to restore the shop.';
//             }
//         } else {
//             $_SESSION['message_type'] = 'error';
//             $_SESSION['message'] = 'Shop not found in the archive.';
//         }
//     }
//     header('Location: main.php?page=backup and restore');
//     exit();
// }
?>
<style>
    .body-table {
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
       
    }
    .container {
    width: 90%;
    margin: 0 auto;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    max-width: 1000px;
    border: 1px solid #ddd;
}


        .body-table h1{
            text-align: left;
        }
    /* Card style */
    .card {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        margin: 10px 0;
        margin-left: 520px;
    }

    .card h1 {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    /* Archive section styles */
    .archive_accounts,
.archive_users {
    width: 80%; /* Set the width to be more flexible */
    max-width: 1000px; /* Keep the max-width */
    margin: 0 auto; /* This will center the table horizontally */
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    text-align: center; /* Center-align the text inside the table */
}

table th,
table td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
    font-size: 14px;
}

table th {
    background-color: #712798;
    color: white;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #f1f1f1;
}
</style>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg">
                <h1>Backup Database</h1>
            </div>
            <div class="card-body">
                <a href="backup_code.php" class="btn btn-success">Backup Database</a>
            </div>
        </div>
    </div>
</div>

<div class="archive_accounts">
    <h1>Users Archive</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Seller ID</th>
                <th>Email</th>
                <th>Password</th>
                <th>Created At</th>
                <th>Archived At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch accounts from the archive
            if ($accounts_result->num_rows > 0) {
                while ($row = $accounts_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['seller_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['archived_at']) . "</td>";
                    echo "<td>
                            <form method='POST'>
                                <input type='hidden' name='seller_id' value='" . htmlspecialchars($row['seller_id']) . "'>
                                <button type='submit' name='retrieve' class='btn btn-primary'>Retrieve</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No archived accounts found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>



