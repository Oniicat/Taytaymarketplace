<?php
include '../registration-process/conn.php';

header('Content-Type: application/json');

$response = ["success" => false]; // Default response

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    // Validate product ID
    $product_id = $_POST['product_id'] ?? '';
    if (!$product_id) {
        throw new Exception('Product ID is required.');
    }

    // Begin transaction for safety
    $conn->begin_transaction();

    // Delete links from tb_product_links
    $linkStmt = $conn->prepare("DELETE FROM tb_product_links WHERE product_id = ?");
    if (!$linkStmt) {
        throw new Exception('Failed to prepare statement for deleting links: ' . $conn->error);
    }

    $linkStmt->bind_param("i", $product_id);
    $linkStmt->execute();

    // Delete product from tb_products
    $productStmt = $conn->prepare("DELETE FROM tb_products WHERE product_id = ?");
    if (!$productStmt) {
        throw new Exception('Failed to prepare statement for deleting product: ' . $conn->error);
    }

    $productStmt->bind_param("i", $product_id);
    $productStmt->execute();

    if ($productStmt->affected_rows > 0) {
        $response["success"] = true;
        $response["message"] = "Product and associated links deleted successfully.";
    } else {
        $response["message"] = "No product found with the given ID.";
    }

    // Commit transaction
    $conn->commit();

    // Close statements
    $linkStmt->close();
    $productStmt->close();
} catch (Exception $e) {
    $conn->rollback(); // Rollback transaction on failure
    $response["message"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>
