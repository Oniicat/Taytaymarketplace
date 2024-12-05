<?php
// Include the database connection file
include '../registration-process/conn.php';
session_start();

// Initialize an empty array for the response
$response = [];

// Get parameters from the GET request and sanitize them
$category = isset($_GET['category']) ? trim(htmlspecialchars($_GET['category'])) : '';
$seller_id = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : 0; // Ensure seller_id is an integer

// Check if seller_id is valid
if ($seller_id <= 0) {
    $response['error'] = "Invalid seller ID.";
    echo json_encode($response);
    exit;
}

try {
    // Prepare the SQL query dynamically based on the presence of a category
    if (!empty($category)) {
        $sql = "SELECT * FROM tb_products WHERE category = ? AND seller_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("si", $category, $seller_id);
    } else {
        $sql = "SELECT * FROM tb_products WHERE seller_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("i", $seller_id);
    }

    // Execute the query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);

        // Return the products as JSON
        echo json_encode($products);
    } else {
        throw new Exception("Failed to execute query: " . $stmt->error);
    }
} catch (Exception $e) {
    // Handle any errors
    $response['error'] = $e->getMessage();
    echo json_encode($response);
} finally {
    // Ensure resources are properly closed
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
