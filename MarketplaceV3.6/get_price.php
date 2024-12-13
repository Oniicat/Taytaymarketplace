<?php
include '../registration-process/conn.php';

try {
    // Determine the sorting order based on query parameters
    $order = $_GET['order'] === 'DESC' ? 'DESC' : 'ASC';

    // Query to fetch products sorted by price and get the first image for each product
    $sql = "SELECT p.*, 
            (SELECT pi.images FROM tb_product_images pi WHERE pi.product_id = p.product_id LIMIT 1) AS images
            FROM tb_products p
            ORDER BY p.product_price $order";

    // Prepare the query
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        // Handle errors in preparing the statement
        $response['error'] = "Failed to prepare statement: " . $conn->error;
        echo json_encode($response);
        exit;
    }

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all products
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Return products as JSON
    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
