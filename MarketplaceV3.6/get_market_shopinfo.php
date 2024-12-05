<?php
include '../registration-process/conn.php';

$user_id = 1; // Replace with dynamic user ID, di pa toh nakabase kung sino naka log in

$sql = "SELECT * FROM tb_users WHERE user_id = ?"; // shop profile, not the user itself
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="UserProfile.css">
    <link rel="stylesheet" href="navbar.css">
    <title>User Profile</title>
</head>
<style>
/* Back Button Styles */
.back-btn {
    margin-left: 850px;
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

/* Hover Effect */
.back-btn:hover {
    background-color: #712798;
    color: white;
    transform: scale(1.05);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .back-btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }
}

</style>
<body>
<!-- Navbar -->
<div class="custom-navbar">
    <div class="navbar-center">
        <a href="Seller_Dashboard.php">
            <img src="Content/New Logo.png" alt="Logo" class="navbar-logo">
        </a>
        <a href="Seller_Dashboard.php" class="back-btn">Back</a>
    </div>
</div>

<!-- Unified Seller Container -->
<div class="unified-seller-container">
    <!-- Seller Header Image -->
    <div class="header-image">
        <img src="Content/Header.png" alt="Seller Header Image">
    </div>

    <div class="seller-container">
    <!-- Seller Details -->
    <div class="seller-details">
        <!-- Edit Button -->
        <!-- Seller Image -->
        <div class="seller-image">
            <img src="<?php echo $user['profile_pic']; ?>" alt="Seller Image">
        </div>

        <!-- Seller Info -->
        <div class="seller-info">
            <h2><?php echo $user['user_name']; ?></h2>
            <p><?php echo $user['contact_num']; ?></p>
        </div>
    </div>
</div>

    <!-- Seller Widget -->
    <div class="seller-widget">
        <div class="widget-grid">
            <a href="<?php echo $user['shopee_link']; ?>" target="_blank" class="widget-item">
                <img src="Content/Shoppee.png" alt="Widget Image 1">
            </a>
            <a href="<?php echo $user['lazada_link']; ?>" target="_blank" class="widget-item">
                <img src="Content/lazada.png" alt="Widget Image 2">
            </a>
            </a>
        </div>
    </div>
    
    <!-- Shop Details Dropdown -->
    <div class="shop-details-dropdown-container">
            <button onclick="toggleShopDetails()">About Us <i class="arrow"></i></button>
            <div id="shop-details-content" class="shop-details-content">
                <p><?php echo $user['shop_desc']; ?></p>
            </div>
        </div>

<!-- Product Widget Dropdown -->
<div class="product-widget-dropdown-container">
    <button onclick="toggleProductWidgets()">Products <i class="arrow"></i></button>
    <div id="product-widget-content" class="product-widget-content">
            <div class="product-widget-container" id="product-widget-container">
                
            </div>
    </div>
</div>


<!-------------------------------------------------------JS Functions------------------------------------------------------------------------>
<script>
    // Function to truncate text if it's too long
    function truncateText(text, maxLength) {
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

   async function fetchProducts() { //for retrieving all products from database
    try {
    const response = await fetch('get_products.php');
    const products = await response.json();

    // Get the container where products will be displayed
    const container = document.getElementById('product-widget-container');
    container.innerHTML = ''; // Clear existing content

    // Loop through the products and create HTML for each
    products.forEach(product => {
      const truncatedDesc = truncateText(product.product_desc, 40); // Limit characters
      const productWidget = `
        <div class="product-widget">
          <div class="product-image">
            <img src="${product.product_image}" alt="Product Image">
          </div>
          <div class="product-info">
            <h3>${product.product_name}</h3>
            <p>${truncatedDesc}</p>
            <div class="price">$${parseFloat(product.product_price).toFixed(2)}</div>
          </div>
        
        </div>
      `;
      container.innerHTML += productWidget;
    });
  } catch (error) {
    console.error('Error fetching products:', error);
  }
}

document.addEventListener('DOMContentLoaded', fetchProducts);

function toggleProductWidgets() {
    const dropdown = document.getElementById('product-widget-content');
    const arrow = document.querySelector('.product-widget-dropdown-container .arrow');
    dropdown.classList.toggle('show');
    arrow.classList.toggle('rotate');
}

// Ensure the dropdown is initially closed by default
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('product-widget-content');
    dropdown.classList.remove('show'); // Make sure it's hidden when the page loads
});
    // Function to toggle the shop details dropdown visibility
    function toggleShopDetails() {
        const dropdown = document.getElementById('shop-details-content');
        const arrow = document.querySelector('.shop-details-dropdown-container .arrow');
        dropdown.classList.toggle('show');
        arrow.classList.toggle('rotate');
    }
</script>

</body>
</html>
