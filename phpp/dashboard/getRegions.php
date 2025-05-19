<?php
// Database connection
require_once '../config/db_connect.php';

// Get all regions
$query = "SELECT id, name FROM regions ORDER BY name";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$regions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $regions[] = $row;
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($regions);
?>
