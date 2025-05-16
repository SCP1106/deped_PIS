<?php
// Database connection
ob_start();

include '../Connect/dataDB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['data'])) {
    // Sanitize the input
    $data = strtoupper(str_replace(' ', '', $_POST['data']));

    // Prepare the query to get city ID
    $stmt = $conn->prepare("SELECT city_id FROM muncity WHERE city_name = ?");
    $stmt->bind_param("s", $data);
    $stmt->execute();
    $stmt->store_result();

    $cityID = null;
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($cityID);
        $stmt->fetch();
    }

    $stmt->close();

    if ($cityID !== null) {
        // Query for schools in the specific city
        $stmt = $conn->prepare("SELECT si.schoolID, si.schoolName, b.barangay_name, cd.CDName as district
            FROM schoolinfo si
            JOIN schooladd sa ON si.address_id = sa.address_id
            JOIN barangay b ON sa.barangay_code = b.barangay_code
            JOIN citydistrict cd ON si.CDNum = cd.CDNum
            WHERE sa.city_id = ?");
        $stmt->bind_param("i", $cityID);
    } else {
        // Query for all schools
        $stmt = $conn->prepare("SELECT si.schoolID, si.schoolName, b.barangay_name, cd.CDName as district
            FROM schoolinfo si
            JOIN schooladd sa ON si.address_id = sa.address_id
            JOIN barangay b ON sa.barangay_code = b.barangay_code
            JOIN citydistrict cd ON si.CDNum = cd.CDNum");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    
    echo json_encode($data);
   // print_r($data);
    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid or missing data"]);
}

// Close the connection
$conn->close();
ob_end_flush();
?>
