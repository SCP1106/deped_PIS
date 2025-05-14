<?php

include '../Connect/dataDB.php';

header('Content-Type: application/json');

if (isset($_GET['schoolID'])) {
    $schoolID = intval($_GET['schoolID']);

    $query = "SELECT 
                employee_no,
                CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) AS name, 
                position, 
                CONCAT(birthday_yyyy, '-', LPAD(birthday_mm, 2, '0'), '-', LPAD(birthday_dd, 2, '0')) AS birthday,
                TIMESTAMPDIFF(YEAR, 
                    STR_TO_DATE(CONCAT(birthday_yyyy, '-', birthday_mm, '-', birthday_dd), '%Y-%M-%d'), 
                    CURDATE()) AS age
              FROM employee_records
              WHERE school_id = ?
              HAVING age BETWEEN 55 AND 65
              ORDER BY rank_position ASC";  // Filtering only ages 55 to 65

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schoolID);
    $stmt->execute();
    $result = $stmt->get_result();

    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }

    echo json_encode($employees);
}

$conn->close();
?>
