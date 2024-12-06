<?php
//map location
$latitude = 14.557675;
$longitude = 121.132690;


//Zoom start
$zoomLevel = 18;


include '../registration-process/conn.php';


$content_keys = ['Address', 'OpeningClosing', 'Directions', 'CUBAO', 'EDSA'];

// Prepare an array to store the fetched content
$content_texts = [];

// Function to add a new line after each sentence
function addNewLinePerSentence($text) {
    // Ensure input is valid
    if (!$text) return '';
    // Add a `<br>` tag after sentence-ending punctuation (., ?, !)
    return preg_replace('/(?<=[.!?:])(?=\s|$)/', '<br>', $text);
}

// Loop through each content key and fetch its corresponding content
foreach ($content_keys as $content_key) {
    $query = "SELECT content_text FROM textchange WHERE content_key = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $content_key);
    $stmt->execute();
    $stmt->bind_result($content_text);
    $stmt->fetch();

    // Process the fetched content to add new lines between sentences
    $content_texts[$content_key] = addNewLinePerSentence($content_text);
    $stmt->close(); // Close the statement for the next iteration
}


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


// Fetch popular products by clicks
function getPopularProducts() {
  global $conn;
  $stmt = $conn->query("
    SELECT p.product_id, p.product_name, p.product_desc, p.product_price, c.click_count
    FROM tb_products p
    LEFT JOIN tb_product_clicks c ON p.id = c.product_id
    ORDER BY c.click_count DESC
  ");
  $products = $stmt->fetch_all(MYSQLI_ASSOC);


  foreach ($products as $product) {
    echo "ID: {$product['id']}, Name: {$product['name']}, Clicks: {$product['click_count']}<br>";
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="Market Place.css">
  <link rel="stylesheet" href="navbar.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
   
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <title>Marketplace Dashboard</title>
<style>
 body {
    background-color: #F4F4F4;
    margin: 0;
}
</style>


</head>
<body>
  <!-- Navbar -->
  <div class="custom-navbar">
    <div class="navbar-center">
      <img src="<?php echo file_exists('logo_path.txt') ? file_get_contents('logo_path.txt') : 'logo.png'; ?>" alt="Logo" class="navbar-logo">


      <!-- Get Directions btn -->
      <button class="get-directions-btn" onclick="showDirectionsModal()">Get Directions</button>


      <!-- Sign In btn, palagay nlng ng page sa sign in -->
      <button class="SignIn-btn" onclick="window.location.href = '../login_module/signin_page.php';">Sign In</button>


    </div>
  </div>
<!-- Unified Container -->
<div class="header-and-content-container">

<!-- Search Bar -->
<div class="search-bar-container">
  <input type="text" id="search-input" placeholder="Search for products, categories, or more...">
  <button class="search-button" onclick="handleSearch()">Search</button>
</div>


  <!-- Product Widgets -->
  <div class="product-widget-container-First">


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


  <button class="Latest-button">Latest</button>


  <!-- New Price Dropdown -->
  <div class="dropdown price-dropdown">
  <button onclick="togglePriceDropdown()">Price <i class="arrow"></i></button>
  <div class="dropdown-content">
  <a href="javascript:void(0);" id="low-to-high">Low to High</a>
  <a href="javascript:void(0);" id="high-to-low">High to Low</a>
  </div>
  </div>
 
  <!-------------------------------------- Container for displaying all products by price----------------------------------------------->
  <div id="product-sorted-results">
    <!-- price products will be displayed here -->
  </div>


  <!-------------------------------------- Container for displaying all latest products----------------------------------------------->
  <div id="product-latest-results">
    <!-- latest products will be displayed here -->
  </div>




  <!-------------------------------------- Container for displaying all popular products----------------------------------------------->
  <div id="product-widget-container-all-popular">
    <!-- Popular products will be displayed here -->
  </div>


  <!-------------------------------------- Container for displaying all search products----------------------------------------------->
  <div id="product-search-results">
  <!-- searched products will be displayed here -->
  </div>


  <!-------------------------------------------------------First Line Of Product Widgets-------------------------------------------------->

  <div class="product-widget-container" id="product-widget-container">
        <!--product widgets here-->
  </div>

<!-------------------------------------------------------Maps-------------------------------------------------->


<div class="Map-Details-container">
  <div class="background-container">
    <!-- Location Text -->
    <div class="location-text">Our Location</div>


    <!-- Map Container -->
    <div class="outer-container">
    <div class="map-container">
    <div id="map">
    </div>
    </div>
    </div>

<!-- Details Container -->
<div class="details-container">
      <h2 class="details-title">Taytay Tiangge</h2>
      <p class="details-text"><?php echo $content_texts['Address']; ?></p>
      <p class="details-text"><strong><?php echo $content_texts['OpeningClosing']; ?></strong></p>
      <div class="from-section">
        <span class="from-text">From:</span>
        <button class="from-btn">Directions</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Directions -->
<div id="directionsModal" class="directions-modal">
  <div class="directions-modal-content">
    <span class="close-modal-btn">&times;</span>
    
    <!-- Two Containers inside the modal -->
    <div class="directions-container">
      <div class="direction-section">
        <h3>CUBAO</h3>
        <p><?php echo $content_texts['CUBAO'] ?? ''; ?></p>
      </div>
      
      <div class="direction-section">
        <h3>EDSA</h3>
        <p><?php echo $content_texts['EDSA'] ?? ''; ?></p>
      </div>
    </div>
  </div>
</div>

<!-------------------------------------------------------JS Functions------------------------------------------------------------------------>


<script>


  //-----------------------------------------------Organize by Prize Functions-------------------------------------
  async function fetchAndDisplayProductsByPrice(order) {
  try {
    // Log the order being passed to ensure it's correct
    console.log("Fetching products with order:", order);


    // Fetch all products from the server (without category filter)
    const response = await fetch(`get_price.php?order=${order}`); // Use backticks for template literal
    const products = await response.json();


    // Hide all other sections
    const sections = document.querySelectorAll('.product-widget-container');
    sections.forEach(section => {
      section.style.display = 'none';
    });


    // Get the container where the sorted products will be displayed
    const container = document.getElementById('product-search-results');
    container.innerHTML = ''; // Clear existing content


    // Loop through all the products and create HTML for each
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
    console.error('Error fetching products:', error);
  }
}


// Event listener for "Low to High"
document.getElementById('low-to-high').addEventListener('click', function () {
  // Hide the section labels for categories
  document.body.classList.add('price-view');


  // Fetch and display products in ascending order
  fetchAndDisplayProductsByPrice('ASC');
});


// Event listener for "High to Low"
document.getElementById('high-to-low').addEventListener('click', function () {
  // Hide the section labels for categories
  document.body.classList.add('price-view');


  // Fetch and display products in descending order
  fetchAndDisplayProductsByPrice('DESC');
});




//-----------------------------------------------Organize by Latest Functions-------------------------------------
async function handleLatest() {
  try {
    // Fetch all latest products from the server (without category filter)
    const response = await fetch('get_latest_products');
    const products = await response.json();


    // Hide all other sections
    const sections = document.querySelectorAll('.product-widget-container');
    sections.forEach(section => {
      section.style.display = 'none';
    });


    // Get the container where the latest products will be displayed
    const container = document.getElementById('product-search-results');
    container.innerHTML = ''; // Clear existing content


    // Loop through all the products and create HTML for each
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
    console.error('Error fetching popular products:', error);
  }
}


// Event listener for the "Latest" button
document.querySelector('.Latest-button').addEventListener('click', function () {
  // Hide all product sections
  const sections = document.querySelectorAll('.product-widget-container');
  sections.forEach(section => {
    section.style.display = 'none';
  });


  // Show the latest products container
  const popularSection = document.getElementById('product-latest-results');
  popularSection.style.display = ''; // Show latest products container


  // Hide the section labels for categories
  document.body.classList.add('latest-view');


  // Fetch and display latest products
  handleLatest();
});


  //-----------------------------------------------Search Functions-------------------------------------
  async function handleSearch() {
  const searchInput = document.getElementById('search-input').value.trim();


  if (searchInput === '') {
    alert('Please enter a search term.');
    return;
  }


  // Hide all other sections
  const sections = document.querySelectorAll('.product-widget-container');
  sections.forEach(section => {
    section.style.display = 'none';
  });


  // Hide the popular products container
  const popularSection = document.getElementById('product-widget-container-all-popular');
  popularSection.style.display = 'none';


  // Show the search products container
  const searchResultsContainer = document.getElementById('product-search-results');
  searchResultsContainer.innerHTML = ''; // Clear previous results
  searchResultsContainer.style.display = 'block'; // Ensure it's visible


  try {
    // Fetch products based on the search query
    const response = await fetch(`search_products.php?query=${encodeURIComponent(searchInput)}`);
    const products = await response.json();


    if (products.length > 0) {
      // Display each matching product
      products.forEach(product => {
        const truncatedDesc = truncateText(product.product_desc, 40); // Limit description to 40 characters
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
        searchResultsContainer.innerHTML += productWidget;
      });
    } else {
      searchResultsContainer.innerHTML = '<p>No products found matching your search.</p>';
    }
  } catch (error) {
    console.error('Error during search:', error);
    alert('An error occurred while searching for products.');
  }
}


// Event listener for the "search" button
document.querySelector('.search-button').addEventListener('click', function () {
  // Hide all product sections
  const sections = document.querySelectorAll('.product-widget-container');
  sections.forEach(section => {
    section.style.display = 'none';
  });


  // Show the search products container
  const popularSection = document.getElementById('product-search-results');
  popularSection.style.display = ''; // Show popular products container


  // Hide the section labels for categories
  document.body.classList.add('search-view');


});


// Function to truncate text if it's too long
function truncateText(text, maxLength) {
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}


//-----------------------------------------------Organize by Popular Functions-------------------------------------
async function fetchAllPopularProducts() {
  try {
    // Fetch all popular products from the server (without category filter)
    const response = await fetch('get_popular_products.php');
    const products = await response.json();


    // Hide all other sections, including popular products
    const sections = document.querySelectorAll('.product-widget-container');
    sections.forEach(section => {
      section.style.display = 'none';
    });


    // Get the container where the popular products will be displayed
    const container = document.getElementById('product-search-results');
    container.innerHTML = ''; // Clear existing content


    // Loop through all the products and create HTML for each
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
    console.error('Error fetching popular products:', error);
  }
}




// Event listener for the "Popular" button
document.querySelector('.popular-button').addEventListener('click', function () {
  // Hide all product sections
  const sections = document.querySelectorAll('.product-widget-container');
  sections.forEach(section => {
    section.style.display = 'none';
  });


  // Show the popular products container
  const popularSection = document.getElementById('product-widget-container-all-popular');
  popularSection.style.display = ''; // Show popular products container


  // Hide the section labels for categories
  document.body.classList.add('popular-view');


  // Fetch and display all popular products
  fetchAllPopularProducts();
});




//-----------------------------------------------Organize by Category Section Functions-------------------------------------
async function fetchProducts(category = '') { 
  try {
      // Fetch products from the server, passing the selected category (empty by default)
      const response = await fetch(`get_products.php?category=${encodeURIComponent(category)}`);
      const products = await response.json();

      // Get the container where products will be displayed
      const container = document.getElementById('product-widget-container');
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
              <div class="product-price">$${parseFloat(product.product_price).toFixed(2)}</div>
            </div>
          `;
          container.innerHTML += productWidget;
      });
  } catch (error) {
        onsole.error('Error fetching products:', error);
  }
}

function redirectToProductReview(productId) {
  // Redirect to Product_Review.php with the product ID as a query parameter
  window.location.href = `ProductReview.php?product_id=${encodeURIComponent(productId)}`;
}

document.addEventListener('DOMContentLoaded', () => {
    fetchProducts(); // Fetch all products initially (without a category filter)
});

function filterByCategory(category) {
  fetchProducts(category);
}

//-----------------------------------------------Other Functions-------------------------------------
// Show the Directions Modal
function showDirectionsModal() {
    const modal = document.querySelector('.directions-modal');
    modal.style.display = 'flex'; // Use flex to ensure centering
}


// Hide the Directions Modal
function closeDirectionsModal() {
    const modal = document.querySelector('.directions-modal');
    modal.style.display = 'none'; // Hide modal
}


// Add Event Listeners
document.querySelector('.from-btn').addEventListener('click', showDirectionsModal); // Open modal
document.querySelector('.close-modal-btn').addEventListener('click', closeDirectionsModal); // Close modal


// Close modal when clicking outside the content
window.addEventListener('click', (event) => {
    const modal = document.querySelector('.directions-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});


// Ensure modal is hidden on page load
window.onload = function () {
    document.querySelector('.directions-modal').style.display = 'none';
};
    // Map initialization with coordinates
    var map = L.map('map').setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], <?php echo $zoomLevel; ?>);


    // Tile layer
    // Change the tile layer URL if you want a different design
    L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; <a href="https://www.google.com/intl/en-US_US/help/terms_maps.html">Google Maps</a>'
    }).addTo(map);


    // Marker on the map
    var marker = L.marker([<?php echo $latitude; ?>, <?php echo $longitude; ?>]).addTo(map);


    // Popup on marker click
    marker.bindPopup("<b>TAYTAY CAPITAL TIANGGE</b><br />HIGHWAY 2000, CORNER Market Rd, Taytay, 1920 Rizal");


    // Toggle function for the Price dropdown
    function togglePriceDropdown() {
        const priceDropdown = document.querySelector('.price-dropdown');
        priceDropdown.classList.toggle('show');
    }


    // Close the dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const priceDropdown = document.querySelector('.price-dropdown');
        if (!priceDropdown.contains(event.target) && !event.target.closest('.price-dropdown button')) {
            priceDropdown.classList.remove('show');
        }
    });


    // Attach click event to all product widgets
    document.addEventListener('DOMContentLoaded', function() {
        const productWidgets = document.querySelectorAll('.product-widget');


        productWidgets.forEach(widget => {
            widget.addEventListener('click', function() {
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
    document.addEventListener('click', function(event) {
        const productDropdown = document.querySelector('.product-categories');
        if (!productDropdown.contains(event.target) && !event.target.closest('.product-categories button')) {
            productDropdown.classList.remove('show');
        }
    });


    // Navbar Functions
    function toggleUserProfileMenu() {
        const menu = document.getElementById('user-profile-dropdown');
        menu.classList.toggle('show');
    }


    document.addEventListener('click', function(event) {
        const menu = document.getElementById('user-profile-dropdown');
        if (!menu.contains(event.target) && !event.target.closest('.user-profile')) {
            menu.classList.remove('show');
        }
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
</script>




</body>
</html>
