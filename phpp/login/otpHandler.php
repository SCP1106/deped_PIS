<?php
// otpHandler.php - Shared OTP Functions
include '../Connect/credsDB.php';

require "../../vendor/autoload.php"; // Or include the PHPMailer files manually

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Reusable OTP generation function
function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Reusable function to store OTP in the database
function storeOTP($user_id, $otp_code) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO otps_log (user_id, otp_code) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $otp_code);
    return $stmt->execute();
}

// Reusable function to send OTP via email
function sendOTPEmail($email, $otp_code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'mail.rimsdone.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@oras.rimsdone.com';
        $mail->Password = 'u0eVL8P]~M+o';

        // Email content
        $mail->setFrom('no-reply@oras.rimsdone.com', 'Health Cartographer');
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP Code';
        $mail->isHTML(true); 

        // HTML design with larger GIF
        $mail->Body = '
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f7f9;
                    margin: 0;
                    padding: 20px;
                }
                .email-container {
                    background-color: #ffffff;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                .otp-header {
                    color: #2c3e50;
                    font-size: 24px;
                    margin-bottom: 10px;
                }
                .otp-code {
                    color: #e74c3c;
                    font-size: 30px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                .gif-container {
                    text-align: center;
                    margin-top: 20px;
                }
                .otp-gif {
                    width: 100%; /* Adjust the width to make it larger */
                    max-width: 500px;
                    height: auto;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <h1 class="otp-header">Your OTP Code</h1>
                <p>Hello,</p>
                <p>To complete your request, please use the OTP code below:</p>
                <div class="otp-code">' . htmlspecialchars($otp_code, ENT_QUOTES, 'UTF-8') . '</div>
                <p>This code is valid for a limited time, so please use it promptly!</p>

                <div class="gif-container">
                    <img class="otp-gif" src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExYzY3M3Y5M2twYWUxYXF5a2IwZnJvb25sN2R2eGJwaWRhYmZ5cXV1aCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/LMiKBYcE5PI01Eow0Z/giphy.gif" alt="Loading GIF">
                </div>
            </div>
        </body>
        </html>';

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $e->getMessage());
        return false;
    }
}

?>
