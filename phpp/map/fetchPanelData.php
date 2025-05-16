<?php

include '../Connect/dataDB.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$schoolID = $_POST['SchoolID'] ?? null;

if (!$schoolID) {
    error_log("School ID not provided in POST request.");
    http_response_code(400);
    exit(json_encode(["error" => "No School Information"], JSON_PRETTY_PRINT));
}

// Get School Information with Institution and Principal Details
$query = "
WITH principal_cte AS (
    SELECT 
        er.School_ID,
        TRIM(CONCAT(er.first_name, ' ', COALESCE(er.middle_name, ''), ' ', er.last_name)) AS principal,
        TIMESTAMPDIFF(
            YEAR, 
            STR_TO_DATE(
                CONCAT(er.BIRTHDAY_DD, ' ', er.BIRTHDAY_MM, ' ', er.BIRTHDAY_YYYY), 
                '%d %M %Y'
            ), 
            CURDATE()
        ) AS principal_age
    FROM employee_records er
    WHERE er.rank_position = ( 
        SELECT MIN(rank_position) 
        FROM employee_records er2 
        WHERE er2.School_ID = er.School_ID
    )
)

SELECT 
    cd.CDName AS district,
    si.schoolID, 
    si.schoolName, 
    si.Institution,
    b.barangay_name,
    p.principal,
    p.principal_age
FROM schoolinfo si
JOIN schooladd sa ON si.address_id = sa.address_id
JOIN barangay b ON sa.barangay_code = b.barangay_code
LEFT JOIN citydistrict cd ON cd.CDNum = si.CDNum
LEFT JOIN principal_cte p ON si.schoolID = p.School_ID
WHERE si.schoolID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $schoolID);
$stmt->execute();
$schoolData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$schoolData) {
    http_response_code(404);
    exit(json_encode(["error" => "No school records found"], JSON_PRETTY_PRINT));
}

// Add fallback if no principal found
$schoolData['principal'] = $schoolData['principal'] ?? 'No principal found';
$schoolData['principal_age'] = $schoolData['principal_age'] ?? 'N/A';

// Get enrollment data from new table
$stmt = $conn->prepare("
    SELECT 
        ELEM_TOTAL_M, ELEM_TOTAL_F,
        JHS_TOTAL_M, JHS_TOTAL_F,
        FS_SHS_TTL_M, FS_SHS_TTL_F,
        SS_SHS_TTL_M, SS_SHS_TTL_F,
        ALL_GRADE_TTL
    FROM enrollment_data
    WHERE schoolID = ? AND SchoolYear = 2024
    LIMIT 1
");
$stmt->bind_param("s", $schoolID);
$stmt->execute();
$enrollmentData = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fallback if no data
if (!$enrollmentData) {
    $enrollmentData = [
        "ELEM_TOTAL_M" => 0, "ELEM_TOTAL_F" => 0,
        "JHS_TOTAL_M" => 0, "JHS_TOTAL_F" => 0,
        "FS_SHS_TTL_M" => 0, "FS_SHS_TTL_F" => 0,
        "SS_SHS_TTL_M" => 0, "SS_SHS_TTL_F" => 0,
        "ALL_GRADE_TTL" => 0
    ];
}

// Calculate total male, female, and enrollees
$total_male = ($enrollmentData['ELEM_TOTAL_M'] ?? 0) +
              ($enrollmentData['JHS_TOTAL_M'] ?? 0) +
              ($enrollmentData['FS_SHS_TTL_M'] ?? 0) +
              ($enrollmentData['SS_SHS_TTL_M'] ?? 0);

$total_female = ($enrollmentData['ELEM_TOTAL_F'] ?? 0) +
                ($enrollmentData['JHS_TOTAL_F'] ?? 0) +
                ($enrollmentData['FS_SHS_TTL_F'] ?? 0) +
                ($enrollmentData['SS_SHS_TTL_F'] ?? 0);

$enrollmentData['total_male'] = $total_male;
$enrollmentData['total_female'] = $total_female;
$enrollmentData['total_enrollees'] = $total_male + $total_female;

// Get employee count
$stmt = $conn->prepare("SELECT COUNT(*) AS total_employees FROM employee_records WHERE School_ID = ?");
$stmt->bind_param("s", $schoolID);
$stmt->execute();
$empResult = $stmt->get_result()->fetch_assoc();
$stmt->close();

$conn->close();

// Return JSON response
echo json_encode([
    "school_info" => $schoolData,
    "enrollment_data" => $enrollmentData,
    "employee_count" => $empResult['total_employees']
], JSON_PRETTY_PRINT);

?>
