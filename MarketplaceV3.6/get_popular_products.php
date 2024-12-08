<?php
include '../registration-process/conn.php';

// Fetch all products with images and sorted by click count
$sql = "SELECT p.*, pi.images
        FROM tb_products p
        LEFT JOIN tb_product_images pi ON p.product_id = pi.product_id
        INNER JOIN tb_product_clicks pc ON p.product_id = pc.product_id
        GROUP BY p.product_id
        ORDER BY pc.click_count DESC";

$stmt = $conn->prepare($sql);

// Check if the statement is prepared successfully
if (!$stmt) {
    // Handle errors in preparing the statement
    $response['error'] = "Failed to prepare statement: " . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

// Return the products as a JSON response
echo json_encode($products);
?>
