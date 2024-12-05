<?php
include '../registration-process/conn.php';

try {
    // Determine the sorting order based on query parameters
    $order = $_GET['order'] === 'DESC' ? 'DESC' : 'ASC';

    // Query to fetch products sorted by price
    $stmt = $conn->prepare("SELECT * FROM tb_products ORDER BY product_price $order");
    $stmt->execute();
    $result = $stmt->get_result();

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
