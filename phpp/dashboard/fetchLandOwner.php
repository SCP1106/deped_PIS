<?php

include '../Connect/dataDB.php';

header('Content-Type: application/json');

$district = isset($_GET['district']) ? $_GET['district'] : null;
if (!$district) {
    echo json_encode(["error" => "Missing district parameter"]);
    exit;
}

// No WHERE clause in SQL
$sql = "SELECT 
            s.school_id, 
            si.SchoolName, 
            s.year_estab, 
            s.land_area, 
            s.classification, 
            s.title_no, 
            s.lot_info, 
            s.transfer_doc, 
            s.proc_no, 
            s.ownership_doc, 
            s.siteplan_doc, 
            sc.longitude, 
            sc.latitude,
            si.LD_Num,
            cd.CDName
        FROM landowner_tb s
        INNER JOIN schoolinfo si ON s.school_id = si.schoolID
        LEFT JOIN schoolcoor sc ON s.school_id = sc.SchoolID
        INNER JOIN citydistrict cd ON si.CDNum = cd.CDNum";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL Error: " . $conn->error]);
    exit;
}

// NO BIND_PARAM here
$stmt->execute();
$result = $stmt->get_result();

$schools = [];
while ($row = $result->fetch_assoc()) {
    $schools[] = $row;
}

echo json_encode($schools, JSON_UNESCAPED_UNICODE);

?>
