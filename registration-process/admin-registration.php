<?php
include 'conn.php';

// Fetch pending sellers
$query = "
    SELECT si.seller_id, si.first_name, si.last_name, si.contact_number, si.shop_name, 
           si.stall_number, si.municipality, si.baranggay, si.business_permit_number, 
           si.permit_image, si.status 
    FROM registration si
    JOIN users s ON si.seller_id = s.seller_id
    WHERE si.status = 'pending'
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
    <title>Pending Shops</title>
    <link rel="stylesheet" href="admin-registration.css">
</head>
<body>
    <div class="container">
        <h2>Registrations</h2>
        <table border="1" id="registration-table">
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
                            data-permit-image="<?= htmlspecialchars($seller['permit_image']) ?>">View More</button>
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
        <p><strong>Seller Name:</strong> <span id="modal-name"></span></p>
        <p><strong>Contact Number:</strong> <span id="modal-contact"></span></p>
        <p><strong>Shop Name:</strong> <span id="modal-shop"></span></p>
        <p><strong>Stall Number:</strong> <span id="modal-stall"></span></p>
        <p><strong>Municipality:</strong> <span id="modal-municipality"></span></p>
        <p><strong>Barangay:</strong> <span id="modal-baranggay"></span></p>
        <p><strong>Business Permit Number:</strong> <span id="modal-business-permit"></span></p>
        <p><strong>Permit Image:</strong> <a id="modal-permit-image" href="" target="_blank">View Image</a></p>

        <form id="action-form">
            <input type="hidden" id="modal-id">
            <button type="button" id="approve-btn">Approve</button>
            <button type="button" id="decline-btn">Decline</button>
        </form>
    </div>

    <script>
const registrationTable = document.getElementById('registration-table');
const modal = document.querySelector('.modal');
const overlay = document.querySelector('.modal-overlay');
const modalId = document.getElementById('modal-id');
const modalName = document.getElementById('modal-name');
const modalContact = document.getElementById('modal-contact');
const modalShop = document.getElementById('modal-shop');
const modalStall = document.getElementById('modal-stall');
const modalMunicipality = document.getElementById('modal-municipality');
const modalBaranggay = document.getElementById('modal-baranggay');
const modalBusinessPermit = document.getElementById('modal-business-permit');
const modalPermitImage = document.getElementById('modal-permit-image');

// Fetch and update table rows
function fetchSellers() {
    fetch('update-registration.php')
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
                        data-permit-image="${seller.permit_image}">View More</button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Modal and action listeners
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('view-btn')) {
        const button = event.target;
        modalId.value = button.dataset.id;
        modalName.textContent = button.dataset.name;
        modalContact.textContent = button.dataset.contact;
        modalShop.textContent = button.dataset.shop;
        modalStall.textContent = button.dataset.stall;
        modalMunicipality.textContent = button.dataset.municipality;
        modalBaranggay.textContent = button.dataset.baranggay;
        modalBusinessPermit.textContent = button.dataset.businessPermit;
        modalPermitImage.href = '' + button.dataset.permitImage;

        modal.style.display = 'block';
        overlay.style.display = 'block';
    }
});

document.querySelector('.close-modal').addEventListener('click', () => {
    modal.style.display = 'none';
    overlay.style.display = 'none';
});

document.getElementById('approve-btn').addEventListener('click', () => {
    const sellerId = modalId.value;

    fetch('send-activation-email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ seller_id: sellerId, action: 'approve' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Seller approved successfully!');
            // Remove the seller's row from the table
            const row = document.getElementById(`row-${sellerId}`);
            if (row) row.remove();
        } else {
            alert('Error approving seller: ' + data.message);
        }
        // Close the modal
        modal.style.display = 'none';
        overlay.style.display = 'none';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while approving the seller.');
    });
});

document.getElementById('decline-btn').addEventListener('click', () => {
    const sellerId = modalId.value;

    fetch('send-activation-email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ seller_id: sellerId, action: 'decline' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Seller declined successfully!');
            // Remove the seller's row from the table
            const row = document.getElementById(`row-${sellerId}`);
            if (row) row.remove();
        } else {
            alert('Error declining seller: ' + data.message);
        }
        // Close the modal
        modal.style.display = 'none';
        overlay.style.display = 'none';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while declining the seller.');
    });
});

// Automatically refresh the table every 10 seconds
setInterval(fetchSellers, 10000);

// Initial fetch on page load
fetchSellers();
    </script>
</body>
</html>
