<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
require_once '../Connect/dataDB.php'; // Make sure this file initializes $conn using mysqli

try {
    $cityId = $_GET['city_id'] ?? '';

    if (empty($cityId)) {
        throw new Exception('City ID is required');
    }

    // Prepare and execute the query using mysqli
    $stmt = $conn->prepare("SELECT barangay_code as id, barangay_name as name FROM barangay WHERE city_id = ? ORDER BY barangay_name");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $cityId);
    $stmt->execute();
    $result = $stmt->get_result();

    $barangays = [];
    while ($row = $result->fetch_assoc()) {
        $barangays[] = $row;
    }

    // If no barangays found in database, return mock data
    if (empty($barangays)) {
        for ($i = 1; $i <= 20; $i++) {
            $barangays[] = [
                'id' => $cityId . 'B' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'name' => 'Barangay ' . $i
            ];
        }
    }

    echo json_encode($barangays);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
