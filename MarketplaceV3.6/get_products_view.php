<?php
// Include the database connection file
include '../registration-process/conn.php';
session_start();

// Initialize an empty array for the response
$response = [];

// Get parameters from the GET request and sanitize them
$category = isset($_GET['category']) ? trim(htmlspecialchars($_GET['category'])) : '';
// $seller_id = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : 0; // Ensure seller_id is an integer
$shop_id = isset($_GET['shop_id']) ? intval($_GET['shop_id']) : 0; // Ensure shop_id is an integer

// Check if shop_id is valid
if ($shop_id <= 0) {
    $response['error'] = "Invalid shop ID.";
    echo json_encode($response);
    exit;
}

 $sql_major_image = "SELECT images FROM tb_product_images WHERE product_id = ? LIMIT 1";
    $stmt_major_image = $conn->prepare($sql_major_image);
    $stmt_major_image->bind_param("i", $product_id);
    $stmt_major_image->execute();
    $result_major_image = $stmt_major_image->get_result();
    
    $major_image = null;
    if ($result_major_image->num_rows > 0) {
        $image_row = $result_major_image->fetch_assoc();
        $major_image = $image_row['images'];
    }
    
    // Fetch the remaining images
    $sql_remaining_images = "SELECT images FROM tb_product_images WHERE product_id = ? AND images != ?";
    $stmt_remaining_images = $conn->prepare($sql_remaining_images);
    $stmt_remaining_images->bind_param("is", $product_id, $major_image);
    $stmt_remaining_images->execute();
    $result_remaining_images = $stmt_remaining_images->get_result();
    
    $remaining_images = [];
    if ($result_remaining_images->num_rows > 0) {
        while ($image_row = $result_remaining_images->fetch_assoc()) {
            $remaining_images[] = $image_row['images'];
        }
    }




try {
    // Prepare the SQL query dynamically based on the presence of a category
    if (!empty($category)) {
        $sql = "SELECT * FROM tb_products WHERE category = ? AND shop_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("si", $category, $shop_id);
    } else {
        $sql = "SELECT * FROM tb_products WHERE shop_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("i", $shop_id);
    }

    // Execute the query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);

        // Return the products as JSON
        echo json_encode($products);
    } else {
        throw new Exception("Failed to execute query: " . $stmt->error);
    }
} catch (Exception $e) {
    // Handle any errors
    $response['error'] = $e->getMessage();
    echo json_encode($response);
} finally {
    // Ensure resources are properly closed
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
