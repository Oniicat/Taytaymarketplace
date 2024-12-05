<?php
session_start();

function getDatabaseConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mini_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // kung si user ba ay logged in
    if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
        $userEmail = $_SESSION['user_email'];

        //kunin mga laman nung nsa loob ng form
        $textarea = isset($_POST['shop-description']) ? $_POST['shop-description'] : "";
$contact_number = $_POST['contact-number'] ?? "";
$shopee_link = isset($_POST['shopee-link']) ? $_POST['shopee-link'] : '';
$lazada_link = isset($_POST['lazada-link']) ? $_POST['lazada-link'] : '';
        $municipality = $_POST['municipality'];

        // insert sa table ng mga input
        $conn = getDatabaseConnection();
        $sql = "INSERT INTO profiles (user_email, shop_description, contact_number, shopee_link, lazada_link, municipality) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisss", $userEmail, $textarea, $contact_number, $shopee_link, $lazada_link, $municipality);

        if ($stmt->execute()) {
         
            echo "<h2>Setup Finished!</h2>";
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