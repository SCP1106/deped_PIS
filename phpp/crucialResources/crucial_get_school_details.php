<?php
include '../Connect/dataDB.php';

header('Content-Type: application/json');

if (!isset($_GET['schoolID']) || !isset($_GET['resource_type'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$schoolID = mysqli_real_escape_string($conn, $_GET['schoolID']);
$resource_type = mysqli_real_escape_string($conn, $_GET['resource_type']);

try {
    $query = "
        SELECT 
            kinder_needs, kinder_excess, kinder_total,
            grade16_needs, grade16_excess, grade16_total,
            sped_needs, sped_excess, sped_total,
            jhs_needs, jhs_excess, jhs_total,
            shs_needs, shs_excess, shs_total
        FROM school_resources 
        WHERE schoolID = ? AND resource_type = ?
    ";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $schoolID, $resource_type);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        // Return default values if no data found
        $defaultData = [
            'kinder_needs' => 0, 'kinder_excess' => 0, 'kinder_total' => 0,
            'grade16_needs' => 0, 'grade16_excess' => 0, 'grade16_total' => 0,
            'sped_needs' => 0, 'sped_excess' => 0, 'sped_total' => 0,
            'jhs_needs' => 0, 'jhs_excess' => 0, 'jhs_total' => 0,
            'shs_needs' => 0, 'shs_excess' => 0, 'shs_total' => 0
        ];
        echo json_encode(['success' => true, 'data' => $defaultData]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn);
?>