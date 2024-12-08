<?php
// Start the session
session_start();

// Get the sellerId from the POST request
$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['sellerId'])) {
    // Set the sellerId in the session
    $_SESSION['seller_id'] = $data['sellerId'];

    // Send a success response
    echo json_encode(['success' => true]);
} else {
    // Send an error response
    echo json_encode(['success' => false, 'message' => 'sellerId not provided']);
}
?>
