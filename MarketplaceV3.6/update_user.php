<?php
include '../registration-process/conn.php';

header('Content-Type: application/json');

$response = ["success" => false]; // Default response

$user_id = 1; // Replace with dynamic user ID as needed

try {
    // Get POST data galing sa formData.append
    $shop_desc = $_POST['shop_desc'] ?? '';
    $contact_num = $_POST['contact_num'] ?? '';
    $shopee_link = $_POST['shopee_link'] ?? '';
    $lazada_link = $_POST['lazada_link'] ?? '';
    $profile_image = $_FILES['profile_pic'] ?? null;

    // Handle file upload
    $profile_image_url = null;
    if ($profile_image && $profile_image['error'] === UPLOAD_ERR_OK) {
        $target_dir = "Content/";
        $target_file = $target_dir . basename($profile_image['name']);
        if (move_uploaded_file($profile_image['tmp_name'], $target_file)) {
            $profile_image_url = $target_file;
        } else {
            throw new Exception("Failed to upload image.");
        }
    }

    // Prepare and execute the update query
    $sql = "UPDATE tb_users 
            SET shop_desc = ?, 
                contact_num = ?, 
                shopee_link = ?, 
                lazada_link = ?" .
           ($profile_image_url ? ", profile_pic = ?" : "") .
           " WHERE user_id = ?";
           
    $stmt = $conn->prepare($sql);

    if ($profile_image_url) {
        $stmt->bind_param("sssssi", $shop_desc, $contact_num, $shopee_link, $lazada_link, $profile_image_url, $user_id);
    } else {
        $stmt->bind_param("ssssi", $shop_desc, $contact_num, $shopee_link, $lazada_link, $user_id);
    }

    $stmt->execute();

    // Check if update was successful
    if ($stmt->affected_rows > 0) {
        $response["success"] = true;
    } else {
        $response["message"] = "No changes were made.";
    }

    $stmt->close();
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>
