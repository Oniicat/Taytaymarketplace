<?php
include '../registration-process/conn.php';

try {
    // Query to fetch all products along with their images, sorted by date_created in descending order
    $sql = "SELECT p.*, 
                   (SELECT images FROM tb_product_images pi WHERE pi.product_id = p.product_id LIMIT 1) AS images
            FROM tb_products p
            ORDER BY p.date_created DESC";
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
