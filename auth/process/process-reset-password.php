<?php
// Start session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
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
$rateLimiter = new RateLimiter($mysqli, $_SERVER['REMOTE_ADDR'], null, 5, 300); // 5 attempts per 5 minutes

if ($rateLimiter->isRateLimited()) {
    $timeLeft = $rateLimiter->getTimeUntilUnlocked();
    $_SESSION['errors'] = ["Too many password reset attempts. Please try again in " . ceil($timeLeft / 60) . " minutes."];
    header("Location: forgot-password.php");
    exit;
}

// Record the attempt
$rateLimiter->recordAttempt();

// Validate token
if (!isset($_POST["token"]) || empty($_POST["token"])) {
    die("Token is missing");
}

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM users
        WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found or has already been used");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired");
}

// Validate password
$errors = [];

$password = $_POST["password"] ?? '';
$password_confirmation = $_POST["password_confirmation"] ?? '';

if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters";
}

if (!preg_match("/[A-Z]/", $password)) {
    $errors[] = "Password must contain at least one uppercase letter";
}

if (!preg_match("/[a-z]/", $password)) {
    $errors[] = "Password must contain at least one lowercase letter";
}

if (!preg_match("/[0-9]/", $password)) {
    $errors[] = "Password must contain at least one number";
}

if (!preg_match("/[@$!%*?&]/", $password)) {
    $errors[] = "Password must contain at least one special character (@, $, !, %, *, ?, &)";
}

if ($password !== $password_confirmation) {
    $errors[] = "Passwords must match";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: reset-password.php?token=" . urlencode($token));
    exit;
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Update the user's password
$sql = "UPDATE users
        SET password = ?,
            reset_token_hash = NULL,
            reset_token_expires_at = NULL
        WHERE id = ?";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("si", $password_hash, $user["id"]);
$stmt->execute();

// Redirect to success page
$_SESSION['success'] = "Your password has been updated successfully. You can now log in with your new password.";
header("Location: login.php");
exit;
