<?php
include '../registration-process/conn.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate required fields
        if (empty($_POST['product_id']) || empty($_POST['name']) || empty($_POST['price']) || empty($_POST['category']) || empty($_POST['description'])) {
            throw new Exception('Required fields are missing.');
        }

        // Retrieve form data
        $productId = $_POST['product_id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $links = $_POST['link'] ?? []; // Array of links
        $linkname = $_POST['linkname'] ?? []; // Array of links

        // Handle image upload
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "Content/"; // Folder where the image will be stored
            $imagePath = $targetDir . basename($_FILES['image']['name']);

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                throw new Exception('Failed to upload image. Error code: ' . $_FILES['image']['error']);
            }
        }

        // Update `tb_products`
        $updateQuery = "
            UPDATE tb_products 
            SET product_name = ?, 
                product_desc = ?, 
                product_price = ?, 
                category = ?" . 
                ($imagePath ? ", product_image = ?" : "") . 
            " WHERE product_id = ?";
        $stmt = $conn->prepare($updateQuery);

        if ($imagePath) {
            $stmt->bind_param("ssdssi", $name, $description, $price, $category, $imagePath, $productId);
        } else {
            $stmt->bind_param("ssdsi", $name, $description, $price, $category, $productId);
        }

        if (!$stmt->execute()) {
            throw new Exception('Failed to update product: ' . $stmt->error);
        }

        // Update `tb_product_links`
        // Delete existing links for the product
        $deleteLinksQuery = "DELETE FROM tb_product_links WHERE product_id = ?";
        $deleteStmt = $conn->prepare($deleteLinksQuery);
        $deleteStmt->bind_param("i", $productId);
        if (!$deleteStmt->execute()) {
            throw new Exception('Failed to delete existing links: ' . $deleteStmt->error);
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
        $response['message'] = 'Product and links updated successfully!';
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage(); // Return detailed error message
}

// Send response as JSON
echo json_encode($response);
?>
