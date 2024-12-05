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
    $query = "SELECT seller_id, first_name, middle_name, last_name, contact_number, municipality, 
              baranggay, shop_name, stall_number, business_permit_number, permit_image 
              FROM shops ORDER BY created_at DESC";

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
