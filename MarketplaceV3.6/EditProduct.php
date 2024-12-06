<?php
include '../registration-process/conn.php';

if (isset($_GET['product_id'])) { // Display product based on product ID
    $product_id = intval($_GET['product_id']); 

    
    // Fetch product details
    $sql = "SELECT * FROM tb_products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
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
    <link rel="stylesheet" href="EditProduct.css">
    <title>Edit Product</title>
</head>
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

<!-- Edit Product Container -->
<div class="Edit-Product-container">
    <div class="Edit-Product-header">
        <h2>Edit Product</h2>
    </div>
    <div class="Edit-Product-container-content">
         <!-- Hidden field for product ID -->
         <input type="hidden" id= "product-id" value= "<?php echo htmlspecialchars($product['product_id']); ?>">

        <!-- Product Image Preview -->
        <div class="product-image-container" onclick="triggerFileInput()">
            <img id="product-image" src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="Product Preview" class="product-image">
            <input type="file" id="image-input" style="display: none;" accept="image/*" onchange="previewImage(event)">
        </div>

        <!-- Product Name -->
        <input type="text" class="product-name" placeholder="Enter Product Name" value= "<?php echo htmlspecialchars($product['product_name']); ?>">

        <!-- Product Price -->
        <input type="number" class="product-price" placeholder="Enter Product Price" value= "<?php echo htmlspecialchars($product['product_price']); ?>">

        <!-- Product Category (Textbox) -->
        <input type="text" id="category-textbox" class="product-category" placeholder="Choose a Category" readonly onclick="toggleCategoryList()"
        value= "<?php echo htmlspecialchars($product['category']); ?>">
        <div id="category-list" class="category-list">
                <?php foreach ($categories as $category): ?>    <!-- from category table-->
                  <a href="#" onclick="selectCategory('<?php echo htmlspecialchars($category); ?>')">
                      <?php echo htmlspecialchars($category); ?>
                  </a>
              <?php endforeach; ?>
        </div>

        <!-- Product Description -->
        <textarea class="product-description" placeholder="Enter Product Description..."><?php echo htmlspecialchars($product['product_desc']); ?></textarea>

        <!-- Links Texts -->
        <div class="Links-text">Product Links</div>

        <button class="add-external-link-btn" onclick="addExternalLink()">+ Add External Link</button>

        <div class="product-links">
            <!-- Loop through PHP array and generate links dynamically -->
            <?php foreach ($product_links as $link): ?>
                <div class="link-item">
                    <label class="link-title"><?php echo htmlspecialchars($link['link_name']); ?></label>
                    <input type="url" class="product-link" value="<?php echo htmlspecialchars($link['link']); ?>" placeholder="Enter URL" required>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Newly Added Link -->

        
        <!-- Delete Links Button -->
        <button class="delete-links-btn" onclick="deleteAllLinks()">Delete All Links</button>

        <!-- Save, Cancel, and Delete Buttons -->
        <div class="buttons-container">
        <button class="delete-btn" onclick="deleteProduct()">Delete Product</button>
        <a href="Seller_Dashboard.php"><button class="cancel-btn" onclick="cancelChanges()">Cancel</button></a>
        <button class="submit-btn" onclick="saveChanges()">Save</button>
        </div>

    </div>
</div>

<!-- Modal Structure -->
<div class="modal-overlay" id="addLinkModal">
    <div class="modal">
        <h2>Add External Link</h2>
        <input type="text" id="linkTitle" placeholder="Title">
        <input type="url" id="linkUrl" placeholder="URL">
        <div class="modal-buttons">
            <button class="cancel-btn" onclick="closeModal()">Cancel</button>
            <button class="add-btn" onclick="addLinkToProduct()">Add</button>
        </div>
    </div>
</div>

<!-------------------------------------------------------JS Functions--------------------------------------------------------------------------->
<script>
    //------------------------------------------------------Update Function-------------------------------------------------------

    async function saveChanges() {
    const formData = new FormData(); //retrieving data from textboxes
    formData.append('product_id', document.getElementById('product-id').value);
    formData.append('image', document.getElementById('image-input').files[0]);
    formData.append('name', document.querySelector('.product-name').value);
    formData.append('price', document.querySelector('.product-price').value);
    formData.append('description', document.querySelector('.product-description').value);
    formData.append('category', document.querySelector('.product-category').value);

    const productLinks = Array.from(document.querySelectorAll('.product-link'));
    const linkTitles = Array.from(document.querySelectorAll('.link-title')).map(label => label.textContent.trim());

    productLinks.forEach((link, index) => {
    formData.append(`link[${index}]`, link.value); // Use an indexed key for each link
    });

    linkTitles.forEach((title, index) => {
    formData.append(`linkname[${index}]`, title); // Use the title text
    });

    // You can use AJAX to send the form data to the server for saving
    fetch('update_products.php', { //calling out update function php
        method: 'POST',
        body: formData
    }).then(response => response.json())
      .then(data => {
        if (data.success) {
            alert("Changes saved!");
            window.location.href = 'Seller_Dashboard.php'; //redirect back
        } else {
            alert("Error saving changes.");
        }
    }).catch(error => {
        console.error("Error:", error);
        alert("An error occurred while saving the changes.");
    });
}

function deleteAllLinks() {
    const confirmation = confirm('Are you sure you want to delete all product links?');
    if (confirmation) {
        const productLinks = document.querySelector('.product-links');
        productLinks.innerHTML = ''; // Clears all the links
        alert('All product links have been deleted.');
    }
}

//------------------------------------------------------Delete Function-------------------------------------------------------

function deleteProduct(productId) {
    const confirmation = confirm('Are you sure you want to delete this product?');
    if (!confirmation) return;

    const formData = new FormData();
    formData.append('product_id', document.getElementById('product-id').value);

    // AJAX request to delete the product
    fetch('delete_product.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product deleted successfully!');
                window.location.href = 'Seller_Dashboard.php'; // Redirect after deletion
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the product.');
        });
}
/**
 * Open the modal popup for adding an external link.
 */
