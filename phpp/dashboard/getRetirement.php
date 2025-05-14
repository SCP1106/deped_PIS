<?php

include '../Connect/dataDB.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON input correctly
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['districtNum'])) { 
        $district = $data['districtNum'];

        $query = "SELECT 
        s.SchoolID, 
        s.schoolName, 
        COUNT(e.employee_no) AS near_retirement_count 
    FROM schoolinfo s 
    LEFT JOIN employee_records e 
        ON s.schoolID = e.school_id 
    WHERE s.LD_Num = ?
        AND TIMESTAMPDIFF(
            YEAR, 
            STR_TO_DATE(
                CONCAT(e.BIRTHDAY_YYYY, '-', e.BIRTHDAY_MM, '-', e.BIRTHDAY_DD), 
                '%Y-%M-%d'
            ), 
            CURDATE()
        ) BETWEEN 55 AND 65
    GROUP BY s.SchoolID, s.schoolName";
        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $district);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $employees = $result->fetch_all(MYSQLI_ASSOC); // Fetch all results
            echo json_encode($employees);
        } else {
            echo json_encode(["error" => "Query execution failed"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Missing districtNum parameter"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method. Use POST."]);
}

?>
