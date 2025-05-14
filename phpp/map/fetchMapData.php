<?php
ob_start();
// Include database connection
include '../Connect/dataDB.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the POST data is set
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assuming 'mapDataCode' is sent from the JS fetch request
    $mapDataCode = $_POST['data'];

    // Convert to uppercase and remove spaces
    $mapDataCode = strtoupper(str_replace(' ', '', $mapDataCode));

    // Prepare your SQL query with the POST data (use prepared statements for security)
    $stmt = $conn->prepare("SELECT * FROM mapdata WHERE mapDataCode = ?");
    $stmt->bind_param("s", $mapDataCode);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $jsonArray = [];
        while ($row = $result->fetch_assoc()) {
            // Decode JSON data into PHP associative array
            $jsonData = json_decode($row['MapData'], true);
           echo $row['MapData'];
            $jsonArray[] = $jsonData;
        }

        // Output as JSON response
       // header('Content-Type: application/json');
       // echo json_encode($jsonArray, JSON_PRETTY_PRINT);
    } else {
        echo "No data found";
    }

    $stmt->close();
}

$conn->close();
ob_end_flush();
?>