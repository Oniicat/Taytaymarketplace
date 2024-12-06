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

        // Handle image upload
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "Content/"; // Folder where the image will be stored
            $imagePath = $targetDir . basename($_FILES['image']['name']);

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                throw new Exception('Failed to upload image. Error code: ' . $_FILES['image']['error']);
            }
        }

        // Insert into `tb_products`
        $stmt = $conn->prepare("
            INSERT INTO tb_products 
            (product_name, seller_id, product_desc, product_price, product_image, category, date_created) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sisdsss", $name, $sellerID, $description, $price, $imagePath, $category, $currentDate);

        if (!$stmt->execute()) {
            throw new Exception('Failed to insert product: ' . $stmt->error);
        }

        // Get the inserted product's ID
        $productId = $stmt->insert_id;

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
