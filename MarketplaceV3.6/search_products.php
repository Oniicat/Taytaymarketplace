<?php
include '../registration-process/conn.php';

header('Content-Type: application/json');

$searchTerm = $_GET['query'] ?? ''; // Get the search term from the request
$response = [];

if (!empty($searchTerm)) {
    $searchTerm = '%' . $conn->real_escape_string($searchTerm) . '%'; // Prepare the search term for a LIKE query

    // Query to search for products
    $sql = "SELECT * FROM tb_products WHERE product_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response[] = $row; // Add product to response
    }
    $stmt->close();
}

echo json_encode($response); // Return the search results as JSON
$conn->close();
?>
