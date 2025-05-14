<?php
include '../Connect/dataDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schoolId = $_POST['update-schoolId'] ?? null;
    if (!$schoolId) {
        echo json_encode(['success' => false, 'message' => 'School ID is required.']);
        exit;
    }

    $fields = [
        'year_estab' => $_POST['update-yearEstablished'] ?? null,
        'lot_info' => $_POST['update-lotInfo'] ?? null,
        'land_area' => $_POST['update-totalLandArea'] ?? null,
        'classification' => $_POST['update-classification'] ?? null,
        'transfer_doc' => $_POST['update-transferInstrument'] ?? null,
        'title_no' => $_POST['update-titleNumber'] ?? null,
        'proc_no' => $_POST['update-proclamationNumber'] ?? null,
    ];

    error_log('Hello Daw');

    function uploadFile($fileKey, $directory) {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['size'] > 0) {
            if (!is_dir(filename: $directory)) mkdir($directory, 0777, true);
            $filePath = $directory . time() . '_' . basename($_FILES[$fileKey]['name']);
            if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $filePath)) {
                echo json_encode(['success' => false, 'message' => 'File upload failed.', 'error' => error_get_last()]);
                exit;
            }
            return $filePath;
        }
        return null;
    }

    $ownershipDoc = uploadFile('update-ownershipDoc', '../uploads/ownership_docs/');
    $siteplanDoc = uploadFile('update-sitePlanDoc', '../uploads/site_plan_docs/');

    if ($ownershipDoc) $fields['ownership_doc'] = $ownershipDoc;
    if ($siteplanDoc) $fields['siteplan_doc'] = $siteplanDoc;

    // Build SQL query using prepared statements
    $updateQuery = "UPDATE landowner_tb SET ";
    $updateFields = [];
    $params = [];

    foreach ($fields as $key => $value) {
        if ($value !== null) {
            $updateFields[] = "$key = ?";
            $params[] = $value;
        }
    }

    if (empty($updateFields)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update.']);
        exit;
    }

    $updateQuery .= implode(", ", $updateFields) . " WHERE school_id = ?";
    $params[] = $schoolId;

    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);

    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        $latitude = (float)($_POST['update-latitude'] ?? 0);
        $longitude = (float)($_POST['update-longitude'] ?? 0);
        $coorQuery = "UPDATE schoolcoor SET latitude = ?, longitude = ? WHERE SchoolID = ?";
        $stmtCoor = mysqli_prepare($conn, $coorQuery);
        mysqli_stmt_bind_param($stmtCoor, 'dds', $latitude, $longitude, $schoolId);

        if (mysqli_stmt_execute($stmtCoor)) {
            echo json_encode(['success' => true, 'message' => 'School data updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update coordinates.', 'error' => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No data was updated. School ID may not exist or no changes were made.', 'error' => mysqli_error($conn)]);
    }

    mysqli_close($conn);
}
?>
