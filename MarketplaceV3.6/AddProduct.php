<?php 
include '../registration-process/conn.php';

$sql = "SELECT category_name FROM tb_category"; //table for category
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
    <link rel="stylesheet" href="AddProduct.css">
    <title>Add Product</title>
    <style>
        .image-upload-container {
            margin-bottom: 20px;
        }

        .image-upload-container label {
            font-weight: bold;
        }

        .image-preview-container {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .image-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .image-preview-container img {
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-image {
            background-color: red;
            color: white;
            border: none;
            border-radius: 50%;
            padding: 5px;
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
        }
        #multiple-images-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            max-height: 200px; /* Set a max height */
            overflow-y: auto; /* Make it scrollable if it exceeds the height */
        }

    </style>
</head>
<body>
<!-- Navbar -->
<div class="custom-navbar">
    <div class="navbar-center">
        <a href="Seller_Dashboard.php">
        <img src="Content/New Logo.png" alt="Logo" class="navbar-logo">
        </a>
        <!-- Back Button -->
        <a href="Seller_Dashboard.php" class="back-btn">Back</a>
    </div>
</div>

<!-- Add Product Container -->
<div class="Add-Product-container">
    <div class="Add-Product-header">
        <h2>Add Product</h2>
    </div>
    <div class="Add-Product-container-content">
        <!-- Product Image Preview -->
        <div class="product-image-container" onclick="triggerFileInput()">
            <img id="product-image" src="" alt="Add Image" class="product-image">
            <input type="file" id="image-input" style="display: none;" accept="image/*" onchange="previewImage(event)">
        </div>

        <!-- Product Name -->
        <input type="text" class="product-name" placeholder="Enter Product Name">

        <!-- Product Price -->
        <input type="number" class="product-price" placeholder="Enter Product Price">

        <!-- Product Category (Textbox) -->
        <input type="text" id="category-textbox" class="product-category" placeholder="Choose a Category" readonly onclick="toggleCategoryList()">
        <div id="category-list" class="category-list">
            <?php foreach ($categories as $category): ?>  <!--display list of categories from category table-->
                <a href="#" onclick="selectCategory('<?php echo htmlspecialchars($category); ?>')"> <!--clickable category options-->
                    <?php echo htmlspecialchars($category); ?> 
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Product Description -->
        <textarea class="product-description" placeholder="Enter Product Description..."></textarea>

        <!-- Links Texts -->
        <div class="Links-text">Product Links</div>

        <button class="add-external-link-btn" onclick="addExternalLink()">+ Add External Link</button>

        <div class="product-links">
            <!-- Existing Link -->
            <div class="link-item">
                <label class="link-title">Shopee</label>
                <input type="url" class="product-link" value="https://shopee.com" placeholder="Enter URL" require_once>
            </div>
            <div class="link-item">
                <label class="link-title">Lazada</label>
                <input type="url" class="product-link" value="https://lazada.com" placeholder="Enter URL" required_once>
            </div>
            <!-- Newly Added Link -->
        </div>

        <!-- Multiple Images Upload -->
        <div class="image-upload-container">
            <label for="multiple-images">Upload Additional Images</label>
            <input type="file" id="multiple-images" multiple accept="image/*" onchange="previewMultipleImages(event)">
        </div>

        <!-- Display Multiple Images -->
        <div id="multiple-images-preview" class="image-preview-container"></div>
            
        <!-- Save and Cancel Buttons -->
        <div class="buttons-container">
            <a href="Seller_Dashboard.php"><button class="cancel-btn" onclick="cancelChanges()">Cancel</button></a>
            <button class="submit-btn" onclick="saveChanges()">Submit</button>
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

//------------------------------------------------------Add Function-------------------------------------------------------
async function saveChanges() {
    // Get all values from inputs/textboxes
    const productName = document.querySelector('.product-name').value.trim();
    const productPrice = document.querySelector('.product-price').value.trim();
    const productCategory = document.querySelector('.product-category').value.trim();
    const productDescription = document.querySelector('.product-description').value.trim();
    const productLinks = Array.from(document.querySelectorAll('.product-link'));
    const linkTitles = Array.from(document.querySelectorAll('.link-title')).map(label => label.textContent.trim());

    // Validate product links
    for (let i = 0; i < productLinks.length; i++) {
        if (!productLinks[i].value.trim()) { // Check if the product link is empty or whitespace
            alert(`Product link at position ${i + 1} is empty. Please fill it out.`);
            productLinks[i].focus(); // Focus the empty input field for user convenience
            return; // Stop the function execution
        }
    }

    // Prepare FormData
    const formData = new FormData();
    formData.append('name', productName);
    formData.append('price', productPrice);
    formData.append('category', productCategory);
    formData.append('description', productDescription);

    productLinks.forEach((link, index) => {
        formData.append(`link[${index}]`, link.value.trim());
    });

    linkTitles.forEach((title, index) => {
        formData.append(`linkname[${index}]`, title);
    });

    // Handle major image upload
    const majorImage = document.querySelector('#image-input').files[0];
    if (majorImage) {
        formData.append('major_image', majorImage);
    }

    // Handle multiple image uploads
    const multipleImages = document.querySelector('#multiple-images').files;
    for (let i = 0; i < multipleImages.length; i++) {
        formData.append('multiple_images[]', multipleImages[i]);
    }

    try {
        // Send data to the backend
        const response = await fetch('add_products.php', { // Fetching the insert function PHP file
            method: 'POST',
            body: formData,
        });

        const result = await response.json();

        if (result.success) {
            alert('Product added successfully!');
            window.location.href = 'Seller_Dashboard.php'; // Redirect back
        } else {
            alert(`Failed to add product: ${result.message}`);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while adding the product.');
    }
}

// Handle major image upload and preview
function triggerMajorImageInput() {
    document.getElementById('image-input').click();
}

function previewMajorImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('product-image');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

// Handle multiple images upload and preview
function previewMultipleImages(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('multiple-images-preview');
    previewContainer.innerHTML = ''; // Clear previous images

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function() {
            const imgElement = document.createElement('img');
            imgElement.src = reader.result;
            imgElement.classList.add('image-preview');
            
            // Add remove button to each image
            const removeBtn = document.createElement('button');
            removeBtn.textContent = 'X';
            removeBtn.classList.add('remove-image');
            removeBtn.onclick = function() {
                imgElement.remove();
                removeBtn.remove();
            };

            // Append the image and remove button to the container
            previewContainer.appendChild(imgElement);
            previewContainer.appendChild(removeBtn);
        };
        reader.readAsDataURL(file);
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