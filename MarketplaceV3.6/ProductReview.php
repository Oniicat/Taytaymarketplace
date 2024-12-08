<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ProductReview.css">
    <title>Product Review</title>
</head>
<style>
.custom-navbar {
    background-color: white ;
    padding: 15px 30px;
    position: fixed;
    top: 0px;
    width: 100%;
    z-index: 1000;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

.navbar-center {
    display: flex;
    align-items: center;
    gap: 20px;
    max-width: 900px;
    width: 100%;
}

.navbar-center .navbar-logo {
    width: 110px;
    height: auto;
    margin-right: 40px;
    margin-left: -250px;
}

@media (max-width: 768px) {
    .navbar-center .navbar-logo {
        width: 50px;
        margin-left: 10px;
    }
}

@media (max-width: 480px) {
    .navbar-center .navbar-logo {
        width: 40px;
        margin-left: 8px;
    }
}

.back-btn {
    margin-left: 940px;
    background-color: white;
    color: #712798;
    padding: 0.5rem 1rem;
    border: 2px solid #712798;
    border-radius: 5px;
    font-size: 1rem;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    z-index: 1001;
    transition: background-color 0.3s, transform 0.3s, color 0.3s;
}

.back-btn:hover {
    background-color: #712798;
    color: white;
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .back-btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }
}

<?php
include '../registration-process/conn.php';

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']); // Ensure it's an integer to avoid SQL injection

    $sql = "SELECT * FROM tb_products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    registerClick($product_id);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }

    // Fetch the major image
    $sql_major_image = "SELECT images FROM tb_product_images WHERE product_id = ? LIMIT 1";
    $stmt_major_image = $conn->prepare($sql_major_image);
    $stmt_major_image->bind_param("i", $product_id);
    $stmt_major_image->execute();
    $result_major_image = $stmt_major_image->get_result();
    
    $major_image = null;
    if ($result_major_image->num_rows > 0) {
        $image_row = $result_major_image->fetch_assoc();
        $major_image = $image_row['images'];
    }
    
    // Fetch the remaining images
    $sql_remaining_images = "SELECT images FROM tb_product_images WHERE product_id = ? AND images != ?";
    $stmt_remaining_images = $conn->prepare($sql_remaining_images);
    $stmt_remaining_images->bind_param("is", $product_id, $major_image);
    $stmt_remaining_images->execute();
    $result_remaining_images = $stmt_remaining_images->get_result();
    
    $remaining_images = [];
    if ($result_remaining_images->num_rows > 0) {
        while ($image_row = $result_remaining_images->fetch_assoc()) {
            $remaining_images[] = $image_row['images'];
        }
    }

    // Fetch product links
    $sql_links = "SELECT link_name, links FROM tb_product_links WHERE product_id = ?";
    $stmt_links = $conn->prepare($sql_links);
    $stmt_links->bind_param("i", $product_id);
    $stmt_links->execute();
    $result_links = $stmt_links->get_result();

    $product_links = [];
    if ($result_links->num_rows > 0) {
        while ($row = $result_links->fetch_assoc()) {
            $product_links[] = [
                'link_name' => $row['link_name'], // Extract link_name
                'link' => $row['links']          // Extract link
            ];
        }
    } else {
        echo "No product links found for this product.";
        exit;
    }
}


    function registerClick($productId) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM tb_product_clicks WHERE product_id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $click = $result->fetch_assoc();
    
        if ($click) {
        $stmt = $conn->prepare("UPDATE tb_product_clicks SET click_count = click_count + 1, updated_at = NOW() WHERE product_id = ?");
        } else {
        $stmt = $conn->prepare("INSERT INTO tb_product_clicks (product_id, click_count, updated_at) VALUES (?, 1, NOW())");
        }
        $stmt->bind_param("i", $productId);
        $stmt->execute();
    }

    
?>

</style>
<body>
<!-- Navbar -->
<div class="custom-navbar">

    <div class="navbar-center">
        <a href="Market_Place_Dashboard.php"> <!--???-->
            <img src="Content/New Logo.png" alt="Logo" class="navbar-logo">
                <!-- Back Button -->
            <a href="Market_Place_Dashboard.php" class="back-btn">Back</a>
        </a>
    </div>
</div>


<div class="parent-container">
    <div class="content-container">
        <!-- Content Container -->
<div class="content-container">
<!-- Image Carousel -->
 <div class="image-carousel">

    <!-- Dynamically loop through remaining images -->
    <?php if (!empty($remaining_images)): ?>
    <?php 
        // Limit to 3 images
        $limited_images = array_slice($remaining_images, 0, 5); 
    ?>
    <?php foreach ($limited_images as $image): ?>
        <img src="<?php echo htmlspecialchars($image); ?>" alt="Product Image" class="carousel-image" onclick="changeMainImage(this)">
    <?php endforeach; ?>
<?php else: ?>
    <p>No additional images available.</p>
<?php endif; ?>

</div>

    <!-- Product Image Container -->
    <div class="product-image-container">
        <div class="product-image">
            <img id="main-product-image" src="<?php echo htmlspecialchars($major_image); ?>" alt="Product Image">
        </div>
    </div>
    <!-- Product Details -->
        <div class="product-details">
            <h1 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <p class="product-price">₱<?php echo number_format($product['product_price'], 2); ?></p>
            <p class="product-description">
                <?php echo htmlspecialchars($product['product_desc']); ?>
            </p>
        </div>
    </div>
    </div>
</div>

<!-- Product Image -->
<div class="product-link-container"> 
    <!-- Text Container for heading -->
    <div class="text-container">
        <p>Product Links</p>
    </div>

    <!-- Dynamically generate product links -->
    <?php foreach ($product_links as $link): ?>
        <a href="<?php echo htmlspecialchars($link['link']); ?>" class="product-link" target="_blank" rel="noopener noreferrer">
            <p><?php echo htmlspecialchars($link['link_name']); ?></p>
        </a>
    <?php endforeach; ?>
</div>

<?php 
include "../registration-process/conn.php";

// Assuming $product_id is provided (you might need to pass it via GET or POST)
$product_id = $_GET['product_id'] ?? 0; // Replace with actual source

// Fetch product and shop details
$sql_reviews = "SELECT p.*, s.*
                FROM tb_products p
                INNER JOIN shops s ON s.shop_id = p.shop_id
                WHERE p.product_id = ?";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $product_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();

if ($result_reviews->num_rows > 0) {
    $product = $result_reviews->fetch_assoc(); // Fetch single product record
} else {
    echo "No product or reviews found.";
    exit; // Stop further execution
}
?>





<!-- View Shop -->
<div class="image-container">
    <div class="posted-by">Posted By</div>
    <div class="image-content">
        <img src="Content/Wallnut.png" alt="Shop Image" class="shop-image">
        <div class="shop-info">
            <h2 class="shop-name"><?php echo htmlspecialchars($product['shop_name']); ?></h2>
            <p class="contact-number">Contact: <?php echo htmlspecialchars($product['contact_number']); ?></p>
        </div>
    </div>
    <a href="ViewShop.php?shop_id=<?php echo $product['shop_id']; ?>" class="visit-shop-link">Visit Shop</a>
</div>


<!-- Reviews Section -->

<!-------------------------------------------------------JS Functions------------------------------------------------------------------------>
<script>
        // JavaScript to handle image change on carousel click
        function changeMainImage(clickedImage) {
            // Get the source of the clicked image
            const newImageSrc = clickedImage.src;
            
            // Get the main product image container and change its src
            document.getElementById('main-product-image').src = newImageSrc;
        }
    </script>
</body>
</html>
