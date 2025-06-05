<?php
// Database connection
include '../Connect/dataDB.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}



if (!isset($_GET['region_id'])) {
    echo json_encode(['error' => 'Region ID is required']);
    exit;
}

$regionId = mysqli_real_escape_string($conn, $_GET['region_id']);

$sql = "SELECT province_id as id, province_name as name, province_code 
        FROM province 
        WHERE region_id = '$regionId' 
        ORDER BY province_name";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$provinces = [];
while ($row = mysqli_fetch_assoc($result)) {
    $provinces[] = $row;
}

echo json_encode($provinces);

mysqli_close($conn);
?>
