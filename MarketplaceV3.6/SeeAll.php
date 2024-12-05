<?php 
include '../registration-process/conn.php';

// Fetch categories from the database
$sql = "SELECT category_name FROM tb_category";
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category_name'];
    }
} else {
    echo "No categories found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="SeeAll.css">
    <title>See all</title>
</head>
<body>
<!-- Navbar -->
<div class="custom-navbar">
    <div class="navbar-center">
        <a href="Seller_Dashboard.php">
        <img src="Content/New Logo.png" alt="Logo" class="navbar-logo">
        </a>
        <!-- Back Button -->
        <a href="Market_Place_Dashboard.php" class="back-btn">Back</a>
    </div>
</div>

<!-- Unified Container -->
<div class="header-and-content-container">

<?php
// Fetch the section label from the query parameter
$sectionLabel = isset($_GET['section']) ? htmlspecialchars($_GET['section']) : 'Default';
?>
  <!-- My Products Text -->
  <div class="Marketplace-text">Market Place - <?php echo $sectionLabel; ?></div>

  <!-- Unified Container for Search and other buttons -->
  <div class="Search&Button-container">
  <!-- Search Bar -->
  <div class="search-bar-container">
    <input type="text" id="search-input" placeholder="Search for products, categories, or more...">
    <button onclick="handleSearch()">Search</button>
  </div>

  <!-- Action Buttons and Dropdowns -->
  <div class="action-controls">
    <!-- New Category Dropdown -->
    <div class="dropdown product-categories">
      <button onclick="toggleProductDropdown()">Categories <i class="arrow"></i></button>
      <div class="dropdown-content">
        <?php foreach ($categories as $category): ?>
          <a href="#" onclick="filterByCategory('<?php echo htmlspecialchars($category); ?>')">
        <?php echo htmlspecialchars($category); ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Popular Button -->
    <button class="popular-button">Popular</button>

    <!-- Latest Button -->
    <button class="Latest-button">Latest</button>

    <!-- New Price Dropdown -->
    <div class="dropdown price-dropdown">
      <button onclick="togglePriceDropdown()">Price <i class="arrow"></i></button>
      <div class="dropdown-content">
        <a href="#">Low to High</a>
        <a href="#">High to Low</a>
      </div>
    </div>
  </div>

  <!-- Section Label -->
<?php
// Fetch the section label from the query parameter
$sectionLabel = isset($_GET['section']) ? htmlspecialchars($_GET['section']) : 'Default';
?>
<div class="section-label">
    <?php echo $sectionLabel; ?>
</div>

  <div class="product-widget-container" id="product-widget-container">
     <!-- product widgets here -->
  </div>
</div>

<!-------------------------------------------------------JS Functions--------------------------------------------------------------------------->

<script>
 function truncateText(text, maxLength) {
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

// Function for products to be displayed on widgets
async function fetchProductsByCategory(category, containerId) {
  try {
    // Fetch products from the server for the specified category
    const response = await fetch(`get_products.php?category=${encodeURIComponent(category)}`);
    const products = await response.json();

    // Get the container for the specified section
    const container = document.getElementById(containerId);
    container.innerHTML = ''; // Clear existing content

    // Loop through the products and create HTML for each
    products.forEach(product => {
      const truncatedDesc = truncateText(product.product_desc, 40); // Limit characters
      const productWidget = `
        <div class="product-widget" onclick="redirectToProductReview(${product.product_id})">
          <div class="product-image">
            <img src="${product.product_image}" alt="Product Image">
          </div>
          <div class="product-info">
            <h3>${product.product_name}</h3>
            <p>${truncatedDesc}</p>
          </div>
          <div class="product-price">₱${parseFloat(product.product_price).toFixed(2)}</div>
        </div>
      `;
      container.innerHTML += productWidget;
    });
  } catch (error) {
    console.error(`Error fetching products for category "${category}":`, error);
  }
}

function redirectToProductReview(productId) {
  // Redirect to Product_Review.php with the product ID as a query parameter
  window.location.href = `ProductReview.php?product_id=${encodeURIComponent(productId)}`;
}

document.addEventListener('DOMContentLoaded', () => {
  // Get the section label from PHP and pass it to fetchProductsByCategory
  const sectionLabel = "<?php echo $sectionLabel; ?>";
    
    // Fetch products for the category based on the section label
    fetchProductsByCategory(sectionLabel, 'product-widget-container');
});



  // Dropdown Functions
  function toggleDropdown() {
    const dropdown = document.querySelector('.dropdown');
    dropdown.classList.toggle('show');
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.dropdown');
    if (!dropdown.contains(event.target)) {
      dropdown.classList.remove('show');
    }
  });
    // Handle the Search Functionality
    function handleSearch() {
    const searchQuery = document.getElementById('search-input').value.trim();
    if (searchQuery) {
      // Redirect or filter products based on the search query
      console.log('Searching for:', searchQuery);
      alert('Search functionality not yet implemented for: ' + searchQuery);
    } else {
      alert('Please enter a search query.');
    }
  }
  // Toggle function for the Price dropdown
  function togglePriceDropdown() {
    const priceDropdown = document.querySelector('.price-dropdown');
    priceDropdown.classList.toggle('show');
}

// Close the dropdown when clicking outside
document.addEventListener('click', function (event) {
    const priceDropdown = document.querySelector('.price-dropdown');
    if (!priceDropdown.contains(event.target) && !event.target.closest('.price-dropdown button')) {
        priceDropdown.classList.remove('show');
    }
});

  // Attach click event to all product widgets
  document.addEventListener('DOMContentLoaded', function () {
  const productWidgets = document.querySelectorAll('.product-widget');

  productWidgets.forEach(widget => {
    widget.addEventListener('click', function () {
      // Get the product title from the widget (you can customize this as needed)
      const productTitle = this.querySelector('.product-info h3').textContent;

      // Redirect to ProductReview.php with the product title in the query string
      window.location.href = 'ProductReview.php?product=' + encodeURIComponent(productTitle);
    });
  });
});

// Toggle function for the new category dropdown beside the Add Product button
function toggleProductDropdown() {
    const productDropdown = document.querySelector('.product-categories');
    productDropdown.classList.toggle('show');
}

// Close the dropdown when clicking outside
document.addEventListener('click', function (event) {
    const productDropdown = document.querySelector('.product-categories');
    if (!productDropdown.contains(event.target) && !event.target.closest('.product-categories button')) {
        productDropdown.classList.remove('show');
    }
});

</script>
</body>
</html>