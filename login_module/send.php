<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require'vendor/autoload.php'; // dapat may composer na naka isntall at phpmailer kahit nandyan yung vendor na folder


// inclutions
include('connection.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];

  // generate ng otp
  $otp = rand(100000, 999999);

  // generate ng expiration ng otp
  $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

  // insert ng otp tsaka otp expiry
  $sql = "UPDATE users SET otp = ?, otp_expiry = ? WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $otp, $otp_expiry, $email);
  $stmt->execute();

  // dito isesend ng mailer yung otp 
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'nedibanez0@gmail.com';// dummy email to(eto yung mailer account)
    $mail->Password   = 'rfhanragjuqfcgjd'; // app password sya,(as long as nasa code yung app password kahit anong device pwede magamit) 500 mails lang per day
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    $mail->setFrom('nedibanez0@gmail.com', 'BSIT 3-1A');
    $mail->addAddress($email); // yung sesendan ng email

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset OTP';
    $mail->Body    = 'Your OTP code is: ' . $otp;

    $mail->send();
    echo 'OTP sent successfully. Please check your email.';
    header('Location: verify_otp.php');  // lipat ng page for otp verification
    exit();
  } catch (Exception $e) {
    echo "Message could not be sent. Error: {$mail->ErrorInfo}";
  }
}
