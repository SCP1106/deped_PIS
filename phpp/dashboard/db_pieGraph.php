<?php

include '../Connect/dataDB.php';


// Query to count schools by type
$sql = "SELECT 
            SUM(CASE WHEN Institution = 'Public' THEN 1 ELSE 0 END) AS public,
            SUM(CASE WHEN Institution = 'Private' THEN 1 ELSE 0 END) AS private,
            SUM(CASE WHEN Institution = 'Semi Private' THEN 1 ELSE 0 END) AS semi_private
        FROM schoolinfo";  // Change 'schools' to your actual table name

$result = $conn->query($sql);
$data = $result->fetch_assoc();

header('Content-Type: application/json');

echo json_encode($data);

$conn->close();

?>
