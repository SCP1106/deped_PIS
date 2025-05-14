<?php
// get_positions.php

include '../Connect/dataDB.php'; // adjust as needed

header('Content-Type: application/json');

$query = "SELECT DISTINCT position FROM employee_records ORDER BY position ASC";
$result = mysqli_query($conn, $query);

$positions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $positions[] = $row['position'];
}

echo json_encode($positions);
?>