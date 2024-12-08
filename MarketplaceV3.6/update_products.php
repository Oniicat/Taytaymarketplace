<?php
include "conn.php"; // Database connection

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate required fields
        if (empty($_POST['product_id']) || empty($_POST['name']) || empty($_POST['price']) || empty($_POST['category']) || empty($_POST['description'])) {
            throw new Exception('Required fields are missing.');
        }

        // Retrieve form data
        $productId = intval($_POST['product_id']);
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $links = $_POST['link'] ?? []; // Array of links
        $linkname = $_POST['linkname'] ?? []; // Array of link names

        // Update `tb_products`
        $stmt = $conn->prepare("
            UPDATE tb_products 
            SET product_name = ?, product_desc = ?, product_price = ?, category = ? 
            WHERE product_id = ?
        ");
        $stmt->bind_param("ssdsi", $name, $description, $price, $category, $productId);

        if (!$stmt->execute()) {
            throw new Exception('Failed to update product: ' . $stmt->error);
        }

        // Handle major image update
        if (!empty($_FILES['major_image']['name'])) {
            $majorImageName = $_FILES['major_image']['name'];
            $majorImageTmpName = $_FILES['major_image']['tmp_name'];

            if ($_FILES['major_image']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Failed to upload major image: ' . $_FILES['major_image']['error']);
            }

            $targetDir = "Content/";
            if (!is_writable($targetDir)) {
                throw new Exception('Target directory is not writable: ' . $targetDir);
            }

            $majorImagePath = $targetDir . basename($majorImageName);
            if (!move_uploaded_file($majorImageTmpName, $majorImagePath)) {
                throw new Exception('Failed to move major image ' . $majorImageName);
            }

            // Replace the existing major image in `tb_product_images`
            $stmtMajorImage = $conn->prepare("
                INSERT INTO tb_product_images (product_id, images) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE images = VALUES(images)
            ");
            $stmtMajorImage->bind_param("is", $productId, $majorImagePath);

            if (!$stmtMajorImage->execute()) {
                throw new Exception('Failed to update major image in database: ' . $stmtMajorImage->error);
            }
        }

        // Handle multiple image updates
        if (!empty($_FILES['multiple_images']['name'][0])) {
            // Clear existing images for the product (optional, depending on use case)
            $stmtClearImages = $conn->prepare("DELETE FROM tb_product_images WHERE product_id = ? AND images != ?");
            $stmtClearImages->bind_param("is", $productId, $majorImagePath);
            $stmtClearImages->execute();

            foreach ($_FILES['multiple_images']['name'] as $key => $imageName) {
                $imageTmpName = $_FILES['multiple_images']['tmp_name'][$key];

                if ($_FILES['multiple_images']['error'][$key] !== UPLOAD_ERR_OK) {
                    throw new Exception('Failed to upload image ' . $imageName . ': ' . $_FILES['multiple_images']['error'][$key]);
                }

                $targetDir = "Content/";
                if (!is_writable($targetDir)) {
                    throw new Exception('Target directory is not writable: ' . $targetDir);
                }

                $imagePath = $targetDir . basename($imageName);
                if (!move_uploaded_file($imageTmpName, $imagePath)) {
                    throw new Exception('Failed to move uploaded image ' . $imageName);
                }

                // Insert new images into `tb_product_images`
                $stmtImage = $conn->prepare("
                    INSERT INTO tb_product_images (product_id, images) 
                    VALUES (?, ?)
                ");
                $stmtImage->bind_param("is", $productId, $imagePath);

                if (!$stmtImage->execute()) {
                    throw new Exception('Failed to insert image into database: ' . $stmtImage->error);
                }
            }
        }

        // Handle links update
        if (!empty($links) && is_array($links) && is_array($linkname)) {
            // Clear existing links for the product
            $stmtClearLinks = $conn->prepare("DELETE FROM tb_product_links WHERE product_id = ?");
            $stmtClearLinks->bind_param("i", $productId);
            $stmtClearLinks->execute();

            // Insert new links
            $stmtLink = $conn->prepare("INSERT INTO tb_product_links (product_id, link_name, links) VALUES (?, ?, ?)");
            foreach ($links as $index => $link) {
                $currentLinkName = $linkname[$index] ?? null;
                if ($currentLinkName === null) {
                    throw new Exception("Missing link name for link at index $index.");
                }

                $stmtLink->bind_param("iss", $productId, $currentLinkName, $link);
                if (!$stmtLink->execute()) {
                    throw new Exception('Failed to insert link: ' . $stmtLink->error);
                }
            }
        }

        // Return success response
        $response['success'] = true;
        $response['message'] = 'Product, images, and links updated successfully!';
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage(); // Return detailed error message
}

// Send response as JSON
echo json_encode($response);
?>