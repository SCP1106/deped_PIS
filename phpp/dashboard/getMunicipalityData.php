<?php
header('Content-Type: application/json');

include '../Connect/dataDB.php';
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit();
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['districtNum']) || !isset($data['municipalities'])) {
    echo json_encode(["success" => false, "message" => "Invalid input data."]);
    exit();
}

$districtNum = (int)$data['districtNum'];
$municipalities = $data['municipalities'];
$responseData = [];

// Fetch average years of service for the district
$sql = "SELECT s.LD_Num AS district_number, 
               AVG(YEAR(CURDATE()) - APPT_YYYY) AS avg_years_of_service 
        FROM employee_records e
        JOIN schoolinfo s ON e.school_id = s.SchoolID
        WHERE s.LD_Num = ?
        GROUP BY s.LD_Num";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $districtNum);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$avgServiceYears = $row ? round($row['avg_years_of_service'], 2) : 0.0;
$stmt->close();

// Fetch data for each municipality
foreach ($municipalities as $municipality) {
    // Get employee count
    $stmt = $conn->prepare("SELECT COUNT(*) AS employees FROM employee_records e JOIN citydistrict c ON e.district = c.CDName 
                                    WHERE c.LD_Num = ? AND c.CDName = ?");
    $stmt->bind_param("is", $districtNum, $municipality);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $employees = $row ? (int)$row['employees'] : 0;
    $stmt->close();

    // Get retirement count based on age between 55 and 65
    $stmt = $conn->prepare("SELECT COUNT(*) AS retirement FROM employee_records e JOIN citydistrict c ON e.district = c.CDName 
                                    WHERE c.LD_Num = ? AND c.CDName = ? 
                                    AND TIMESTAMPDIFF(YEAR, STR_TO_DATE(CONCAT(e.BIRTHDAY_YYYY, '-', e.BIRTHDAY_MM, '-', e.BIRTHDAY_DD), '%Y-%M-%d'), CURDATE()) 
                                    BETWEEN 55 AND 65");
    $stmt->bind_param("is", $districtNum, $municipality);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $retirement = $row ? (int)$row['retirement'] : 0;
    $stmt->close();

    $responseData[$municipality] = [
        'employees' => $employees,
        'retirement' => $retirement,
        'service' => $avgServiceYears,
    ];
}

$conn->close();

// Return response
echo json_encode(["success" => true, "data" => $responseData]);
?>
