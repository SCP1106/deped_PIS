<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include '../Connect/dataDB.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data - only editable fields
$schoolId = mysqli_real_escape_string($conn, $_POST['schoolId']);
$schoolName = mysqli_real_escape_string($conn, $_POST['schoolName']);
$institutionType = mysqli_real_escape_string($conn, $_POST['institutionType']);
$curricularOffer = mysqli_real_escape_string($conn, $_POST['curricularOffer']);
$schoolDistrict = mysqli_real_escape_string($conn, $_POST['schoolDistrict']);
$contactNumber = mysqli_real_escape_string($conn, $_POST['contactNumber']);
$emailAddress = mysqli_real_escape_string($conn, $_POST['emailAddress']);
$latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
$longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
$regionId = mysqli_real_escape_string($conn, $_POST['regionId']);
$provinceId = mysqli_real_escape_string($conn, $_POST['provinceId']);
$cityId = mysqli_real_escape_string($conn, $_POST['cityId']);
$barangayId = mysqli_real_escape_string($conn, $_POST['barangayId']);
$landMark = mysqli_real_escape_string($conn, $_POST['landMark']);

// Note: schoolPopulation, schoolPrincipal, and principalAge are NOT updated
// as they are disabled fields in the update form

try {
    mysqli_begin_transaction($conn);
    
    // Update school basic information (excluding disabled fields)
    $updateSchoolSql = "UPDATE schoolinfo SET 
                        SchoolName = '$schoolName',
                        Institution = '$institutionType',
                        CurricularOffer = '$curricularOffer',
                        district = '$schoolDistrict',
                        contact_no = '$contactNumber',
                        email = '$emailAddress'
                        WHERE SchoolID = '$schoolId'";
    
    if (!mysqli_query($conn, $updateSchoolSql)) {
        throw new Exception("Failed to update school information: " . mysqli_error($conn));
    }
    
    // Update address information
    $updateAddressSql = "UPDATE schooladd sa 
                         JOIN schoolinfo si ON sa.address_id = si.address_id 
                         SET sa.region_id = '$regionId',
                             sa.city_id = '$cityId',
                             sa.barangay_code = '$barangayId',
                             sa.landmark = '$landMark'
                         WHERE si.SchoolID = '$schoolId'";
    
    if (!mysqli_query($conn, $updateAddressSql)) {
        throw new Exception("Failed to update address information: " . mysqli_error($conn));
    }
    
    // Update coordinates
    $updateCoordsSql = "UPDATE schoolcoor sc 
                        JOIN schoolinfo si ON sc.schoolCoorID = si.schoolCoorID 
                        SET sc.latitude = '$latitude',
                            sc.longitude = '$longitude'
                        WHERE si.SchoolID = '$schoolId'";
    
    if (!mysqli_query($conn, $updateCoordsSql)) {
        throw new Exception("Failed to update coordinates: " . mysqli_error($conn));
    }
    
    mysqli_commit($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'School information updated successfully'
    ]);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>