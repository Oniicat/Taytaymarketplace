<?php
// Include the database connection file
include '../registration-process/conn.php';

// Initialize an empty array for the response
$response = [];

// Get the category from the GET request, or default to an empty string
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Prepare SQL query based on whether a category is provided
if (!empty($category)) {
    $sql = "SELECT p.*, pi.images 
            FROM tb_products p
            LEFT JOIN tb_product_images pi ON p.product_id = pi.product_id 
            WHERE p.category = ?
            GROUP BY p.product_id";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $category);
    } else {
        // Handle errors in preparing the statement
        $response['error'] = "Failed to prepare statement: " . $conn->error;
        echo json_encode($response);
        exit;
    }
} else {
    // Fetch all products if no category is specified
    $sql = "SELECT p.*, pi.images
            FROM tb_products p
            LEFT JOIN tb_product_images pi ON p.product_id = pi.product_id 
            GROUP BY p.product_id";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Handle errors in preparing the statement
        $response['error'] = "Failed to prepare statement: " . $conn->error;
        echo json_encode($response);
        exit;
    }
}

// Execute the query and handle errors
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);

    // Return the products as JSON
    echo json_encode($products);
} else {
    // Handle execution errors
    $response['error'] = "Failed to execute query: " . $stmt->error;
    echo json_encode($response);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>