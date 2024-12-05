<?php
include '../registration-process/conn.php';

try {
    // Query to fetch all products sorted by date_created in descending order
    $stmt = $conn->prepare("SELECT * FROM tb_products ORDER BY date_created DESC");
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
