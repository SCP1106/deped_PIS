<?php
include '../Connect/dataDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schoolId = $_POST['update-schoolId'] ?? null;
    $yearEstablished = $_POST['update-yearEstablished'] ?? null;
    $lotInfo = $_POST['update-lotInfo'] ?? null;
    $totalLandArea = $_POST['update-totalLandArea'] ?? null;
    $classification = $_POST['update-classification'] ?? null;
    $transferInstrument = $_POST['update-transferInstrument'] ?? null;
    $titleNumber = $_POST['update-titleNumber'] ?? null;
    $proclamationNumber = $_POST['update-proclamationNumber'] ?? null;
    $latitude = $_POST['update-latitude'] ?? null;
    $longitude = $_POST['update-longitude'] ?? null;

    if (!$schoolId) {
        echo json_encode(['success' => false, 'message' => 'School ID is required.']);
        exit;
    }

    function getCurrentFilePath($schoolId, $column) {
        global $conn;
        $stmt = mysqli_prepare($conn, "SELECT $column FROM landowner_tb WHERE school_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $schoolId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $filePath);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return $filePath;
    }

    // Handle File Uploads
    $ownershipPath = getCurrentFilePath($schoolId, 'ownership_doc');
    $sitePlanPath = getCurrentFilePath($schoolId, 'siteplan_doc');

    $ownershipDir = '../uploads/ownership_docs/';
    $sitePlanDir = '../uploads/site_plan_docs/';

    if (!is_dir($ownershipDir)) mkdir($ownershipDir, 0777, true);
    if (!is_dir($sitePlanDir)) mkdir($sitePlanDir, 0777, true);

    if (isset($_FILES['update-ownershipDoc']) && $_FILES['update-ownershipDoc']['size'] > 0) {
        $ownershipPath = $ownershipDir . time() . '_' . basename($_FILES['update-ownershipDoc']['name']);
        if (!move_uploaded_file($_FILES['update-ownershipDoc']['tmp_name'], $ownershipPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload ownership document.']);
            exit;
        }
    }

    if (isset($_FILES['update-sitePlanDoc']) && $_FILES['update-sitePlanDoc']['size'] > 0) {
        $sitePlanPath = $sitePlanDir . time() . '_' . basename($_FILES['update-sitePlanDoc']['name']);
        if (!move_uploaded_file($_FILES['update-sitePlanDoc']['tmp_name'], $sitePlanPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload site plan document.']);
            exit;
        }
    }

    // Update landowner_tb
    $stmt = mysqli_prepare($conn, "UPDATE landowner_tb SET year_estab = ?, lot_info = ?, land_area = ?, classification = ?, transfer_doc = ?, title_no = ?, pro_no = ?, ownership_doc = ?, siteplan_doc = ? WHERE school_id = ?");
    mysqli_stmt_bind_param($stmt, "issssssssi", (int)$yearEstablished, $lotInfo, $totalLandArea, $classification, $transferInstrument, $titleNumber, $proclamationNumber, $ownershipPath, $sitePlanPath, (int)$schoolId);
    $successSchools = mysqli_stmt_execute($stmt);

    if (!$successSchools) {
        error_log('Error updating landowner_tb: ' . mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);

    // Update schoolcoor
    $stmtCoor = mysqli_prepare($conn, "UPDATE schoolcoor SET latitude = ?, longitude = ? WHERE SchoolID = ?");
    mysqli_stmt_bind_param($stmtCoor, "ddi", (float)$latitude, (float)$longitude, (int)$schoolId);
    $successCoor = mysqli_stmt_execute($stmtCoor);

    if (!$successCoor) {
        error_log('Error updating schoolcoor: ' . mysqli_error($conn));
    }

    mysqli_stmt_close($stmtCoor);
    mysqli_close($conn);

    if ($successSchools && $successCoor) {
        echo json_encode(['success' => true, 'message' => 'School data updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update school data.']);
    }
}
?>
