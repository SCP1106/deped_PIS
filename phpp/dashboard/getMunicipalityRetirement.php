<?php

include '../Connect/dataDB.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['districtNum'], $data['municipality'])) {
        $district = $data['districtNum'];
        $municipality = $data['municipality'];

        // Query to get employees per school based on district and municipality
        $query = "
            SELECT 
                s.SchoolID, 
                s.schoolName, 
                COUNT(e.employee_no) AS total_retirement 
            FROM schoolinfo s 
            LEFT JOIN employee_records e 
                ON s.schoolID = e.school_id 
            WHERE s.LD_Num = ? AND e.district = ?
             AND TIMESTAMPDIFF(
                    YEAR, 
                    STR_TO_DATE(
                        CONCAT(e.BIRTHDAY_YYYY, '-', e.BIRTHDAY_MM, '-', e.BIRTHDAY_DD), 
                        '%Y-%M-%d'
                    ), 
                    CURDATE()
                ) BETWEEN 55 AND 65
            GROUP BY s.SchoolID, s.schoolName;
        ";

        $stmt = $conn->prepare($query);
        
        // Bind both districtNum and municipality
        $stmt->bind_param("is", $district, $municipality); // "is" means integer and string

        $stmt->execute();
        $result = $stmt->get_result();

        $employees = [];
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }

        echo json_encode($employees);
    } else {
        echo json_encode(["error" => "Missing districtNum or municipality parameter"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method. Use POST."]);
}

?>
