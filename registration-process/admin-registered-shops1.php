<?php
include 'conn.php';


$query = "
    SELECT si.seller_id, si.shop_name, si.stall_number, 
           si.business_permit_number, si.permit_image, si.shop_profile_pic, si.contact_number, si.shop_description, si.lazada_link, 
           si.shopee_link, si.created_at, s.first_name, s.last_name
    FROM registered_shops si
    JOIN users s ON si.seller_id = s.seller_id
";

$sellers = [];
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $sellers[] = $row;
    }
} else {
    echo "Error: " . $conn->error;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Shops</title>
    <link rel="stylesheet" href="admin-registration.css">
</head>
<body>
    <div class="container">
        <h2>Registered Shops</h2>
        <table border="1" id="registration-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Shop Name</th>
                    <th>Seller Name</th>
                    <th>Stall Number</th>
                    <th>Business Permit Number</th>
                    <th>Contact Number</th>
                    <th>Shop Description</th>
                    <th>Lazada Link</th>
                    <th>Shopee Link</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $index => $seller): ?>
                <tr id="row-<?= htmlspecialchars($seller['seller_id']) ?>">
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($seller['shop_name']) ?></td>
                    <td><?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?></td>
                    <td><?= htmlspecialchars($seller['stall_number']) ?></td>
                    <td><?= htmlspecialchars($seller['business_permit_number']) ?></td>
                    <td><?= htmlspecialchars($seller['contact_number']) ?></td>
                    <td><?= htmlspecialchars($seller['shop_description']) ?></td>
                    <td><a href="<?= htmlspecialchars($seller['lazada_link']) ?>" target="_blank">Lazada</a></td>
                    <td><a href="<?= htmlspecialchars($seller['shopee_link']) ?>" target="_blank">Shopee</a></td>
                    <td><?= htmlspecialchars($seller['created_at']) ?></td>
                    <td>
                        <button class="view-btn" 
                            data-id="<?= htmlspecialchars($seller['seller_id']) ?>" 
                            data-shop="<?= htmlspecialchars($seller['shop_name']) ?>" 
                            data-name="<?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?>" 
                            data-stall="<?= htmlspecialchars($seller['stall_number']) ?>" 
                            data-business-permit="<?= htmlspecialchars($seller['business_permit_number']) ?>" 
                            data-contact="<?= htmlspecialchars($seller['contact_number']) ?>" 
                            data-description="<?= htmlspecialchars($seller['shop_description']) ?>" 
                            data-lazada-link="<?= htmlspecialchars($seller['lazada_link']) ?>" 
                            data-shopee-link="<?= htmlspecialchars($seller['shopee_link']) ?>" 
                            data-permit-image="<?= htmlspecialchars($seller['permit_image']) ?>" 
                            data-profile-image="<?= htmlspecialchars($seller['shop_profile_pic']) ?>">View More</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <!-- Modal -->
<div class="modal-overlay" style="display: none;"></div>
<div class="modal" style="display: none;">
    <span class="close-modal">X</span>
    <h3>Shop Profile</h3>
    <p><strong>Shop Name:</strong> <span id="modal-shop"></span></p>
    <p><strong>Seller Name:</strong> <span id="modal-name"></span></p>
    <p><strong>Stall Number:</strong> <span id="modal-stall"></span></p>
    <p><strong>Business Permit Number:</strong> <span id="modal-business-permit"></span></p>
    <p><strong>Contact Number:</strong> <span id="modal-contact"></span></p>
    <p><strong>Shop Description:</strong> <span id="modal-description"></span></p>
    <p><strong>Lazada Link:</strong> <span id="modal-lazada-link"></span></p>
    <p><strong>Shopee Link:</strong> <span id="modal-shopee-link"></span></p>
    <p><strong>Permit Image:</strong> <a id="modal-permit-image" href="" target="_blank">View Image</a></p>
    <p><strong>Shop Profile Image:</strong> <a id="modal-profile-image" href="" target="_blank">View Image</a></p>

    <script>
const modal = document.querySelector('.modal');
        const overlay = document.querySelector('.modal-overlay');
        const closeModalBtn = document.querySelector('.close-modal');

        const modalShop = document.getElementById('modal-shop');
        const modalName = document.getElementById('modal-name');
        const modalStall = document.getElementById('modal-stall');
        const modalBusinessPermit = document.getElementById('modal-business-permit');
        const modalContact = document.getElementById('modal-contact');
        const modalDescription = document.getElementById('modal-description');
        const modalLazadaLink = document.getElementById('modal-lazada-link');
        const modalShopeeLink = document.getElementById('modal-shopee-link');
        const modalPermitImage = document.getElementById('modal-permit-image');
        const modalProfileImage = document.getElementById('modal-profile-image');

        document.addEventListener('click', (event) => {
        if (event.target.classList.contains('view-btn')) {
        const button = event.target;

        // Fill modal fields
        modalShop.textContent = button.dataset.shop;
        modalName.textContent = button.dataset.name;
        modalStall.textContent = button.dataset.stall;
        modalBusinessPermit.textContent = button.dataset.businessPermit;
        modalContact.textContent = button.dataset.contact;
        modalDescription.textContent = button.dataset.description;

        // Update Lazada Link
        modalLazadaLink.href = button.dataset.lazadaLink;
        modalLazadaLink.textContent = button.dataset.lazadaLink || 'No Lazada link available';

        // Update Shopee Link
        modalShopeeLink.href = button.dataset.shopeeLink;
        modalShopeeLink.textContent = button.dataset.shopeeLink || 'No Shopee link available';

        modalPermitImage.href = button.dataset.permitImage;
        modalProfileImage.href = button.dataset.profileImage;

        // Show modal
        modal.style.display = 'block';
        overlay.style.display = 'block';
    }
});

closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            overlay.style.display = 'none';
        });

        overlay.addEventListener('click', () => {
            modal.style.display = 'none';
            overlay.style.display = 'none';
        });




//------------------------------------------------------------------------------------------------

// Automatically refresh the table every 10 seconds
setInterval(fetchSellers, 10000);


// Fetch and update table rows
function fetchSellers() {
    fetch('update-shops.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTable(data.data);
            } else {
                console.error('Failed to fetch sellers:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Update the table with fetched data
function updateTable(sellers) {
    const tbody = registrationTable.querySelector('tbody');
    tbody.innerHTML = ''; // Clear existing rows

    sellers.forEach((seller, index) => {
        const row = `
            <tr id="row-${seller.seller_id}">
                <td>${index + 1}</td>
                <td>${seller.shop_name}</td>
                <td>${seller.first_name} ${seller.last_name}</td>
                <td>${seller.stall_number}</td>
                <td>${seller.business_permit_number}</td>
                <td>${seller.contact_number}</td>
                <td>${seller.shop_description}</td>
                <td>${seller.lazada_link}</td>
                <td>${seller.shopee_link}</td>
                <td>${seller.created_at}</td>
                <td>
                    <button class="view-btn" 
                        data-id="${seller.shop_name}" 
                        data-name="${seller.first_name} ${seller.last_name}" 
                        data-contact="${seller.stall_number}" 
                        data-shop="${seller.business_permit_number}" 
                        data-stall="${seller.contact_number}" 
                        data-municipality="${seller.shop_description}" 
                        data-baranggay="${seller.lazada_link}"
                        data-business-permit="${seller.shopee_link}">View More</button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}


// Initial fetch on page load
fetchSellers();
    </script>
</body>
</html>
