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

// Get Enrollment Data
$stmt = $conn->prepare(" SELECT 
        SchoolID, 
        enrollment_year, 
        SUM(num_male) AS total_males, 
        SUM(num_female) AS total_females, 
        (SUM(num_male) + SUM(num_female)) AS total_enrollees
    FROM enrollment
    WHERE SchoolID = ? 
        AND enrollment_year = 2024  -- Filters records for the current year
    GROUP BY SchoolID, enrollment_year;");
    
$stmt->bind_param("s", $schoolID);
$stmt->execute();
$result = $stmt->get_result();
$enrollmentData = $result->fetch_assoc(); // ✅ changed from fetch_all() to fetch_assoc()
$stmt->close();

// Get Employee Count
$empQuery = "SELECT COUNT(*) AS total_employees FROM employee_records WHERE School_ID = ?";
$stmt = $conn->prepare($empQuery);
$stmt->bind_param("s", $schoolID);
$stmt->execute();
$empResult = $stmt->get_result()->fetch_assoc();
$stmt->close();

$conn->close();

// Return JSON response
echo json_encode([
    "school_info" => $schoolData,
    "enrollment_data" => !empty($enrollmentData) ? $enrollmentData : ["message" => "No enrollment data found"],
    "employee_count" => $empResult['total_employees']
], JSON_PRETTY_PRINT);

?>
