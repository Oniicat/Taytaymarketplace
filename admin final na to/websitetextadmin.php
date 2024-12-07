<?php
// Include database connection
include 'dbcon.php';

// Initialize an array to store fetched content
$content_keys = ['Address', 'OpeningClosing', 'CUBAO', 'EDSA'];
$content_texts = [];

// Fetch the existing content for each key from the database
foreach ($content_keys as $content_key) {
    $query = "SELECT content_text FROM textchange WHERE content_key = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $content_key);
    $stmt->execute();
    $stmt->bind_result($content_text);
    $stmt->fetch();
    $content_texts[$content_key] = $content_text; // Store the fetched content in the array
    $stmt->close();
}

// Function to add newlines after sentences
function formatTextWithLineBreaks($text) {
    // Replace sentence-ending punctuation with newline
    return preg_replace('/(?<=[.!?])\s+/', "\n", $text);
}

// Handle form submission for content update
$update_message = ''; // Message to show feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $content_key = $_POST['contentt']; // Content key to update
    $new_content = trim($_POST['content']); // New content provided by the user

    // Validate input
    if (!empty($content_key) && !empty($new_content)) {
        // Prepare the update query
        $update_query = "UPDATE textchange SET content_text = ? WHERE content_key = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $new_content, $content_key);

//activity log ni josh mojica(nakikita ka nya, dapat masipag ka)
$activityType = "Update Website Information";
$insert_sql = "INSERT INTO activity_log (user_name, activity_type, date_time) VALUES (?, ?, NOW())";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("ss", $userEmail, $activityType);
$insert_stmt->execute();
$insert_stmt->close();




        // Execute the update query and provide feedback
        if ($update_stmt->execute()) {
            $update_message = "<p style='color: green;'>Content updated successfully!</p>";
            // Refresh fetched content
            $content_texts[$content_key] = $new_content;
        } else {
            $update_message = "<p style='color: red;'>Error updating content: " . htmlspecialchars($conn->error) . "</p>";
        }
        $update_stmt->close();
    } else {
        $update_message = "<p style='color: red;'>Content cannot be empty.</p>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="website.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>Content Management</title>
</head>
<body>
    <!-- Feedback Message -->
    <div>
        <?php echo $update_message; ?>
    </div>

    <!-- Address Section -->
    <div class="terms-container">
        <h2>Address</h2>
        <form method="POST" action="">
            <textarea name="content" placeholder="Enter Address here..." rows="10"><?php echo htmlspecialchars(formatTextWithLineBreaks($content_texts['Address'] ?? '')); ?></textarea>
            <input type="hidden" name="contentt" value="Address">
            <div class="terms-buttons">
                <button type="submit" name="update" class="save-btn">Update</button>
            </div>
        </form>
    </div>

    <!-- Opening and Closing Section -->
    <div class="terms-container">
        <h2>Opening and Closing</h2>
        <form method="POST" action="">
            <textarea name="content" placeholder="Enter Opening and Closing hours here..." rows="10"><?php echo htmlspecialchars(formatTextWithLineBreaks($content_texts['OpeningClosing'] ?? '')); ?></textarea>
            <input type="hidden" name="contentt" value="OpeningClosing">
            <div class="terms-buttons">
                <button type="submit" name="update" class="save-btn">Update</button>
            </div>
        </form>
    </div>

    <!-- Directions Section -->
    <div class="terms-container">
        <h2>Directions</h2>

        <!-- To Get There Section -->
        <form method="POST" action="">
            <h3>Cubao</h3>
            <textarea name="content" placeholder="Enter directions to get there..." rows="5"><?php echo htmlspecialchars(formatTextWithLineBreaks($content_texts['CUBAO'] ?? '')); ?></textarea>
            <input type="hidden" name="contentt" value="CUBAO">
            <div class="terms-buttons">
                <button type="submit" name="update" class="save-btn">Update</button>
            </div>
        </form>

        <!-- On Arrival Section -->
        <form method="POST" action="">
            <h3>Edsa</h3>
            <textarea name="content" placeholder="Enter directions for when you arrive..." rows="5"><?php echo htmlspecialchars(formatTextWithLineBreaks($content_texts['EDSA'] ?? '')); ?></textarea>
            <input type="hidden" name="contentt" value="EDSA">
            <div class="terms-buttons">
                <button type="submit" name="update" class="save-btn">Update</button>
            </div>
        </form>
    </div>
</body>
</html>