function addExternalLink() {
    const modal = document.getElementById('addLinkModal');
    modal.style.display = 'flex'; // Show the modal
}

/*** Close the modal popup.*/
function closeModal() {
    const modal = document.getElementById('addLinkModal');
    modal.style.display = 'none'; // Hide the modal
    document.getElementById('linkTitle').value = ''; // Clear the title input
    document.getElementById('linkUrl').value = ''; // Clear the URL input
}

/*** Add a new link to the product links section in a 2x2 format.*/
function addLinkToProduct() {
    const title = document.getElementById('linkTitle').value.trim();
    const url = document.getElementById('linkUrl').value.trim();

    if (!title || !url) {
        alert('Please fill in both fields.');
        return;
    }

    // Create a container for the new link
    const linkContainer = document.createElement('div');
    linkContainer.classList.add('link-item');

    // Create the title label
    const titleLabel = document.createElement('label');
    titleLabel.textContent = title;
    titleLabel.classList.add('link-title');

    // Create the URL input box
    const urlInput = document.createElement('input');
    urlInput.type = 'url';
    urlInput.classList.add('product-link');
    urlInput.value = url;
    urlInput.placeholder = 'Enter URL';

    // Append the title and input to the container
    linkContainer.appendChild(titleLabel);
    linkContainer.appendChild(urlInput);

    // Append the new link container to the product-links section
    document.querySelector('.product-links').appendChild(linkContainer);

    // Close the modal
    closeModal();
}

// Toggle the visibility of the category list
function toggleCategoryList() {
    const categoryList = document.getElementById('category-list');
    categoryList.classList.toggle('show');
}

// Select a category and update the textbox value
function selectCategory(category) {
    document.getElementById('category-textbox').value = category;
    document.getElementById('category-list').classList.remove('show'); // Hide the list after selection
}


// Close the category list if clicking outside
document.addEventListener('click', function(event) {
    const categoryTextbox = document.getElementById('category-textbox');
    const categoryList = document.getElementById('category-list');
    if (!categoryTextbox.contains(event.target) && !categoryList.contains(event.target)) {
        categoryList.classList.remove('show'); // Hide the list if clicking outside
    }
});

// Function to toggle the category dropdown visibility
function toggleDropdown() {
    const dropdown = document.querySelector('.dropdown');
    dropdown.classList.toggle('show');
  }

  // Close category dropdown when clicking outside of it
  document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.dropdown');
    if (!dropdown.contains(event.target)) {
      dropdown.classList.remove('show');
    }
  });

  // Profile DropDown Toggle
  function toggleUserProfileMenu() {
    const menu = document.getElementById('user-profile-dropdown');
    menu.classList.toggle('show');
  }

  // Close profile dropdown when clicking outside of it
  document.addEventListener('click', function(event) {
    const menu = document.getElementById('user-profile-dropdown');
    if (!menu.contains(event.target) && !event.target.closest('.user-profile')) {
      menu.classList.remove('show');
    }
  });

   // Trigger file input when clicking on the image container
function triggerFileInput() {
    document.getElementById('image-input').click();
}

// Image preview function
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('product-image');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

</script>
</body>
</html>
