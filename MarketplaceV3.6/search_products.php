<?php
include '../registration-process/conn.php';

header('Content-Type: application/json');

// Get the search term from the request (if provided)
$searchTerm = $_GET['query'] ?? ''; 
$response = [];

// Prepare the SQL query
if (!empty($searchTerm)) {
    // If there is a search term, filter products by name using LIKE
    $searchTerm = '%' . $conn->real_escape_string($searchTerm) . '%'; // Prepare search term for LIKE query

    // Query to search for products with their images
    $sql = "SELECT p.*, pi.images 
            FROM tb_products p
            LEFT JOIN tb_product_images pi ON p.product_id = pi.product_id
            WHERE p.product_name LIKE ?
            GROUP BY p.product_id";
} else {
    // If no search term is provided, fetch all products and their images
    $sql = "SELECT p.*, pi.images 
            FROM tb_products p
            LEFT JOIN tb_product_images pi ON p.product_id = pi.product_id 
            GROUP BY p.product_id";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    // Handle errors in preparing the statement
    $response['error'] = "Failed to prepare statement: " . $conn->error;
    echo json_encode($response);
    exit;
}

// Bind the search term (if provided) to the prepared statement
if (!empty($searchTerm)) {
    $stmt->bind_param("s", $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $response[] = $row; // Add product to response
}

$stmt->close();
$conn->close();

// Return the search results (or all products) as JSON
echo json_encode($response);
?>