<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="navbar.css">
    <title>Seller Shop Dashboard</title>
<style>
/* General Styles */
body {
  margin: 0;
  font-family: Arial, sans-serif;
  overflow-x: hidden;
  background-color: #F4F4F4;
}

/* Dashboard Text */
.MyDashboard-text {
  text-align: center;
  color: #712798;
  font-size: 44px;
  margin-top: 150px;
  margin-bottom: 150px;
  font-weight: bold;
}

/* Widgets Container */
.widgets-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr); /* Three widgets per row */
  gap: 20px; /* Spacing between widgets */
  justify-content: center;
  align-items: center;
  margin: 30px auto; /* Center the grid container */
  max-width: 800px; /* Optional: Limit the width of the container */
}

/* Individual Widget Styles */
.widget-AddShop {
  background-color: #712798;
  width: 250px;
  height: 150px;
  border-radius: 8px;
  border: 2px solid gray; /* Added white outline */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  font-size: 1.2rem;
  font-weight: bold;
  color: white;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.3s ease, border-color 0.3s ease;
  overflow: hidden; /* Ensures text stays within bounds */
  text-overflow: ellipsis; /* Adds "..." to overflowing text */s
  -webkit-line-clamp: 3; /* Limits description to 3 lines */
}

/* Hover Effect */
.widget-AddShop:hover {
  background-color: gray;
  border-color: #ffffff; /* Retain the white outline on hover */
  transform: scale(1.00);
}


/* Individual Widget Styles */
.widget {
  width: 250px;
  height: 150px;
  border-radius: 8px;
  border: 2px solid #712798; /* Added outline */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  font-size: 1.2rem;
  font-weight: bold;
  color:  #712798;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.3s ease, border-color 0.3s ease;
  background-color: white; /* Added background color */
  overflow: hidden; /* Ensures text stays within bounds */
  text-overflow: ellipsis; /* Adds "..." to overflowing text */
  -webkit-line-clamp: 3; /* Limits description to 3 lines */
}

/* Hover Effect */
.widget:hover {
  sbackground-color: #f7f7f7;
  color: #6e38d8;
  border-color: #6e38d8;
  transform: scale(1.05);
}

.widget-link {
  text-decoration: none; 
  display: inline-block; 
}

/* Margin Modifier for Widgets Container */
.widgets-container-margin {
  margin: 50px auto;
}

.logout-btn {
  position: absolute;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
  color: white;
  background-color: #712798;
  transition: background-color 0.3s ease;
  white-space: nowrap;
  margin-left: 1010px;
  z-index: 10;
}

.logout-btn:hover {
  background-color: red;
}

.profile-container-seller {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  margin-left: 102%;
  margin-right: -80px;
  transition: transform 0.3s ease;
}

.user-profile-container {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-left: auto;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

</style>
</head>
<body>
<div class="custom-navbar">
    <div class="navbar-center">
        <a href="MarketPlace(Seller).php">
        <img src="Content/New Logo.png" alt="Logo" class="navbar-logo">
        </a> 
        <!-- Logout Button -->
        <button class="logout-btn">Log Out</button>
            <div class="profile-container-seller" onclick="toggleUserProfileMenu()">
                <a href="UserProfile.php">
                    <img src="Content/RenzPogi.png" alt="User Avatar" class="user-avatar">
                </a>
            </div>
        </div>
    </div>
</div>

    <!-- My Dashboard Text -->
    <div class="MyDashboard-text">My Dashboard</div>

<!-- Widgets Container -->
<div class="widgets-container widgets-container-margin">
    <a href="add-shop.php" class="widget-link">
        <div class="widget-AddShop">Add Shop</div>
    </a>
    <a href="Seller Dashboard.php" class="widget-link">
        <div class="widget">Shap ni RenzPogi</div>
    </a>
    <a href="shop3.html" class="widget-link">
        <div class="widget">Shop 3</div>
    </a>
    <a href="shop4.html" class="widget-link">
        <div class="widget">Shop 4</div>
    </a>
    <a href="shop5.html" class="widget-link">
        <div class="widget">Shop 5</div>
    </a>
</div>

</body>
</html>