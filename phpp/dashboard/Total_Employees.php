<?php

include '../Connect/dataDB.php';

$sql = "SELECT s.LD_Num AS district_number, COUNT(e.employee_no) AS employee_count 
        FROM employee_records e
        JOIN schoolinfo s ON e.school_id = s.SchoolID
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