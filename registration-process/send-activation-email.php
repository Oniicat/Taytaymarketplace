<?php
use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'conn.php';

header('Content-Type: application/json');


// Read the JSON input
$input = json_decode(file_get_contents("php://input"), true);
$action = $input['action'] ?? null;
$seller_id = $input['seller_id'] ?? null;
$decline_reason = $input['reason'] ?? null;
$shop_name = $input['shop_name'] ?? null;



try {
    $stmt = $conn->prepare("SELECT email FROM accounts WHERE seller_id = ?");
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seller = $result->fetch_assoc();

    if (!$seller) {
        echo json_encode(['success' => false, 'message' => 'Seller not found.']);
        exit;
    }

    if ($action === 'approve') {
        
        // Insert the seller's data into the shops table
        $shopStmt = $conn->prepare("INSERT INTO registered_shops (seller_id, shop_name, stall_number, 
           business_permit_number, permit_image, shop_profile_pic, contact_number, shop_description, lazada_link, 
           shopee_link, created_at)
        SELECT seller_id, shop_name, stall_number, business_permit_number, permit_image, 
               shop_profile_pic, contact_number, shop_description, lazada_link, shopee_link, NOW()
        FROM registration
        WHERE seller_id = ?;
");
        $shopStmt->bind_param("i", $seller_id);
        $shopStmt->execute();

        // Delete the seller's record from the registration table
        $deleteStmt = $conn->prepare("DELETE FROM registration WHERE seller_id = ?");
        $deleteStmt->bind_param("i", $seller_id);
        $deleteStmt->execute();

        // Send approval email
        // $mail = new PHPMailer(true);
        // $mail->isSMTP();
        // $mail->Host = 'smtp.sendgrid.net';
        // $mail->SMTPAuth = true;
        // // $mail->Username = 'apikey';
        // // $mail->Password = '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';
        // $mail->SMTPSecure = 'tls';
        // $mail->Port = 587;

        // $mail->setFrom('lacandiliangelod@gmail.com', 'Admin');
        // $mail->addAddress($seller['email']);
        // $mail->isHTML(true);
        // $mail->Subject = 'Account Approved - Taytay Marketplace';

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

        $decline_reason = $input['reason'] ?? null;

        // Delete the seller's record from the registration table
        $deleteStmt = $conn->prepare("DELETE FROM registration WHERE seller_id = ?");
        $deleteStmt->bind_param("i", $seller_id);
        $deleteStmt->execute();

        // Send email to the seller notifying them that their account is declined
        // $mail = new PHPMailer(true);
        // $mail->isSMTP();
        // $mail->Host = 'smtp.sendgrid.net';
        // $mail->SMTPAuth = true;
        // // $mail->Username = 'apikey';
        // // $mail->Password = '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';
        // $mail->SMTPSecure = 'tls';
        // $mail->Port = 587;

        // $mail->setFrom('lacandiliangelod@gmail.com', 'Admin');
        // $mail->addAddress($seller['email']);
        // $mail->isHTML(true);
        // $mail->Subject = 'Account Declined - Taytay Marketplace';

        // Customized Email Design with Decline Reason
    $mail->Body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        // <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                padding: 20px;
            }
            .container {
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                max-width: 600px;
                margin: auto;
            }
            h1 {
                color: #d9534f;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Account Declined</h1>
            <p>Dear Seller,</p>
            <p>We regret to inform you the shop you created has been declined for creation.</p>
            <p><strong>Reason:</strong> ' . htmlspecialchars($decline_reason) . '</p>
            <p>If you have any questions, feel free to contact our support team.</p>
            <p>Thank you,</p>
            <p>The Taytay Marketplace Team</p>
        </div>
    </body>
    </html>
    ';

        $mail->send();

        echo json_encode(['success' => true, 'message' => 'Account declined and email sent.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close the mysqli connection
$conn->close();
?>
