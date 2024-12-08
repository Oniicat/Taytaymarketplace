<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'webdev2';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

$seller_id = $_SESSION['seller_id'] ?? null; // Get seller_id from session

if ($seller_id) {
    // Query to get shops for the seller
    $stmt = $conn->prepare("SELECT * FROM shops WHERE seller_id = ?");
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $shops = [];
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row; // Collect shop details
    }

    echo json_encode(['success' => true, 'shops' => $shops, 'seller_id' => $seller_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Seller ID not found.']);
}

$conn->close();
?>
