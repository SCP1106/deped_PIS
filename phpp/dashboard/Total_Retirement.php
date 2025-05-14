<?php

include '../Connect/dataDB.php';

$sql = "SELECT s.LD_Num AS district_number, COUNT(e.employee_no) AS employee_count 
        FROM employee_records e
        JOIN schoolinfo s ON e.school_id = s.SchoolID
        WHERE TIMESTAMPDIFF(YEAR, 
                STR_TO_DATE(CONCAT(e.BIRTHDAY_YYYY, '-', e.BIRTHDAY_MM, '-', e.BIRTHDAY_DD), '%Y-%M-%d'), 
                CURDATE()) 
              BETWEEN 55 AND 65
        GROUP BY s.LD_Num";

$result = $conn->query($sql);

if ($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['district_number']] = $row['employee_count'];
    }
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to fetch data"]);
}

$conn->close();
?>