<?php
// Start session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: forgot-password.php");
    exit;
}

// CSRF protection
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed");
}

// Include rate limiter
require_once 'database.php';
require_once 'rate-limiter.php';

// Rate limiting
$email = filter_var($_POST["email"] ?? '', FILTER_VALIDATE_EMAIL);
$rateLimiter = new RateLimiter($mysqli, $_SERVER['REMOTE_ADDR'], $email, 3, 300); // 3 attempts per 5 minutes

if ($rateLimiter->isRateLimited()) {
    $timeLeft = $rateLimiter->getTimeUntilUnlocked();
    $_SESSION['errors'] = ["Too many password reset attempts. Please try again in " . ceil($timeLeft / 60) . " minutes."];
    header("Location: forgot-password.php");
    exit;
}

// Record the attempt
$rateLimiter->recordAttempt();

// Validate email
if (!$email) {
    $_SESSION['errors'] = ["Please enter a valid email address"];
    header("Location: forgot-password.php");
    exit;
}

// Generate token
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 minutes

$mysqli = require __DIR__ . "/database.php";

// Check if email exists
$stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Don't reveal that the email doesn't exist for security reasons
    $_SESSION['success'] = "If your email is registered, you will receive a password reset link shortly.";
    header("Location: forgot-password.php");
    exit;
}

// Update user with reset token
$sql = "UPDATE users
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

// Send email if the update was successful
if ($mysqli->affected_rows > 0) {
    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("your-email@gmail.com", "DepEd Password Reset"); // REPLACE with your actual email
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";

    $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=" . $token;

    // For development/testing - display the reset link
    $_SESSION['debug_reset_link'] = $reset_link;

    $mail->Body = <<<HTML
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .button { display: inline-block; padding: 10px 20px; background-color: #2e8b57; color: white; text-decoration: none; border-radius: 5px; }
            .footer { font-size: 12px; color: #777; margin-top: 30px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Password Reset Request</h2>
            <p>We received a request to reset your password. Click the button below to create a new password:</p>
            <p style="text-align: center; margin: 30px 0;">
                <a href="$reset_link" class="button">Reset Password</a>
            </p>
            <p>If you didn't request a password reset, you can safely ignore this email.</p>
            <p>This link will expire in 30 minutes.</p>
            <div class="footer">
                <p>This is an automated message, please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    HTML;

    try {
        $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        // Don't reveal the error to the user
    }
}

// Always show success message even if email doesn't exist (for security)
$_SESSION['success'] = "If your email is registered, you will receive a password reset link shortly.";
header("Location: forgot-password.php");
exit;
