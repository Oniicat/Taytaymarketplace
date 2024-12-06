<?php
include 'dbcon.php';

try {
    $query = "SELECT seller_id, first_name, middle_name, last_name, contact_number, municipality, baranggay, shop_name, stall_number, business_permit_number, permit_image FROM shops ORDER BY created_at DESC";
    $result = $conn->query($query);

    // Fetch all results
    $sellers = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sellers[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RegisterShop</title>
    <link rel="stylesheet" href="admin-registered-shops.css">
    <style>
        .modal, .modal-overlay {
            display: none;
            position: fixed;
            z-index: 1000;
        }
        .modal {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .modal-overlay {
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .close-modal {
            float: right;
            cursor: pointer;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Shops</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Seller Name</th>
                    <th>Contact Number</th>
                    <th>Shop Name</th>
                    <th>Stall Number</th>
                    <th>Municipality</th>
                    <th>Barangay</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $index => $seller): ?>
                <tr id="row-<?= htmlspecialchars($seller['seller_id']) ?>">
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?></td>
                    <td><?= htmlspecialchars($seller['contact_number']) ?></td>
                    <td><?= htmlspecialchars($seller['shop_name']) ?></td>
                    <td><?= htmlspecialchars($seller['stall_number']) ?></td>
                    <td><?= htmlspecialchars($seller['municipality']) ?></td>
                    <td><?= htmlspecialchars($seller['baranggay']) ?></td>
                    <td>
                        <button class="view-btn" 
                            data-id="<?= htmlspecialchars($seller['seller_id']) ?>" 
                            data-name="<?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?>" 
                            data-contact="<?= htmlspecialchars($seller['contact_number']) ?>" 
                            data-shop="<?= htmlspecialchars($seller['shop_name']) ?>" 
                            data-stall="<?= htmlspecialchars($seller['stall_number']) ?>" 
                            data-municipality="<?= htmlspecialchars($seller['municipality']) ?>" 
                            data-baranggay="<?= htmlspecialchars($seller['baranggay']) ?>" 
                            data-business-permit="<?= htmlspecialchars($seller['business_permit_number']) ?>" 
                            data-permit-image="<?= htmlspecialchars($seller['permit_image']) ?>">View</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal-overlay"></div>
    <div class="modal">
        <span class="close-modal">X</span>
        <h3>Shop Profile</h3>
        <p><strong>Seller Name:</strong> <span id="modal-name"></span></p>
        <p><strong>Contact Number:</strong> <span id="modal-contact"></span></p>
        <p><strong>Shop Name:</strong> <span id="modal-shop"></span></p>
        <p><strong>Stall Number:</strong> <span id="modal-stall"></span></p>
        <p><strong>Municipality:</strong> <span id="modal-municipality"></span></p>
        <p><strong>Barangay:</strong> <span id="modal-baranggay"></span></p>
        <p><strong>Business Permit Number:</strong> <span id="modal-business-permit"></span></p>
        <p><strong>Permit Image:</strong> <a id="modal-permit-image" href="" target="_blank">View</a></p>
    </div>

    <script>
        const modal = document.querySelector('.modal');
const overlay = document.querySelector('.modal-overlay');
const closeModal = document.querySelector('.close-modal');
const modalName = document.getElementById('modal-name');
const modalContact = document.getElementById('modal-contact');
const modalShop = document.getElementById('modal-shop');
const modalStall = document.getElementById('modal-stall');
const modalMunicipality = document.getElementById('modal-municipality');
const modalBaranggay = document.getElementById('modal-baranggay');
const modalBusinessPermit = document.getElementById('modal-business-permit');
const modalPermitImage = document.getElementById('modal-permit-image');

// Close modal logic
closeModal.addEventListener('click', () => {
    modal.style.display = 'none';
    overlay.style.display = 'none';
});

overlay.addEventListener('click', () => {
    modal.style.display = 'none';
    overlay.style.display = 'none';
});

// Dynamically update table and re-attach event listeners
const sellersTable = document.querySelector('tbody');

function updateTable(sellers) {
    sellersTable.innerHTML = ''; // Clear current rows

    sellers.forEach((seller, index) => {
        const row = document.createElement('tr');
        row.id = `row-${seller.seller_id}`;

        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${seller.first_name} ${seller.last_name}</td>
            <td>${seller.contact_number}</td>
            <td>${seller.shop_name}</td>
            <td>${seller.stall_number}</td>
            <td>${seller.municipality}</td>
            <td>${seller.baranggay}</td>
            <td>
                <button class="view-btn" 
                    data-id="${seller.seller_id}" 
                    data-name="${seller.first_name} ${seller.last_name}" 
                    data-contact="${seller.contact_number}" 
                    data-shop="${seller.shop_name}" 
                    data-stall="${seller.stall_number}" 
                    data-municipality="${seller.municipality}" 
                    data-baranggay="${seller.baranggay}" 
                    data-business-permit="${seller.business_permit_number}" 
                    data-permit-image="${seller.permit_image}">View</button>
            </td>
        `;

        sellersTable.appendChild(row);
    });

    // Re-attach view button listeners
    attachViewListeners();
}

function attachViewListeners() {
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', () => {
            // Populate modal data from button attributes
            modalName.textContent = button.dataset.name;
            modalContact.textContent = button.dataset.contact;
            modalShop.textContent = button.dataset.shop;
            modalStall.textContent = button.dataset.stall;
            modalMunicipality.textContent = button.dataset.municipality;
            modalBaranggay.textContent = button.dataset.baranggay;
            modalBusinessPermit.textContent = button.dataset.businessPermit;
            modalPermitImage.href = button.dataset.permitImage;

            // Show modal
            modal.style.display = 'block';
            overlay.style.display = 'block';
        });
    });
}

// Fetch sellers periodically
function fetchSellers() {
    fetch('update-shops.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTable(data.data); // Update table and re-attach listeners
            } else {
                console.error('Failed to fetch sellers:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Fetch sellers every 5 seconds
setInterval(fetchSellers, 5000);
fetchSellers(); // Initial fetch on page load


    </script>
</body>
</html>
