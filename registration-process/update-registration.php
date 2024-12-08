<?php
include 'conn.php'; // Include the database connection file

// Check the connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

try {
    // Prepare the query using MySQLi
    $query = "
        SELECT si.seller_id, si.shop_name, si.stall_number, 
           si.business_permit_number, si.permit_image, si.shop_profile_pic, si.contact_number, si.shop_description, si.lazada_link, 
           si.shopee_link, si.created_at, s.first_name, s.last_name
    FROM registration si
    JOIN accounts s ON si.seller_id = s.seller_id
    ";

    // Execute the query
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result(); // Fetch result

        // Fetch all rows as an associative array
        $sellers = $result->fetch_all(MYSQLI_ASSOC);

        // Return the data as JSON
        echo json_encode(['success' => true, 'data' => $sellers]);
    } else {
        // Handle query preparation error
        echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    }
} catch (Exception $e) {
    // Catch any exceptions and return the error message as JSON
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close the connection
$conn->close();
?>
