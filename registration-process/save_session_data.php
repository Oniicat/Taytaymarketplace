<?php
session_start();
header("Content-Type: application/json");

// Ensure that both shop_id and seller_id are provided
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['shop_id']) && isset($data['seller_id'])) {
        // Save both the shop_id and seller_id in the session
        $_SESSION['shop_id'] = $data['shop_id'];
        $_SESSION['seller_id'] = $data['seller_id'];

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Session data saved successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Missing shop_id or seller_id.'
        ]);
    }
    exit();
}

// Return error if request method is not POST
echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
exit();
