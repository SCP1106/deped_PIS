<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
include '../Connect/dataDB.php';

if (!isset($_GET['school_id'])) {
    echo json_encode(['error' => 'School ID is required']);
    exit;
}

$schoolId = mysqli_real_escape_string($conn, $_GET['school_id']);

// Fetch complete school information with address and coordinates
$sql = "SELECT 
            si.SchoolID,
            si.SchoolName,
            si.SchoolPrevName,
            si.Institution,
            si.MotherSchool,
            si.DateEstab,
            si.ClassOrgNum,
            si.SchoolTypeNum,
            si.LD_Num,
            si.CDNum,
            si.SubClassNum,
            si.DivisionNum,
            si.ImplementingUnit,
            si.CurricularOffer,
            
            sa.region_id,
            sa.city_id,
            sa.barangay_code,
            sa.street,
            sa.landmark,
            
            sc.latitude,
            sc.longitude,
            
            r.region_name,
            r.region_code,
            
            mc.city_name,
            mc.city_code,
            mc.province_id,
            mc.is_city,
            mc.muncityClassID,
            
            b.barangay_name
            
        FROM schoolinfo si
        LEFT JOIN schooladd sa ON si.address_id = sa.address_id
        LEFT JOIN schoolcoor sc ON si.schoolCoorID = sc.schoolCoorID
        LEFT JOIN region r ON sa.region_id = r.region_id
        LEFT JOIN muncity mc ON sa.city_id = mc.city_id
        LEFT JOIN barangay b ON sa.barangay_code = b.barangay_code
        WHERE si.SchoolID = '$schoolId'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    mysqli_close($conn);
    exit;
}

$school = mysqli_fetch_assoc($result);

if (!$school) {
    echo json_encode(['error' => 'School not found']);
    mysqli_close($conn);
    exit;
}

// Format the response
$response = [
    'success' => true,
    'school' => [
        'basic_info' => [
            'SchoolID' => $school['SchoolID'],
            'SchoolName' => $school['SchoolName'],
            'SchoolPrevName' => $school['SchoolPrevName'],
            'Institution' => $school['Institution'],
            'MotherSchool' => $school['MotherSchool'],
            'DateEstab' => $school['DateEstab'],
            'ClassOrgNum' => $school['ClassOrgNum'],
            'SchoolTypeNum' => $school['SchoolTypeNum'],
            'LD_Num' => $school['LD_Num'],
            'CDNum' => $school['CDNum'],
            'SubClassNum' => $school['SubClassNum'],
            'DivisionNum' => $school['DivisionNum'],
            'ImplementingUnit' => $school['ImplementingUnit'],
            'CurricularOffer' => $school['CurricularOffer']
        ],
        'address' => [
            'region_id' => $school['region_id'],
            'region_name' => $school['region_name'],
            'region_code' => $school['region_code'],
            'city_id' => $school['city_id'],
            'city_name' => $school['city_name'],
            'city_code' => $school['city_code'],
            'province_id' => $school['province_id'],
            'is_city' => $school['is_city'],
            'muncityClassID' => $school['muncityClassID'],
            'barangay_code' => $school['barangay_code'],
            'barangay_name' => $school['barangay_name'],
            'street' => $school['street'],
            'landmark' => $school['landmark']
        ],
        'coordinates' => [
            'latitude' => $school['latitude'],
            'longitude' => $school['longitude']
        ]
    ]
];

echo json_encode($response);
mysqli_close($conn);
?>
