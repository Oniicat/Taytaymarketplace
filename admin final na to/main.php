<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taytaymarketplace</title>
    <link rel="stylesheet" href="sidebarstyle.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="index.php">
            <img src="logo.png" alt="Logo" class="logo">
        </a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="nav-links">
            <li><a href="main.php?page=dashboard">Dashboard</a></li>
            <li><a href="main.php?page=admin-registration">Seller Registration</a></li>
            <li><a href="main.php?page=admin-registered-shops">Shops</a></li>
            <li><a href="main.php?page=accounts">Accounts</a></li>
            <li class="dropdown">
                <a href="" class="dropdown-toggle">
                    Reports <span class="arrow"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="main.php?page=products_report">Products</a></li>
                    <li><a href="main.php?page=users_report">Users</a></li>
                    
                </ul>
            </li>
            <li><a href="main.php?page=activity_log">Activity Log</a></li>
            <li class="dropdown">
                <a href="#settings" class="dropdown-toggle">
                    Settings <span class="arrow"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="main.php?page=category">Add Category</a></li>
                    <li><a href="main.php?page=legaladmin">Legal Documents</a></li>
                    <li><a href="main.php?page=websitetextadmin">Website</a></li>
                </ul>
            </li>
            <li><a href="main.php?page=backup and restore">Backup and Restore</a></li>
        </ul>
    </div>

    <div class="main-content">
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
        $allowed_pages = ['accounts', 'dashboard', 'admin-registered-shops', 'products_report', 'settings', 'users_report', 'products', 'activity_log', 'users', 'category', 'websitetextadmin', 'legaladmin', 'archive', 'backup and restore', 'admin-registration'];//name ng mga php file 
        if (in_array($page, $allowed_pages)) {
            include("$page.php");
        } else {
            echo "<p>Page not found.</p>";
        }
        ?>
    </div>

<script>
    // looping ng drop down bale checker yan
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', (e) => {
    e.preventDefault();
    const dropdownMenu = toggle.nextElementSibling;
        const arrow = toggle.querySelector('.arrow');

        // pang check lng ng maxmimun height para sa animation
        if (dropdownMenu.style.maxHeight) {
            dropdownMenu.style.maxHeight = null; // Close
        } else {
            dropdownMenu.style.maxHeight = dropdownMenu.scrollHeight + "px"; // Open
        }

        // Rotate arrow
        arrow.classList.toggle('open'); //animation lng ng arrow
    });
});
</script>
</body>
</html>
