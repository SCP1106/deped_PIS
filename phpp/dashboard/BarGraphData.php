<?php
session_start();
include '../Connect/dataDB.php';

if (!$conn) {
    die(json_encode(["error" => "Database connection failed: " . mysqli_connect_error()]));
}

// SQL Query to get total enrollees per year
$sql_enrollees = "SELECT 
  SchoolYear,
  COALESCE(SUM(ELEM_TOTAL_M + JHS_TOTAL_M + FS_SHS_TTL_M + SS_SHS_TTL_M), 0) AS total_male,
  COALESCE(SUM(ELEM_TOTAL_F + JHS_TOTAL_F + FS_SHS_TTL_F + SS_SHS_TTL_F), 0) AS total_female,
  COALESCE(SUM(ALL_GRADE_TTL), 0) AS total_enrollees
FROM enrollment_data
GROUP BY SchoolYear
ORDER BY SchoolYear ASC;";

// Prepare and execute query
$stmt = $conn->prepare($sql_enrollees);
if (!$stmt) {
    die(json_encode(["error" => "Error preparing statement: " . $conn->error]));
}
$stmt->execute();
$result = $stmt->get_result();

$enrollees_by_year = []; // Initialize array

while ($row = $result->fetch_assoc()) {
    $enrollees_by_year[] = [
        "enrollment_year" => $row['SchoolYear'],
        "total_male" => intval($row['total_male']),
        "total_female" => intval($row['total_female']),
        "total_enrollees" => intval($row['total_enrollees'])
    ];
}

$stmt->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($enrollees_by_year);
?>
