<?php
include "../registration-process/conn.php";

// Assuming $seller_id is provided (e.g., via GET or POST)
$seller_id = $_GET['seller_id'] ?? 0; // Replace with actual source

// Fetch shop details
$sql_shop = "SELECT *
             FROM shops 
             WHERE seller_id = ?";
$stmt_shop = $conn->prepare($sql_shop);
$stmt_shop->bind_param("i", $seller_id);
$stmt_shop->execute();
$result_shop = $stmt_shop->get_result();

if ($result_shop->num_rows > 0) {
    $shop = $result_shop->fetch_assoc(); // Fetch the shop details
} else {
    echo "Shop not found.";
    exit; // Stop further execution
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ViewShop.css">
    <title>View Shop</title>
</head>
<body>
<!-- Navbar -->
<div class="custom-navbar">
    <div class="navbar-center">
        <a href="Seller_Dashboard.php">
        <img src="Content/New Logo.png" alt="Logo" class="navbar-logo">
        </a>
        <a href="Market_Place_Dashboard.php" class="back-btn">Back</a>
    </div>
</div>



<!-- Shop Header -->
<div class="shop-header">
    <div class="shop-info">
        <img src="Content/Wallnut.png" alt="Profile Image" class="shop-profile-img">
        <div class="shop-details">
            <h3 class="shop-name"><?php echo htmlspecialchars($shop['shop_name']); ?></h3>
            <p class="shop-contact">Contact: <?php echo htmlspecialchars($shop['contact_number']); ?></p>
            <p class="shop-municipality">Municipality: <?php echo htmlspecialchars($shop['municipality']); ?></p>
        </div>
    </div>
</div>



<!-- Shops Label -->
<div class="shops-label">
    <h2>Shops</h2>
</div>

<div class="widget-container">
    <div class="widget" onclick="window.location.href='https://shopee.ph/';">
        <img src="Content/Shoppee.png" alt="Widget 2">
    </div>
    <div class="widget" onclick="window.location.href='https://www.lazada.com.ph/?spm=a2o4l.homepage.header.dhome.239eca18APOWqC';">
        <img src="Content/lazada.png" alt="Widget 1">
    </div>
</div>

<!-- Unified Container -->
<div class="header-and-content-container">
<!-- Products Label -->
<div class="Products-label">
    <h2>Products</h2>
</div>

<!-- Unified Container -->
<div class="header-and-content-container">
<!-- "See All" Button -->

<div class="product-widget-container" id="product-widget-container">
        <!--product widgets here-->
</div>
</div>


<!-- Footer -->
<footer class="footer"></footer>

<script>

    const sellerId = <?= json_encode($seller_id) ?>; // Get the seller ID from PHP

    // Function for products to be displayed on widgets
  // Function to fetch and display products in widgets
async function fetchProducts(category = '', sellerId = 0) {
  try {
    // Construct the URL with both category and seller_id as query parameters
    const url = `get_products_view.php?category=${encodeURIComponent(category)}&seller_id=${encodeURIComponent(sellerId)}`;

    // Fetch products from the server
    const response = await fetch(url);

    // Check if the response is OK
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    const products = await response.json();

    // Get the container where products will be displayed
    const container = document.getElementById('product-widget-container');
    container.innerHTML = ''; // Clear existing content

    // Check if there are products to display
    if (products.length === 0) {
      container.innerHTML = '<p>No products available.</p>';
      return;
    }

    // Loop through the products and create HTML for each
    products.forEach(product => {
      const truncateDesc = truncateText(product.product_desc, 40); // Limit description to 40 characters
      const productWidget = `
        <div class="product-widget">
          <div class="product-image">
            <img src="${product.product_image}" alt="Product Image">
          </div>
          <div class="product-info">
            <h3>${product.product_name}</h3>
            <p>${truncateDesc}</p>
          </div>
          <div class="product-price">$${parseFloat(product.product_price).toFixed(2)}</div>
        </div>
      `;
      container.innerHTML += productWidget;
    });
  } catch (error) {
    console.error('Error fetching products:', error);
    const container = document.getElementById('product-widget-container');
    container.innerHTML = '<p>Error fetching products. Please try again later.</p>';
  }
}

// Utility function to truncate text
function truncateText(text, maxLength) {
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

// Fetch products once the page is fully loaded
document.addEventListener('DOMContentLoaded', () => {
  fetchProducts('', sellerId); // Fetch all products initially (without a category filter)
});

</script>
</body>
</html>