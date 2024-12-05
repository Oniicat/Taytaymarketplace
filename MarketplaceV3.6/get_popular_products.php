<?php
include '../registration-process/conn.php';

// If no category is provided, fetch all popular products
$sql = "SELECT * FROM tb_products INNER JOIN tb_product_clicks ON tb_products.product_id = tb_product_clicks.product_id ORDER BY click_count DESC";
$stmt = $conn->prepare($sql);

$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($products);
?>
