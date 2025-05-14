<?php
// Include database connection
$mysqli = require __DIR__ . "/database.php";

// Check if email is provided
if (!isset($_GET["email"])) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Email parameter is required"]);
    exit;
}

// Sanitize email
$email = filter_var($_GET["email"], FILTER_VALIDATE_EMAIL);

if (!$email) {
    header("Content-Type: application/json");
    echo json_encode(["available" => false, "error" => "Invalid email format"]);
    exit;
}

// Prepare SQL statement
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if email exists
$is_available = $result->num_rows === 0;

// Return JSON response
header("Content-Type: application/json");
echo json_encode(["available" => $is_available]);
