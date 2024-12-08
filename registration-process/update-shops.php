<?php
include 'conn.php'; // Include the database connection file

// Set the response type to JSON
header('Content-Type: application/json');

// Check the connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

try {
    // Prepare the SQL query
    $query = "SELECT si.seller_id, si.shop_name, si.stall_number, 
           si.business_permit_number, si.permit_image, si.shop_profile_pic, si.contact_number, si.shop_description, si.lazada_link, 
           si.shopee_link, si.created_at, s.first_name, s.last_name
    FROM registered_shops si
    JOIN accounts s ON si.seller_id = s.seller_id";

    // Execute the query
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result(); // Get the result set

        // Fetch all data as an associative array
        $sellers = $result->fetch_all(MYSQLI_ASSOC);

        // Return the data as a JSON object
        echo json_encode(['success' => true, 'data' => $sellers]);
    } else {
        // Handle query preparation failure
        echo json_encode(['success' => false, 'error' => 'Query preparation failed: ' . $conn->error]);
    }
} catch (Exception $e) {
    // Handle any exceptions
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Close the connection
$conn->close();
?>
