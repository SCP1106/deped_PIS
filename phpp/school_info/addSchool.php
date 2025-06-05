<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
require_once '../Connect/dataDB.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    // Get form data
    $schoolId = $_POST['schoolId'] ?? '';
    $schoolName = $_POST['schoolName'] ?? '';
    $schoolType = $_POST['schoolType'] ?? '';
    $schoolPopulation = $_POST['schoolPopulation'] ?? 0;
    $schoolDistrict = $_POST['schoolDistrict'] ?? '';
    $schoolPrincipal = $_POST['schoolPrincipal'] ?? '';
    $contactNumber = $_POST['contactNumber'] ?? '';
    $emailAddress = $_POST['emailAddress'] ?? '';
    $principalAge = $_POST['principalAge'] ?? null;
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $regionId = $_POST['regionId'] ?? '';
    $cityId = $_POST['cityId'] ?? '';
    $barangayId = $_POST['barangayId'] ?? '';
    $landMark = $_POST['landMark'] ?? '';

    // Validate required fields
    if (empty($schoolId) || empty($schoolName) || empty($schoolType)) {
        throw new Exception('School ID, School Name, and Type are required');
    }

    // Start transaction
    $conn->beginTransaction();

    // Check if school ID already exists
    $checkStmt = $conn->prepare("SELECT SchoolID FROM schoolinfo WHERE SchoolID = ?");
    $checkStmt->execute([$schoolId]);
    if ($checkStmt->rowCount() > 0) {
        throw new Exception('School ID already exists');
    }

    // Insert into schooladd table first to get address_id
    $addressStmt = $conn->prepare("
        INSERT INTO schooladd (region_id, city_id, barangay_code, street, landmark) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $addressStmt->execute([$regionId, $cityId, $barangayId, '', $landMark]);
    $addressId = $conn->lastInsertId();

    // Insert into schoolcoor table to get schoolCoorID
    $coordStmt = $conn->prepare("
        INSERT INTO schoolcoor (SchoolID, latitude, longitude, city_id) 
        VALUES (?, ?, ?, ?)
    ");
    $coordStmt->execute([$schoolId, $latitude, $longitude, $cityId]);
    $schoolCoorId = $conn->lastInsertId();

    // Insert into schoolinfo table
    $schoolStmt = $conn->prepare("
        INSERT INTO schoolinfo (
            SchoolID, SchoolName, SchoolPrevName, Institution, MotherSchool, 
            DateEstab, schoolCoorID, ClassOrgNum, SchoolTypeNum, LD_Num, 
            CDNum, SubClassNum, DivisionNum, address_id, ImplementingUnit, 
            CurricularOffer
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $schoolStmt->execute([
        $schoolId,
        $schoolName,
        null, // SchoolPrevName
        $schoolType,
        null, // MotherSchool
        date('Y-m-d'), // DateEstab
        $schoolCoorId,
        null, // ClassOrgNum
        null, // SchoolTypeNum
        null, // LD_Num
        null, // CDNum
        null, // SubClassNum
        null, // DivisionNum
        $addressId,
        null, // ImplementingUnit
        null  // CurricularOffer
    ]);

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'School added successfully',
        'school_id' => $schoolId,
        'address_id' => $addressId,
        'coord_id' => $schoolCoorId
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
