<?php
include 'dbcon.php'; // Include database connection

// Initialize the array to store fetched content
$content_keys = ['Terms', 'DataPrivacy'];
$content_texts = [];

// Fetch the existing content for each key
foreach ($content_keys as $content_key) {
    $query = "SELECT content_text FROM textchange WHERE content_key = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $content_key);
    $stmt->execute();
    $stmt->bind_result($content_text);
    $stmt->fetch();
    $content_texts[$content_key] = $content_text; // Store in the array
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $dataPrivacyId = $_POST['dataPrivacyId']; // Content key
    $content = trim($_POST['content']); // New content

    // Update query to modify content in the database
    $update_query = "UPDATE textchange SET content_text = ? WHERE content_key = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ss", $content, $dataPrivacyId);

    // Handle success or error
    if ($update_stmt->execute()) {
        // Success message
        $_SESSION['message_type'] = 'success';
        $_SESSION['message'] = 'Content updated successfully!';


//activity log ni josh mojica(nakikita ka nya, dapat masipag ka)
$activityType = "Changed Terms and Condition, Data Privacy";
$userName = "Admin"; // Default user name

$conn->begin_transaction();
try {
    $insert_sql = "INSERT INTO activity_log (user_name, activity_type, date_time) VALUES (?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ss", $userName, $activityType);

    if (!$insert_stmt->execute()) {
        throw new Exception("Activity log insertion failed: " . $insert_stmt->error);
    }

    $insert_stmt->close();
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
}



    } else {
        // Error message
        $_SESSION['message_type'] = 'error';
        $_SESSION['message'] = 'Error updating content: ' . $conn->error;
    }

    $update_stmt->close();

    // Reload the page to reflect changes
    header('Location: main.php?page=legaladmin');
    exit();
}
$conn->close();
?>


    <link rel="stylesheet" href="website.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">



    <!-- Display success or error message -->
    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Terms and Conditions Section -->
    <div class="terms-container">
        <h2>Terms and Conditions</h2>
        <form method="POST" action="">
            <!-- Pre-populate the textarea with the existing Terms content -->
            <textarea name="content" placeholder="Enter your Terms and Conditions here..."><?php echo htmlspecialchars($content_texts['Terms']); ?></textarea>
            <input type="hidden" name="dataPrivacyId" value="Terms">
            <div class="terms-buttons">
                <button type="submit" name="update" class="save-btn">Update</button>
            </div>
        </form>
    </div>

    <!-- Data Privacy Section -->
    <div class="terms-container">
        <h2>Data Privacy</h2>
        <form method="POST" action="">
            <!-- Pre-populate the textarea with the existing Data Privacy content -->
            <textarea name="content" placeholder="Enter your data privacy content here..."><?php echo htmlspecialchars($content_texts['DataPrivacy']); ?></textarea>
            <input type="hidden" name="dataPrivacyId" value="DataPrivacy">
            <div class="terms-buttons">
                <button type="submit" name="update" class="save-btn">Update</button>
            </div>
        </form>
    </div>

