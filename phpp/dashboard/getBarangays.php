<?php
// Database connection
require_once '../config/db_connect.php';

// Get city ID from request
$city_id = isset($_GET['city_id']) ? $_GET['city_id'] : '';

if (empty($city_id)) {
    echo json_encode(['error' => 'City ID is required']);
    exit;
}

// Get barangays for the specified city
$query = "SELECT id, name FROM barangays WHERE city_id = ? ORDER BY name";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $city_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$barangays = [];
while ($row = mysqli_fetch_assoc($result)) {
    $barangays[] = $row;
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($barangays);
?>
