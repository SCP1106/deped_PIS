<?php
// Database connection
include '../Connect/dataDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and prepare input
    $clickedCity = isset($_POST['data']) ? strtoupper(str_replace(' ', '', $_POST['data'])) : '';
    $filterType = isset($_POST['filter']) ? strtolower(trim($_POST['filter'])) : '';

    // Retrieve city_id if city name was provided
    $cityID = null;
    if (!empty($clickedCity)) {
        $cityQuery = $conn->prepare("SELECT city_id FROM muncity WHERE REPLACE(UPPER(city_name), ' ', '') = ?");
        $cityQuery->bind_param("s", $clickedCity);
        $cityQuery->execute();
        $cityQuery->bind_result($cityID);
        $cityQuery->fetch();
        $cityQuery->close();
    }

    // Prepare base query
    $sql = "SELECT sc.SchoolID, si.SchoolName, sc.latitude, sc.longitude, b.barangay_name,
                   CASE
                       WHEN si.CurricularOffer REGEXP 'Grade 1-6' THEN 'elementary'
                       WHEN si.CurricularOffer REGEXP 'Grade 7-10|Grade 11-12' THEN 'secondary'
                       ELSE 'secondary'
                   END AS CurricularOffer
            FROM schoolcoor sc
            INNER JOIN schoolinfo si ON sc.schoolCoorID = si.schoolCoorID
            INNER JOIN schooladd sa ON si.address_id = sa.address_id
            INNER JOIN barangay b ON sa.barangay_code = b.barangay_code";

    // Add condition only if cityID was found
    $params = [];
    $paramTypes = '';
    if ($cityID !== null) {
        $sql .= " WHERE sc.city_id = ?";
        $params[] = $cityID;
        $paramTypes .= 'i';
    }

    // Execute query
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["error" => "Query preparation failed: " . $conn->error]);
        exit;
    }

    if (!empty($params)) {
        $stmt->bind_param($paramTypes, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $response = [];
    while ($row = $result->fetch_assoc()) {
        // Normalize and apply filter if needed
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

?>