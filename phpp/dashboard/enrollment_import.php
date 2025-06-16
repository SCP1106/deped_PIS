<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include your existing database connection
require_once '../Connect/dataDB.php';

// Check if connection exists
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
if (empty($_POST['schoolYear']) || empty($_POST['schoolType']) || empty($_POST['importType'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$schoolYear = trim($_POST['schoolYear']);
$schoolType = trim($_POST['schoolType']);
$importType = $_POST['importType'];
$schoolID = isset($_POST['schoolID']) ? trim($_POST['schoolID']) : null;

// Validate specific school import
if ($importType === 'specific' && empty($schoolID)) {
    echo json_encode(['success' => false, 'message' => 'School ID is required for specific school import']);
    exit;
}

// Validate file upload
if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File upload failed']);
    exit;
}

$csvFile = $_FILES['csvFile']['tmp_name'];
$fileExtension = strtolower(pathinfo($_FILES['csvFile']['name'], PATHINFO_EXTENSION));

if ($fileExtension !== 'csv') {
    echo json_encode(['success' => false, 'message' => 'Only CSV files are allowed']);
    exit;
}

try {
    // Start transaction
    mysqli_autocommit($conn, FALSE);
    
    // Read and process CSV file
    $csvData = [];
    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        $header = fgetcsv($handle); // Skip header row if exists
        
        while (($row = fgetcsv($handle)) !== FALSE) {
            if (count($row) >= 79) { // Ensure we have all required columns
                $csvData[] = $row;
            }
        }
        fclose($handle);
    }
    
    if (empty($csvData)) {
        throw new Exception('No valid data found in CSV file');
    }
    
    // Handle different import types
    if ($importType === 'whole') {
        // Delete all records with the same school year
        $deleteStmt = mysqli_prepare($conn, "DELETE FROM enrollment_data WHERE SchoolYear = ?");
        mysqli_stmt_bind_param($deleteStmt, "s", $schoolYear);
        mysqli_stmt_execute($deleteStmt);
        $deletedCount = mysqli_stmt_affected_rows($deleteStmt);
        mysqli_stmt_close($deleteStmt);
        
        // Insert new records
        $insertedCount = insertEnrollmentData($conn, $csvData, $schoolYear, $schoolType);
        
        $message = "Successfully updated whole table: Deleted $deletedCount existing records and inserted $insertedCount new records for school year $schoolYear";
        
    } else { // specific school
        // Check if record exists for this school and year
        $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM enrollment_data WHERE schoolID = ? AND SchoolYear = ?");
        mysqli_stmt_bind_param($checkStmt, "ss", $schoolID, $schoolYear);
        mysqli_stmt_execute($checkStmt);
        $result = mysqli_stmt_get_result($checkStmt);
        $exists = mysqli_fetch_row($result)[0] > 0;
        mysqli_stmt_close($checkStmt);
        
        if ($exists) {
            // Update existing record
            $updatedCount = updateSpecificSchool($conn, $csvData, $schoolID, $schoolYear, $schoolType);
            $message = "Successfully updated $updatedCount record(s) for School ID: $schoolID, School Year: $schoolYear";
        } else {
            // Insert new record
            $insertedCount = insertSpecificSchool($conn, $csvData, $schoolID, $schoolYear, $schoolType);
            $message = "Successfully inserted $insertedCount new record(s) for School ID: $schoolID, School Year: $schoolYear";
        }
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    echo json_encode(['success' => true, 'message' => $message]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Error processing data: ' . $e->getMessage()]);
}

function insertEnrollmentData($conn, $csvData, $schoolYear, $schoolType) {
    $sql = "INSERT INTO enrollment_data (
        enrollment_data_ID, schoolID, schoolType, SchoolYear, K_M, K_F, G1_M, G1_F, G2_M, G2_F, 
        G3_M, G3_F, G4_M, G4_F, G5_M, G5_F, G6_M, G6_F, ELEM_NG_M, ELEM_NG_F, ELEM_TOTAL_M, 
        ELEM_TOTAL_F, G7_M, G7_F, G8_M, G8_F, G9_M, G9_F, G10_M, G10_F, JHS_NG_M, JHS_NG_F, 
        JHS_TOTAL_M, JHS_TOTAL_F, FS_G11_ACAD_M, FS_G11_ACAD_F, FS_G11_TVL_M, FS_G11_TVL_F, 
        FS_G11_SPORT_M, FS_G11_SPORT_F, FS_G11_ART_M, FS_G11_ART_F, FS_G11_UNQ_M, FS_G11_UNQ_F, 
        FS_G12_ACAD_M, FS_G12_ACAD_F, FS_G12_TVL_M, FS_G12_TVL_F, FS_G12_SPORT_M, FS_G12_SPORT_F, 
        FS_G12_ART_M, FS_G12_ART_F, FS_G12_UNQ_M, FS_G12_UNQ_F, FS_SHS_TTL_M, FS_SHS_TTL_F, 
        SS_G11_ACAD_M, SS_G11_ACAD_F, SS_G11_TVL_M, SS_G11_TVL_F, SS_G11_SPORT_M, SS_G11_SPORT_F, 
        SS_G11_ART_M, SS_G11_ART_F, SS_G11_UNQ_M, SS_G11_UNQ_F, SS_G12_ACAD_M, SS_G12_ACAD_F, 
        SS_G12_TVL_M, SS_G12_TVL_F, SS_G12_SPORT_M, SS_G12_SPORT_F, SS_G12_ART_M, SS_G12_ART_F, 
        SS_G12_UNQ_M, SS_G12_UNQ_F, SS_SHS_TTL_M, SS_SHS_TTL_F, ALL_GRADE_TTL
    ) VALUES (" . str_repeat('?,', 78) . "?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    $insertedCount = 0;
    
    foreach ($csvData as $row) {
        // Prepare data array with school year and type
        $data = array_merge(
            [$row[0]], // enrollment_data_ID
            [$row[1]], // schoolID
            [$schoolType], // schoolType (from form)
            [$schoolYear], // SchoolYear (from form)
            array_slice($row, 4) // Rest of the data starting from K_M
        );
        
        // Create type string for bind_param (all strings for simplicity)
        $types = str_repeat('s', count($data));
        
        mysqli_stmt_bind_param($stmt, $types, ...$data);
        
        if (mysqli_stmt_execute($stmt)) {
            $insertedCount++;
        }
    }
    
    mysqli_stmt_close($stmt);
    return $insertedCount;
}

function updateSpecificSchool($conn, $csvData, $schoolID, $schoolYear, $schoolType) {
    // Find the row for the specific school
    $targetRow = null;
    foreach ($csvData as $row) {
        if ($row[1] == $schoolID) { // Assuming schoolID is in column 1
            $targetRow = $row;
            break;
        }
    }
    
    if (!$targetRow) {
        throw new Exception("School ID $schoolID not found in CSV data");
    }
    
    $sql = "UPDATE enrollment_data SET 
        schoolType = ?, K_M = ?, K_F = ?, G1_M = ?, G1_F = ?, G2_M = ?, G2_F = ?, G3_M = ?, G3_F = ?, 
        G4_M = ?, G4_F = ?, G5_M = ?, G5_F = ?, G6_M = ?, G6_F = ?, ELEM_NG_M = ?, ELEM_NG_F = ?, 
        ELEM_TOTAL_M = ?, ELEM_TOTAL_F = ?, G7_M = ?, G7_F = ?, G8_M = ?, G8_F = ?, G9_M = ?, G9_F = ?, 
        G10_M = ?, G10_F = ?, JHS_NG_M = ?, JHS_NG_F = ?, JHS_TOTAL_M = ?, JHS_TOTAL_F = ?, 
        FS_G11_ACAD_M = ?, FS_G11_ACAD_F = ?, FS_G11_TVL_M = ?, FS_G11_TVL_F = ?, FS_G11_SPORT_M = ?, 
        FS_G11_SPORT_F = ?, FS_G11_ART_M = ?, FS_G11_ART_F = ?, FS_G11_UNQ_M = ?, FS_G11_UNQ_F = ?, 
        FS_G12_ACAD_M = ?, FS_G12_ACAD_F = ?, FS_G12_TVL_M = ?, FS_G12_TVL_F = ?, FS_G12_SPORT_M = ?, 
        FS_G12_SPORT_F = ?, FS_G12_ART_M = ?, FS_G12_ART_F = ?, FS_G12_UNQ_M = ?, FS_G12_UNQ_F = ?, 
        FS_SHS_TTL_M = ?, FS_SHS_TTL_F = ?, SS_G11_ACAD_M = ?, SS_G11_ACAD_F = ?, SS_G11_TVL_M = ?, 
        SS_G11_TVL_F = ?, SS_G11_SPORT_M = ?, SS_G11_SPORT_F = ?, SS_G11_ART_M = ?, SS_G11_ART_F = ?, 
        SS_G11_UNQ_M = ?, SS_G11_UNQ_F = ?, SS_G12_ACAD_M = ?, SS_G12_ACAD_F = ?, SS_G12_TVL_M = ?, 
        SS_G12_TVL_F = ?, SS_G12_SPORT_M = ?, SS_G12_SPORT_F = ?, SS_G12_ART_M = ?, SS_G12_ART_F = ?, 
        SS_G12_UNQ_M = ?, SS_G12_UNQ_F = ?, SS_SHS_TTL_M = ?, SS_SHS_TTL_F = ?, ALL_GRADE_TTL = ?
        WHERE schoolID = ? AND SchoolYear = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    // Prepare data array (excluding enrollment_data_ID and schoolID which are not updated)
    $data = array_merge(
        [$schoolType], // schoolType
        array_slice($targetRow, 4), // Data from K_M onwards
        [$schoolID, $schoolYear] // WHERE conditions
    );
    
    // Create type string for bind_param
    $types = str_repeat('s', count($data));
    
    mysqli_stmt_bind_param($stmt, $types, ...$data);
    mysqli_stmt_execute($stmt);
    $affectedRows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    
    return $affectedRows;
}

function insertSpecificSchool($conn, $csvData, $schoolID, $schoolYear, $schoolType) {
    // Find the row for the specific school
    $targetRow = null;
    foreach ($csvData as $row) {
        if ($row[1] == $schoolID) { // Assuming schoolID is in column 1
            $targetRow = $row;
            break;
        }
    }
    
    if (!$targetRow) {
        throw new Exception("School ID $schoolID not found in CSV data");
    }
    
    $sql = "INSERT INTO enrollment_data (
        enrollment_data_ID, schoolID, schoolType, SchoolYear, K_M, K_F, G1_M, G1_F, G2_M, G2_F, 
        G3_M, G3_F, G4_M, G4_F, G5_M, G5_F, G6_M, G6_F, ELEM_NG_M, ELEM_NG_F, ELEM_TOTAL_M, 
        ELEM_TOTAL_F, G7_M, G7_F, G8_M, G8_F, G9_M, G9_F, G10_M, G10_F, JHS_NG_M, JHS_NG_F, 
        JHS_TOTAL_M, JHS_TOTAL_F, FS_G11_ACAD_M, FS_G11_ACAD_F, FS_G11_TVL_M, FS_G11_TVL_F, 
        FS_G11_SPORT_M, FS_G11_SPORT_F, FS_G11_ART_M, FS_G11_ART_F, FS_G11_UNQ_M, FS_G11_UNQ_F, 
        FS_G12_ACAD_M, FS_G12_ACAD_F, FS_G12_TVL_M, FS_G12_TVL_F, FS_G12_SPORT_M, FS_G12_SPORT_F, 
        FS_G12_ART_M, FS_G12_ART_F, FS_G12_UNQ_M, FS_G12_UNQ_F, FS_SHS_TTL_M, FS_SHS_TTL_F, 
        SS_G11_ACAD_M, SS_G11_ACAD_F, SS_G11_TVL_M, SS_G11_TVL_F, SS_G11_SPORT_M, SS_G11_SPORT_F, 
        SS_G11_ART_M, SS_G11_ART_F, SS_G11_UNQ_M, SS_G11_UNQ_F, SS_G12_ACAD_M, SS_G12_ACAD_F, 
        SS_G12_TVL_M, SS_G12_TVL_F, SS_G12_SPORT_M, SS_G12_SPORT_F, SS_G12_ART_M, SS_G12_ART_F, 
        SS_G12_UNQ_M, SS_G12_UNQ_F, SS_SHS_TTL_M, SS_SHS_TTL_F, ALL_GRADE_TTL
    ) VALUES (" . str_repeat('?,', 78) . "?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    // Prepare data array
    $data = array_merge(
        [$targetRow[0]], // enrollment_data_ID
        [$schoolID], // schoolID
        [$schoolType], // schoolType
        [$schoolYear], // SchoolYear
        array_slice($targetRow, 4) // Rest of the data starting from K_M
    );
    
    // Create type string for bind_param
    $types = str_repeat('s', count($data));
    
    mysqli_stmt_bind_param($stmt, $types, ...$data);
    mysqli_stmt_execute($stmt);
    $affectedRows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    
    return $affectedRows;
}
?>