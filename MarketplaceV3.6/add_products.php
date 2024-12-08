<?php
include '../registration-process/conn.php';

session_start();
$response = ['success' => false, 'message' => ''];

// taenamo
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate required fields
        if (empty($_POST['name']) || empty($_POST['price']) || empty($_POST['category']) || empty($_POST['description'])) {
            throw new Exception('Required fields are missing.');
        }

        // Retrieve form data
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $links = $_POST['link'] ?? []; // Array of links
        $linkname = $_POST['linkname'] ?? []; // Array of links

        $sellerID = $_SESSION['seller_id'];
        

        // Get current date and time
        $currentDate = date("Y-m-d"); // Adjust format if necessary (e.g., 'Y-m-d' for DATE column)


        // Insert into `tb_products`
        $stmt = $conn->prepare("
           INSERT INTO tb_products 
            (product_name, product_desc, product_price, category, date_created) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssdss", $name, $description, $price, $category, $currentDate)    ;

        if (!$stmt->execute()) {
            throw new Exception('Failed to insert product: ' . $stmt->error);
        }

        // Get the inserted product's ID
        $productId = $stmt->insert_id;

        // Handle major image upload
if (!empty($_FILES['major_image']['name'])) { // Check if there is a major image uploaded
    $majorImageName = $_FILES['major_image']['name'];
    $majorImageTmpName = $_FILES['major_image']['tmp_name'];
    
    // Validate the major image upload
    if ($_FILES['major_image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Failed to upload major image: ' . $_FILES['major_image']['error']);
    }

    // Ensure the target directory is writable
    $targetDir = "Content/";
    if (!is_writable($targetDir)) {
        throw new Exception('Target directory is not writable: ' . $targetDir);
    }

    // Handle major image file upload
    $majorImagePath = $targetDir . basename($majorImageName);
    if (!move_uploaded_file($majorImageTmpName, $majorImagePath)) {
        throw new Exception('Failed to move major image ' . $majorImageName);
    }

    // Insert the major image into `tb_product_images`
    $imageStmt = $conn->prepare("
        INSERT INTO tb_product_images (product_id, images) 
        VALUES (?, ?)
    ");
    $imageStmt->bind_param("is", $productId, $majorImagePath);

    if (!$imageStmt->execute()) {
        throw new Exception('Failed to insert major image into database: ' . $imageStmt->error);
    }
}

// Handle multiple image uploads
if (!empty($_FILES['multiple_images']['name'][0])) { // Check if there are multiple images uploaded
    foreach ($_FILES['multiple_images']['name'] as $key => $imageName) {
        $imageTmpName = $_FILES['multiple_images']['tmp_name'][$key];

        // Validate each uploaded image
        if ($_FILES['multiple_images']['error'][$key] !== UPLOAD_ERR_OK) {
            throw new Exception('Failed to upload image ' . $imageName . ': ' . $_FILES['multiple_images']['error'][$key]);
        }

        // Handle image file upload
        $imagePath = $targetDir . basename($imageName);
        if (!move_uploaded_file($imageTmpName, $imagePath)) {
            throw new Exception('Failed to move uploaded image ' . $imageName);
        }

        // Insert each image into `tb_product_images`
        $imageStmt = $conn->prepare("
            INSERT INTO tb_product_images (product_id, images) 
            VALUES (?, ?)
        ");
        $imageStmt->bind_param("is", $productId, $imagePath);

        if (!$imageStmt->execute()) {
            throw new Exception('Failed to insert image into database: ' . $imageStmt->error);
        }
    }
}

        // Insert links into `tb_product_links`
        if (!empty($links) && is_array($links) && is_array($linkname)) {
            $linkStmt = $conn->prepare("INSERT INTO tb_product_links (product_id, link_name, links) VALUES (?, ?, ?)");
            foreach ($links as $index => $link) {
                // Ensure corresponding `linkname` exists
                $currentLinkName = $linkname[$index] ?? null;
                if ($currentLinkName === null) {
                    throw new Exception("Missing link name for link at index $index.");
                }

                $linkStmt->bind_param("iss", $productId, $currentLinkName, $link);
                if (!$linkStmt->execute()) {
                    throw new Exception('Failed to insert link: ' . $linkStmt->error);
                }
            }
        }


        $response['success'] = true;
        $response['message'] = 'Product and links added successfully!';
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage(); // Return detailed error message
}

// Send response as JSON
echo json_encode($response);
?>
