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
  display: flex;
  flex-wrap: nowrap;
  justify-content: center;
  align-items: center;
  margin: 30px auto;
  overflow-x: auto;
  padding: 10px;
  max-width: 100%;
}

.widget {
  margin: 0 20px; /* Adds horizontal spacing (half of gap) */
}


@media (min-width: 768px) {
  .widgets-container {
    grid-template-columns: repeat(3, 1fr); /* Switch to grid with 3 columns on larger screens */
  }
}


/* Individual Widget Styles */
.widget-AddShop {
  background-color: #712798;
  width: 250px;
  height: 150px;
  margin-right: 20px;
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
  margin: 0 10px;
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
  background-color: #f7f7f7;
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
  right: 5%;
  top: 3%;
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

        <a href="logout.php">
          <button class="logout-btn">Log Out</button>
        </a>



        <!-- Logout Button -->
      
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

    <!-- dapat di pa lalabas dito hanggat di pa na aapprove ni admin -->

    <div id="shopContainer">
    <!-- Shops will be dynamically added here -->
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const shopContainer = document.getElementById("shopContainer");

    // Fetch shops for the logged-in seller
    fetch("get_shops.php")
        .then(response => response.json())
        .then(data => {
            if (data.success && data.shops.length > 0) {
                // Loop through shops and create widgets
              data.shops.forEach(shop => {
    const shopLink = document.createElement("a");
    shopLink.href = `../MarketplaceV3.6/Seller_Dashboard.php?shop_id=${shop.shop_id}&seller_id=${data.seller_id}`;
    shopLink.className = "widget-link";

    const shopDiv = document.createElement("div");
    shopDiv.className = "widget";
    shopDiv.textContent = shop.shop_name;

    shopLink.addEventListener("click", (e) => {
        e.preventDefault(); // Prevent default navigation

        // Send the seller_id and shop_id to the session using AJAX
        fetch("save_session_data.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                shop_id: shop.shop_id,
                seller_id: data.seller_id
            }),
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // After successful session save, redirect to the dashboard
                    window.location.href = shopLink.href;
                } else {
                    console.error("Failed to save session data:", result.message);
                }
            })
            .catch(error => console.error("Error saving session data:", error));
    });

    shopLink.appendChild(shopDiv);
    shopContainer.appendChild(shopLink);
});


                // Ensure the container is visible
                shopContainer.style.display = "block";
            } else {
                console.error("No shops found for this seller:", data.message);
                shopContainer.style.display = "none"; // Hide the container if no shops
            }
        })
        .catch(error => {
            console.error("Error fetching shops:", error);
            shopContainer.innerHTML = "<p>An error occurred while loading shops.</p>";
            shopContainer.style.display = "none"; // Hide the container on error
        });
});



</script>

</body>
</html>