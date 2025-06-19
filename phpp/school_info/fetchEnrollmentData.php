<?php

include '../Connect/dataDB.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$schoolID = $_GET['school_id'] ?? null;

if (!$schoolID) {
    echo json_encode(["error" => "Missing school_id parameter."], JSON_PRETTY_PRINT);
    exit;
}

$sql = "SELECT 
    K_M, G1_M, G2_M, G3_M, G4_M, G5_M, G6_M, G7_M, G8_M, G9_M, G10_M,
    FS_G11_ACAD_M, FS_G11_TVL_M, FS_G11_SPORT_M, FS_G11_ART_M, FS_G11_UNQ_M,
    FS_G12_ACAD_M, FS_G12_TVL_M, FS_G12_SPORT_M, FS_G12_ART_M, FS_G12_UNQ_M,
    SS_G11_ACAD_M, SS_G11_TVL_M, SS_G11_SPORT_M, SS_G11_ART_M, SS_G11_UNQ_M,
    SS_G12_ACAD_M, SS_G12_TVL_M, SS_G12_SPORT_M, SS_G12_ART_M, SS_G12_UNQ_M,
    
    K_F, G1_F, G2_F, G3_F, G4_F, G5_F, G6_F, G7_F, G8_F, G9_F, G10_F,
    FS_G11_ACAD_F, FS_G11_TVL_F, FS_G11_SPORT_F, FS_G11_ART_F, FS_G11_UNQ_F,
    FS_G12_ACAD_F, FS_G12_TVL_F, FS_G12_SPORT_F, FS_G12_ART_F, FS_G12_UNQ_F,
    SS_G11_ACAD_F, SS_G11_TVL_F, SS_G11_SPORT_F, SS_G11_ART_F, SS_G11_UNQ_F,
    SS_G12_ACAD_F, SS_G12_TVL_F, SS_G12_SPORT_F, SS_G12_ART_F, SS_G12_UNQ_F
FROM enrollment_data
WHERE SchoolID = ?
AND SchoolYear = 2024
-- CASE 
--     WHEN MONTH(CURDATE()) >= 6 
--     THEN CONCAT(YEAR(CURDATE()), '-', YEAR(CURDATE()) + 1)
--     ELSE CONCAT(YEAR(CURDATE()) - 1, '-', YEAR(CURDATE()))
-- END";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL Prepare Failed: " . $conn->error], JSON_PRETTY_PRINT);
    exit;
}

$stmt->bind_param("s", $schoolID);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$data) {
    echo json_encode(["error" => "No enrollment data found for this school"], JSON_PRETTY_PRINT);
    exit;
}

echo json_encode($data, JSON_PRETTY_PRINT);

?>
