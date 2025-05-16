<?php
// Start session
session_start();

// Include database connection and rate limiter
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/rate-limiter.php';
require_once __DIR__ . '/../core/otp-handler.php'; // Use the separate OTP handler file

// CSRF protection
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed");
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limiting
    $rateLimiter = new RateLimiter($mysqli, $_SERVER['REMOTE_ADDR'], null, 5, 300); // 5 attempts per 5 minutes

    if ($rateLimiter->isRateLimited()) {
        $timeLeft = $rateLimiter->getTimeUntilUnlocked();
        $_SESSION['errors'] = ["Too many registration attempts. Please try again in " . ceil($timeLeft / 60) . " minutes."];
        header("Location: ../pages/register.php");
        exit;
    }

    // Sanitize and validate input
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

    // Error array to store validation errors
    $errors = [];

    // Validate email
    if (!$email) {
        $errors[] = "A valid email address is required.";
    }

    // Check if email already exists
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "This email is already registered.";
    }
    $stmt->close();

    // If there are errors, redirect back with errors
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;
        header("Location: ../pages/register.php");
        exit;
    }

    // Record the attempt
    $rateLimiter->recordAttempt();

    // Generate activation token
    $token = bin2hex(random_bytes(16));
    $token_hash = hash('sha256', $token);

    // Insert email into the database with pending status
    try {
        $stmt = $mysqli->prepare("INSERT INTO users (email, account_activation_hash) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $token_hash);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Error creating user account");
        }

        $user_id = $mysqli->insert_id;
        $stmt->close();

        // Send activation email
        require_once __DIR__ . '../assets/vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        // Server settings
        // $mail->SMTPDebug = 2; // Uncomment for debugging
        $mail->isSMTP();
        $mail->Host = 'mail.rimsdone.com'; // Use Gmail or another reliable SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@oras.rimsdone.com'; // REPLACE with your actual email
        $mail->Password = 'u0eVL8P]~M+o'; // REPLACE with your actual password or app password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('no-reply@oras.rimsdone.com', 'DepEd Registration'); // REPLACE with your actual email
        $mail->addAddress($email);
        
        // Build a robust base URL
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . $host;
        
        // Correct activation link path after restructure
        $activation_link = $base_url . '/auth/pages/activate-account.php?token=' . urlencode($token);

        $mail->isHtml(true);
        $mail->Subject = "DepEd System: Activate Your Account";
        $mail->Body = <<<END
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
        <h2>Verify Your Email Address</h2>
        <p>Thank you for registering with DepEd System.</p>
        <p>To complete your registration, please click the button below to activate your account:</p>
        <p><a href="$activation_link" class="button">Activate Account</a></p>
        <p>If the button above does not work, copy and paste this link into your browser:</p>
        <p><a href="$activation_link">$activation_link</a></p>
        <div class="footer">
            If you did not request this registration, you can safely ignore this email.<br>
            &copy; DepEd System
        </div>
    </div>
</body>
</html>
END;

        // For development/testing - display the activation link on screen
        // Comment this out in production
        $_SESSION['debug_activation_link'] = $activation_link;

        try {
            $mail->send();
            $_SESSION['success'] = "Registration initiated! Please check your email to verify your address.";
        } catch (Exception $e) {
            // Log the error with more details
            error_log("Email sending failed: " . $e->getMessage());
            error_log("PHPMailer ErrorInfo: " . $mail->ErrorInfo);
            
            // Store error info in session for admin/debug
            $_SESSION['email_error'] = "Mailer Error: " . htmlspecialchars($mail->ErrorInfo);
            
            // For development - still allow registration to proceed with a different message
            $_SESSION['success'] = "Account created, but we couldn't send the verification email. Please use the link below:";
        }

        // Redirect to a success page
        header("Location: ../pages/register.php");
        exit;
    } catch (Exception $e) {
        // Log the error but don't expose details to users
        error_log("Database error: " . $e->getMessage());

        // Handle database errors
        $_SESSION['errors'] = ["An error occurred while creating your account. Please try again."];
        header("Location: ../pages/register.php");
        exit;
    }
} else {
    // Redirect if accessed directly
    header("Location: ../pages/register.php");
    exit;
}
