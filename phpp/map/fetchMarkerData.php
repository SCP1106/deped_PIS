<?php
ob_start();
// Database connection
include '../Connect/dataDB.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and prepare input
    $clickedCity = isset($_POST['data']) ? trim($_POST['data']) : '';
    $filterType = isset($_POST['filter']) ? strtolower(trim($_POST['filter'])) : '';

    $cityID = null;

    // Retrieve city_id only if city name is provided
    if (!empty($clickedCity)) {
        $cityQuery = $conn->prepare("SELECT city_id FROM muncity WHERE city_name = ?");
        if ($cityQuery) {
            $cityQuery->bind_param("s", $clickedCity);
            $cityQuery->execute();
            $cityQuery->bind_result($cityID);
            $cityQuery->fetch();
            $cityQuery->close();
        }
    }

    // Base SQL query
    $sql = "SELECT sc.SchoolID, si.SchoolName, sc.latitude, sc.longitude, b.barangay_name,
                   CASE
                       WHEN si.CurricularOffer REGEXP 'Grade[[:space:]]*1-6' THEN 'elementary'
                       WHEN si.CurricularOffer REGEXP 'Grade[[:space:]]*7-10|Grade[[:space:]]*11-12' THEN 'secondary'
                       ELSE 'secondary'
                   END AS CurricularOffer
            FROM schoolcoor sc
            INNER JOIN schoolinfo si ON sc.schoolCoorID = si.schoolCoorID
            INNER JOIN schooladd sa ON si.address_id = sa.address_id
            INNER JOIN barangay b ON sa.barangay_code = b.barangay_code";

    // Add WHERE clause only if cityID is found
    $params = [];
    $paramTypes = '';
    if ($cityID !== null) {
        $sql .= " WHERE sc.city_id = ?";
        $params[] = $cityID;
        $paramTypes .= 'i';
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["error" => "Query preparation failed: " . $conn->error]);
        exit;
    }

    // Bind parameters if necessary
    if (!empty($params)) {
        $stmt->bind_param($paramTypes, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $response = [];
    while ($row = $result->fetch_assoc()) {
        // Normalize CurricularOffer for filtering
        $curricular = strtolower($row['CurricularOffer']);
        if ($filterType && $curricular !== $filterType) {
            continue;
        }
        $response[] = $row;
    }
    echo json_encode($response);
    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
ob_end_flush();
?>
