<?php
// Include the database connection file
include '../registration-process/conn.php';
session_start();
// Initialize an empty array for the response
$response = [];

// Get the category from the GET request, or default to an empty string
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Prepare SQL query based on whether a category is provided
if (!empty($category)) {
    $sql = "SELECT * FROM tb_products WHERE category = ? AND seller_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("si", $category, $_SESSION['seller_id']);
    } else {
        // Handle errors in preparing the statement
        $response['error'] = "Failed to prepare statement: " . $conn->error;
        echo json_encode($response);
        exit;
    }
} else {
    // Fetch all products if no category is specified
    $sql = "SELECT * FROM tb_products WHERE seller_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['seller_id']);
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
