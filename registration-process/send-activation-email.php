<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'conn.php';

header('Content-Type: application/json');


$data = json_decode(file_get_contents('php://input'), true);
$seller_id = $data['seller_id'];
$action = $data['action'];

try {
    $stmt = $conn->prepare("SELECT email FROM users WHERE seller_id = ?");
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seller = $result->fetch_assoc();

    if (!$seller) {
        echo json_encode(['success' => false, 'message' => 'Seller not found.']);
        exit;
    }

    if ($action === 'approve') {
        // Approve the seller's account
        $stmt = $conn->prepare("UPDATE registration SET status = 'approved' WHERE seller_id = ?");
        $stmt->bind_param("i", $seller_id);
        $stmt->execute();

        // Insert the seller's data into the shops table
        $shopStmt = $conn->prepare("
        INSERT INTO shops (seller_id, first_name, middle_name, last_name, contact_number, municipality, baranggay, shop_name, stall_number, business_permit_number, permit_image, created_at)
        SELECT seller_id, first_name, middle_name, last_name, contact_number, municipality, baranggay, shop_name, stall_number, business_permit_number, permit_image, NOW()
        FROM registration
        WHERE seller_id = ?");
        $shopStmt->bind_param("i", $seller_id);
        $shopStmt->execute();

        // Delete the seller's record from the registration table
        $deleteStmt = $conn->prepare("DELETE FROM registration WHERE seller_id = ?");
        $deleteStmt->bind_param("i", $seller_id);
        $deleteStmt->execute();

        // Send approval email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.sendgrid.net';
        $mail->SMTPAuth = true;
        $mail->Username = 'apikey';
        $mail->Password = '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';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('lacandiliangelod@gmail.com', 'Admin');
        $mail->addAddress($seller['email']);
        $mail->isHTML(true);
        $mail->Subject = 'Account Approved - Taytay Marketplace';

        // Customized Email Design
        $mail->Body = '
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-header img {
        max-width: 150px;
        margin-bottom: 10px;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header h1 {
            color: #712798;
            font-size: 24px;
            margin: 0 0 20px 0;
            text-align: left;
        }
        .email-content {
            font-size: 16px;
            color: #555555;
            line-height: 1.6;
            text-align: left;
        }
        .email-content a {
            color: #712798;
            text-decoration: none;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: left;
            font-size: 12px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="email-container">
     <div class="email-header">
            <img src="Admin-approval/logo.png" alt="Taytay Marketplace Logo">
        <div class="email-header">
            <h1>Account Approved</h1>
        </div>
        <div class="email-content">
            <p>Dear Seller,</p>
            <p>Your account has been successfully approved! You can now log in and start using your account.</p>
            <p><a href="http://localhost/Admin-approval/seller-sign-up.php">Login here</a> to access your account.</p>
        </div>
        <div class="footer">
            <p>&copy; 2024 Taytay Marketplace. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';

        $mail->send();

        echo json_encode(['success' => true, 'message' => 'Account approved and email sent.']);

    } elseif ($action === 'decline') {
        // Delete the seller's record from the registration table
        $deleteStmt = $conn->prepare("DELETE FROM registration WHERE seller_id = ?");
        $deleteStmt->bind_param("i", $seller_id);
        $deleteStmt->execute();

        // Send email to the seller notifying them that their account is declined
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.sendgrid.net';
        $mail->SMTPAuth = true;
        $mail->Username = 'apikey';
        $mail->Password = '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';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('lacandiliangelod@gmail.com', 'Admin');
        $mail->addAddress($seller['email']);
        $mail->isHTML(true);
        $mail->Subject = 'Account Declined - Taytay Marketplace';

        // Customized Email Design
        $mail->Body = '
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Declined</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-header img {
        max-width: 150px;
        margin-bottom: 10px;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header h1 {
            color: #e74c3c;
            font-size: 24px;
            margin: 0 0 20px 0;
            text-align: left;
        }
        .email-content {
            font-size: 16px;
            color: #555555;
            line-height: 1.6;
            text-align: left;
        }
        .email-content a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: left;
            font-size: 12px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
        <div class="email-header">
            <img src="Admin-approval/logo.png" alt="Taytay Marketplace Logo">
            <h1>Account Declined</h1>
        </div>
        <div class="email-content">
            <p>Dear Seller,</p>
            <p>Your account has been declined. Please resubmit your shop information for further review.</p>
            <p><a href="http://localhost/Admin-approval/seller-sign-up.php">Click here</a> to resubmit your shop details.</p>
        </div>
        <div class="footer">
            <p>&copy; 2024 Taytay Marketplace. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';

        $mail->send();

        echo json_encode(['success' => true, 'message' => 'Account declined and email sent.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close the mysqli connection
$conn->close();
?>
