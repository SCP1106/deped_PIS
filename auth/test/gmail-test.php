<?php
// This script tests Gmail SMTP configuration

// Load PHPMailer
require_once __DIR__ . "/vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Enable verbose debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    // Configure SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = ''; // ENTER YOUR GMAIL ADDRESS HERE
    $mail->Password = ''; // ENTER YOUR APP PASSWORD HERE (NOT YOUR REGULAR PASSWORD)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Set sender and recipient
    $mail->setFrom($mail->Username, 'DepEd System');
    $mail->addAddress($mail->Username); // Send to yourself for testing

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Gmail SMTP Test';
    $mail->Body = '<h1>This is a test email</h1><p>If you can see this, your Gmail SMTP configuration is working correctly!</p>';
    $mail->AltBody = 'This is a test email. If you can see this, your Gmail SMTP configuration is working correctly!';

    // Send the email
    $mail->send();
    echo '<div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h2>Success!</h2>
            <p>Test email has been sent successfully. Check your inbox (and spam folder).</p>
          </div>';
} catch (Exception $e) {
    echo '<div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h2>Error</h2>
            <p>Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '</p>
          </div>';
}

// Display configuration instructions
echo '<div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
        <h2>Gmail SMTP Configuration Guide</h2>
        <ol>
            <li><strong>Create an App Password for Gmail:</strong>
                <ul>
                    <li>Go to <a href="https://myaccount.google.com/security" target="_blank">Google Account Security</a></li>
                    <li>Make sure 2-Step Verification is enabled</li>
                    <li>Scroll down to "App passwords"</li>
                    <li>Select "Mail" as the app and "Other" as the device (name it "DepEd System")</li>
                    <li>Copy the 16-character password Google generates</li>
                </ul>
            </li>
            <li><strong>Update this file with your credentials:</strong>
                <ul>
                    <li>Enter your Gmail address on line 17</li>
                    <li>Enter your App Password on line 18</li>
                </ul>
            </li>
            <li><strong>Run this test script again to verify your configuration</strong></li>
            <li><strong>Once working, update these settings in all email-sending files:</strong>
                <ul>
                    <li>process-register.php</li>
                    <li>mailer.php</li>
                    <li>resend-otp.php</li>
                    <li>send-password-reset.php</li>
                </ul>
            </li>
        </ol>
      </div>';
