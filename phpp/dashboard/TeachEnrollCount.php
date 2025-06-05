<?php
// Start a session to access session variables
session_start();

// Include database connection
include '../Connect/dataDB.php';

// Prepare the SQL queries
// $sql_enrollees = "SELECT SUM(ALL_GRADE_TTL) AS total_enrollees
//                   FROM enrollment_data
//                   WHERE SchoolYear = CASE 
//                       WHEN MONTH(CURDATE()) >= 6 THEN YEAR(CURDATE()) 
//                       ELSE YEAR(CURDATE()) - 1 
//                   END;";

$sql_enrollees = "SELECT SUM(ALL_GRADE_TTL) AS total_enrollees
                  FROM enrollment_data
                  WHERE SchoolYear = 2024;";
                  
$sql_employees = "SELECT COUNT(*) AS total_employees FROM employee_records";

// Execute the first query (enrollment)
$stmt1 = $conn->prepare($sql_enrollees);
if (!$stmt1) {
    die(json_encode(["error" => "Error preparing statement (enrollees): " . $conn->error]));
}
$stmt1->execute();
$result1 = $stmt1->get_result();
$schoolData = $result1->fetch_assoc();
$stmt1->close();

// Execute the second query (employee records)
$stmt2 = $conn->prepare($sql_employees);
if (!$stmt2) {
    die(json_encode(["error" => "Error preparing statement (employees): " . $conn->error]));
}
$stmt2->execute();
$result2 = $stmt2->get_result();
$employeeData = $result2->fetch_assoc();
$stmt2->close();

// Prepare JSON response
$response = [
    "total_enrollees" => $schoolData ? number_format((int) $schoolData['total_enrollees']) : 0,
    "total_employees" => $employeeData ? number_format((int) $employeeData['total_employees']) : 0
];

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
