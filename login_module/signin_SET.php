<?php 
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Shop Profile</title>
    <style>
                body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        header img {
            max-height: 70px; 
            width: auto;
            margin-left: 20px; 
        }

        h1 {
            text-align: center;
            color: #732d91;
            font-size: 24px;
            margin: 20px 0;
        }

        main {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-container {
            text-align: center;
            margin-top: 100px;
            margin-bottom: 20px;
        }

        .profile-container img {
            width: 150px;
            height: 150px;
            border: 1px solid white;
            object-fit: cover;
        }

        .profile-container button {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            background-image: url(setUp.png);
            background-size: contain; /* Make the image cover the entire button */
            background-repeat: no-repeat;
            border: none;
            cursor: pointer;
        }

        .shop-name-container,
        .shop-description-container {
            font-weight: bold;
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center elements */
            margin-bottom: 30px;
        }
       
        label[for="shop-descriptionlabel"] {
            font-size: 16px;
            color: #333;
            margin-left: 85px;
            font-weight: bolder;
        }

        #shop-description {
            width: 80%;
            height: 150px;
            font-size: 16px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 50px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .shop-profile-container {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            justify-items: flex-start;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .shop-details {
            margin-bottom: 20px;
        }

        .shop-details label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .shop-details input,
        .shop-details textarea {
            width: 360px;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .cancel-button,
        .save-button {
            padding: 10px 50px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;

        }

        .cancel-button {
            background-color: #ccc;
            color: #333;
        }

        .save-button {
            background-color: #732d91;
            color: white;
            margin-left: 10px;
            margin-right: 100px;
        }

        .add-links-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .add-links-section a {
            text-decoration: none;
            font-size: 16px;
            color: #732d91;
            font-weight: bold;
        }

        #editprofimg {
            text-align: center;
        }

        #shopdesc {
            text-align: left;
        }

        #shopdesc1 {
            margin-top: 90px;
        }

        #shopdesc2 {
            margin-top: 90px;
        }

        .shop-details select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-top: 5px;
        }

        #municipality option:first-child {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <form action="signin_setup.php" method="POST">
        <header>
            <img src="images/TaytayTianggeIcon.png" alt="Taytay Marketplace Logo">
        </header>

        <h1>Set Up Shop Profile</h1>

        <main>
            <div class="profile-container">
                <input type="file" id="profile-image-input" style="display: none;">
                <button type="button" onclick="document.getElementById('profile-image-input').click()"></button>
            </div>

            <div class="shop-name-container">
                <div class="shop-name-text" id="shop-name">
                    <?php  
                    $shopName = $_SESSION['shop_name'];  
                    echo $shopName;
                    ?>
                </div>
            </div>

            <label for="shop-descriptionlabel">Shop Description:</label>
            <div class="shop-description-container">
                <textarea id="shop-description" name="shop-description"></textarea>
            </div>

            <div class="shop-profile-container">
                <div class="shop-details">
                    <label for="contact-number">Contact Number:</label>
                    <input type="tel" id="contact-number" name="contact-number">
                    <label for="shopee-link" id="shopdesc1">Shopee Link:</label>
                    <input type="url" id="shopee-link" name="shopee-link">
                </div>

                <div class="shop-details">
                    <label for="municipality" id="municipality">Municipality:</label>
                    <input type="text" id="municipality" name="municipality">

                    <label for="lazada-link" id="shopdesc2">Lazada Link:</label>
                    <input type="url" id="lazada-link" name="lazada-link">

                    
                </div>
            </div>

            <div class="button-container">
                <button type="button" class="cancel-button" onclick="window.location.href='signin_page.html';">Cancel</button>
                <button type="submit" class="save-button">Save</button>
            </div>
        </form>
    </main>

    <script>
        // Profile Image Upload and Preview
        const profileImageInput = document.getElementById('profile-image-input');
        const profilePreview = document.getElementById('profile-preview');

        profileImageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = (e) => {
                profilePreview.src = e.target.result;
            };

            reader.readAsDataURL(file);
        });

        const selectElement = document.getElementById('municipality');

        selectElement.addEventListener('change', () => {
            selectElement.options[0].style.color = '';
            selectElement.options[0].style.fontStyle = '';
        });
    </script>
</body>
</html>
