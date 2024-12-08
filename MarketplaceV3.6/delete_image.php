<?php
include "conn.php"; // Include the database connection

// Check if the image_path is set in the POST request
if (isset($_POST['image_path'])) {
    $image_path = $_POST['image_path'];

    // Validate image_path (check if it's a valid string or path)
    if (empty($image_path)) {
        echo json_encode(['success' => false, 'error' => 'Image path is empty.']);
        exit;
    }

    // Check if the image exists in the database
    $sql = "SELECT image_id FROM tb_product_images WHERE images = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $image_path);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Delete the image from the database
        $sql_delete = "DELETE FROM tb_product_images WHERE images = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("s", $image_path);
        $stmt_delete->execute();

        if ($stmt_delete->affected_rows > 0) {
            echo json_encode(['success' => true, 'error' => '']); // Return success response
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete image.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Image not found in the database.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Image path not provided.']);
}
?>
