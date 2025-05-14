<?php

include '../Connect/dataDB.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Ensure it's a POST request
    // Decode JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['districtNum'])) { // Expecting 'districtNum' instead of 'district'
        $district = $data['districtNum'];

        // Query to get total employees per school
        $query = "CALL SELECT 
                    s.SchoolID, 
                    s.schoolName, 
                    COUNT(e.employee_no) AS total_employees 
                FROM schoolinfo s 
                LEFT JOIN employee_records e 
                    ON s.schoolID = e.school_id 
                WHERE s.LD_Num = ?  -- Filtering by LD_Num
                GROUP BY s.SchoolID, s.schoolName;";

        $stmt = $conn->prepare($query);
        
        // Assuming districtNum is an integer
        $stmt->bind_param("i", $district);

        $stmt->execute();
        $result = $stmt->get_result();

        $employees = [];
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }

        echo json_encode($employees);
    } else {
        echo json_encode(["error" => "Missing districtNum parameter"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method. Use POST."]);
}

?>
