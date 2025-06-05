<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../Connect/dataDB.php';

try {
    $provinceId = $_GET['province_id'] ?? '';

    if (empty($provinceId)) {
        throw new Exception('Province ID is required');
    }

    $provinceIdSafe = mysqli_real_escape_string($conn, $provinceId);

    $query = "
        SELECT city_id as id, city_name as name 
        FROM muncity 
        WHERE province_id = '$provinceIdSafe' 
        ORDER BY city_name
    ";

    $result = mysqli_query($conn, $query);
    $cities = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cities[] = $row;
        }
    } else {
        // Optional: Return placeholder/mock cities
        for ($i = 1; $i <= 5; $i++) {
            $cities[] = [
                'id' => $provinceId . str_pad($i, 2, '0', STR_PAD_LEFT),
                'name' => "Mock City $i (Province $provinceId)"
            ];
        }
    }

    echo json_encode($cities);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>