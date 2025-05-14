<?php

include '../Connect/dataDB.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$schoolID = $_POST['SchoolID'] ?? null;

if (!$schoolID) {
    echo json_encode(["error" => "No School Information"], JSON_PRETTY_PRINT);
    exit;
}

// First Query: Get School Information
$sql = "SELECT si.schoolID, si.schoolName, b.barangay_name 
        FROM schoolinfo si
        JOIN schooladd sa ON si.address_id = sa.address_id
        JOIN barangay b ON sa.barangay_code = b.barangay_code
        WHERE si.schoolID = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL Prepare Failed: " . $conn->error], JSON_PRETTY_PRINT);
    exit;
}

$stmt->bind_param("s", $schoolID);
$stmt->execute();
$result = $stmt->get_result();
$schoolData = $result->fetch_assoc();
$stmt->close();

// If no school info is found, return error
if (!$schoolData) {
    echo json_encode(["error" => "No school records found"], JSON_PRETTY_PRINT);
    exit;
}

// Second Query: Call the Stored Procedure for Enrollment Data
$stmt = $conn->prepare("CALL SELECT 
        SchoolID, 
        enrollment_year, 
        SUM(num_male) AS total_males, 
        SUM(num_female) AS total_females, 
        (SUM(num_male) + SUM(num_female)) AS total_enrollees
    FROM enrollment
    WHERE SchoolID = ?
        AND enrollment_year = 2024  -- Filters records for the current year
    GROUP BY SchoolID, enrollment_year;");

if (!$stmt) {
    echo json_encode(["error" => "Stored Procedure Call Failed: " . $conn->error], JSON_PRETTY_PRINT);
    exit;
}

$stmt->bind_param("s", $schoolID);
$stmt->execute();
$result = $stmt->get_result();
$enrollmentData = $result->fetch_assoc(); // Since the result is always one row
$stmt->close();

// Ensure we close any remaining results to prevent errors
$conn->next_result();
$conn->close();

// Combine school info and enrollment data
$response = [
    "school_info" => $schoolData,
    "enrollment_data" => $enrollmentData ?: ["message" => "No enrollment data found"]
];

echo json_encode($response, JSON_PRETTY_PRINT);

?>
