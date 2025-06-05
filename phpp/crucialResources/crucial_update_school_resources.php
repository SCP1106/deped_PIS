<?php
include '../Connect/dataDB.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['schoolID']) || !isset($input['resource_type'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$schoolID = mysqli_real_escape_string($conn, $input['schoolID']);
$resource_type = mysqli_real_escape_string($conn, $input['resource_type']);

try {
    $query = "
        INSERT INTO school_resources (
            schoolID, resource_type,
            kinder_needs, kinder_excess, kinder_total,
            grade16_needs, grade16_excess, grade16_total,
            sped_needs, sped_excess, sped_total,
            jhs_needs, jhs_excess, jhs_total,
            shs_needs, shs_excess, shs_total
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            kinder_needs = VALUES(kinder_needs),
            kinder_excess = VALUES(kinder_excess),
            kinder_total = VALUES(kinder_total),
            grade16_needs = VALUES(grade16_needs),
            grade16_excess = VALUES(grade16_excess),
            grade16_total = VALUES(grade16_total),
            sped_needs = VALUES(sped_needs),
            sped_excess = VALUES(sped_excess),
            sped_total = VALUES(sped_total),
            jhs_needs = VALUES(jhs_needs),
            jhs_excess = VALUES(jhs_excess),
            jhs_total = VALUES(jhs_total),
            shs_needs = VALUES(shs_needs),
            shs_excess = VALUES(shs_excess),
            shs_total = VALUES(shs_total),
            updated_at = CURRENT_TIMESTAMP
    ";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssiiiiiiiiiiiiiii", 
        $schoolID, $resource_type,
        $input['kinder']['needs'], $input['kinder']['excess'], $input['kinder']['total'],
        $input['grade16']['needs'], $input['grade16']['excess'], $input['grade16']['total'],
        $input['sped']['needs'], $input['sped']['excess'], $input['sped']['total'],
        $input['jhs']['needs'], $input['jhs']['excess'], $input['jhs']['total'],
        $input['shs']['needs'], $input['shs']['excess'], $input['shs']['total']
    );
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Resources updated successfully']);
    } else {
        throw new Exception("Failed to update resources: " . mysqli_error($conn));
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn);
?>