<?php
session_start();

include "../registration-process/conn.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // kung si user ba ay logged in
    if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
        $userEmail = $_SESSION['user_email'];
        $shopID = $_SESSION['shop_id'];

        //kunin mga laman nung nsa loob ng form
        $textarea = isset($_POST['shop-description']) ? $_POST['shop-description'] : "";
        $contact_number = $_POST['contact-number'] ?? "";
        $shopee_link = isset($_POST['shopee-link']) ? $_POST['shopee-link'] : '';
        $lazada_link = isset($_POST['lazada-link']) ? $_POST['lazada-link'] : '';
        $municipality = $_POST['municipality'];

        // insert sa table ng mga input
        $sql = "INSERT INTO profiles (shop_id, user_email, shop_description, contact_number, shopee_link, lazada_link, municipality) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ississs",  $shopID, $userEmail, $textarea, $contact_number, $shopee_link, $lazada_link, $municipality);

        if ($stmt->execute()) {
            // next page
           header("Location: ../MarketplaceV3.6/Seller_Dashboard.php");
            exit();
        } else {
           
            echo "<h2>Sign-up failed: " . $conn->error . "</h2>";
        }

        $stmt->close();
        $conn->close();
    } else {
        // next page
        header("Location: login.php");
        exit();
    }
}