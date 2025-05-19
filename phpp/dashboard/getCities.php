<?php
// Database connection
require_once '../config/db_connect.php';

// Get region ID from request
$region_id = isset($_GET['region_id']) ? $_GET['region_id'] : '';

if (empty($region_id)) {
    echo json_encode(['error' => 'Region ID is required']);
    exit;
}

// Get cities for the specified region
$query = "SELECT id, name FROM cities WHERE region_id = ? ORDER BY name";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $region_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$cities = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cities[] = $row;
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($cities);
?>
