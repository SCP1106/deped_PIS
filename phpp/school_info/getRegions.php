<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once '../Connect/dataDB.php';

try {
    $regions = [];

    // Query the database using mysqli
    $query = "SELECT region_id AS id, region_name AS name FROM region ORDER BY region_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $regions[] = $row;
        }
    } else {
        // Default regions if database is empty or error
        $regions = [
            ['id' => '01', 'name' => 'Region I - Ilocos Region'],
            ['id' => '02', 'name' => 'Region II - Cagayan Valley'],
            ['id' => '03', 'name' => 'Region III - Central Luzon'],
            ['id' => '04', 'name' => 'Region IV-A - CALABARZON'],
            ['id' => '05', 'name' => 'Region V - Bicol Region'],
            ['id' => '06', 'name' => 'Region VI - Western Visayas'],
            ['id' => '07', 'name' => 'Region VII - Central Visayas'],
            ['id' => '08', 'name' => 'Region VIII - Eastern Visayas'],
            ['id' => '09', 'name' => 'Region IX - Zamboanga Peninsula'],
            ['id' => '10', 'name' => 'Region X - Northern Mindanao'],
            ['id' => '11', 'name' => 'Region XI - Davao Region'],
            ['id' => '12', 'name' => 'Region XII - SOCCSKSARGEN'],
            ['id' => '13', 'name' => 'Region XIII - Caraga'],
            ['id' => 'NCR', 'name' => 'National Capital Region'],
            ['id' => 'CAR', 'name' => 'Cordillera Administrative Region'],
            ['id' => 'BARMM', 'name' => 'Bangsamoro Autonomous Region in Muslim Mindanao']
        ];
    }

    echo json_encode($regions);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>
