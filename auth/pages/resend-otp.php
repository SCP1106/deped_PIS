<?php
// Start session
session_start();

// Include database connection
require_once 'database.php';
require_once 'otp-handler.php'; // Use the separate OTP handler file

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['errors'] = ["You must be logged in to resend a verification code."];
    header("Location: login.php");
    exit;
}

// Include rate limiter
require_once 'rate-limiter.php';

// Rate limiting
$rateLimiter = new RateLimiter($mysqli, $_SERVER['REMOTE_ADDR'], null, 3, 300); // 3 attempts per 5 minutes

if ($rateLimiter->isRateLimited()) {
    $timeLeft = $rateLimiter->getTimeUntilUnlocked();
    $_SESSION['errors'] = ["Too many verification code requests. Please try again in " . ceil($timeLeft / 60) . " minutes."];
    header("Location: verify-otp.php");
    exit;
}

// Record the attempt
$rateLimiter->recordAttempt();

// Get user email
$stmt = $mysqli->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['errors'] = ["User not found."];
    header("Location: login.php");
    exit;
}

$email = $user['email'];

// Create OTP handler
$otpHandler = new OTPHandler($mysqli);

// Generate new OTP
$otp_code = $otpHandler->generateOTP();

// Store OTP in database
$otpHandler->storeOTP($user_id, $otp_code);

// Update the email configuration
// Create PHPMailer instance
require_once __DIR__ . "/../vendor/autoload.php";
$mail = new PHPMailer\PHPMailer\PHPMailer(true);

// $mail->SMTPDebug = 2; // Uncomment for debugging
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->Host = "mail.rimsdone.com"; // Use Gmail or another reliable SMTP server
$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->Username = "no-reply@oras.rimsdone.com"; // REPLACE with your actual email
$mail->Password = "u0eVL8P]~M+o"; // REPLACE with your actual password or app password
$mail->isHtml(true);

// Send OTP via email
try {
    $otpHandler->sendOTPEmail($email, $otp_code, $mail);
    $_SESSION['success'] = "A new verification code has been sent to your email.";
} catch (Exception $e) {
    error_log("Failed to send OTP: " . $e->getMessage());
    $_SESSION['errors'] = ["Failed to send verification code. Please try again later."];
}

// Redirect back to verify page
header("Location: verify-otp.php");
exit;
