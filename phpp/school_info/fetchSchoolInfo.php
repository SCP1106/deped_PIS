<?php

include '../Connect/dataDB.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if a specific school_id is requested
$schoolID = $_GET['school_id'] ?? null;
if ($schoolID) {

    $sql = "SELECT 
        K_M, G1_M, G2_M, G3_M, G4_M, G5_M, G6_M, G7_M, G8_M, G9_M, G10_M,
        FS_G11_ACAD_M, FS_G11_TVL_M, FS_G11_SPORT_M, FS_G11_ART_M, FS_G11_UNQ_M,
        FS_G12_ACAD_M, FS_G12_TVL_M, FS_G12_SPORT_M, FS_G12_ART_M, FS_G12_UNQ_M,
        SS_G11_ACAD_M, SS_G11_TVL_M, SS_G11_SPORT_M, SS_G11_ART_M, SS_G11_UNQ_M,
        SS_G12_ACAD_M, SS_G12_TVL_M, SS_G12_SPORT_M, SS_G12_ART_M, SS_G12_UNQ_M,
        ELEM_NG_M, JHS_NG_M,

        K_F, G1_F, G2_F, G3_F, G4_F, G5_F, G6_F, G7_F, G8_F, G9_F, G10_F,
        FS_G11_ACAD_F, FS_G11_TVL_F, FS_G11_SPORT_F, FS_G11_ART_F, FS_G11_UNQ_F,
        FS_G12_ACAD_F, FS_G12_TVL_F, FS_G12_SPORT_F, FS_G12_ART_F, FS_G12_UNQ_F,
        SS_G11_ACAD_F, SS_G11_TVL_F, SS_G11_SPORT_F, SS_G11_ART_F, SS_G11_UNQ_F,
        SS_G12_ACAD_F, SS_G12_TVL_F, SS_G12_SPORT_F, SS_G12_ART_F, SS_G12_UNQ_F,
        ELEM_NG_F, JHS_NG_F
    FROM enrollment_data
    WHERE SchoolID = ?
    AND SchoolYear = 2024";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["error" => "SQL Prepare Failed: " . $conn->error], JSON_PRETTY_PRINT);
        exit;
    }

    $stmt->bind_param("s", $schoolID);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$data) {
        echo json_encode(["error" => "No enrollment data found for this school"], JSON_PRETTY_PRINT);
        exit;
    }

    echo json_encode($data, JSON_PRETTY_PRINT);

} else {
    // Summary of all schools with principal and enrollment totals
    $sql = "
            WITH principal_cte AS (
            SELECT 
                er.School_ID,
                TRIM(CONCAT(
                    er.first_name, ' ',
                    IFNULL(CONCAT(er.middle_name, ' '), ''),
                    er.last_name
                )) AS principal,
                TIMESTAMPDIFF(
                    YEAR, 
                    STR_TO_DATE(
                        CONCAT(er.BIRTHDAY_DD, ' ', er.BIRTHDAY_MM, ' ', er.BIRTHDAY_YYYY), 
                        '%d %M %Y'
                    ), 
                    CURDATE()
                ) AS principal_age,
                ROW_NUMBER() OVER (PARTITION BY er.School_ID ORDER BY er.rank_position ASC) AS rn
            FROM employee_records er
        )

        SELECT 
            s.SchoolID, 
            s.Schoolname, 
            s.Institution, 
            s.curricularOffer,  -- Keep this in the query to pass the data
            cd.CDName AS district,
            
            -- Location information
            r.region_name,
            p.province_name,
            c.city_name,
            b.barangay_name,
            sa.landmark,
            sc.latitude,
            sc.longitude,

            -- Aggregated Enrollees
            COALESCE(SUM(
                IFNULL(ed.ELEM_TOTAL_M, 0) + 
                IFNULL(ed.JHS_TOTAL_M, 0) + 
                IFNULL(ed.FS_SHS_TTL_M, 0) + 
                IFNULL(ed.SS_SHS_TTL_M, 0)
            ), 0) AS total_male,

            COALESCE(SUM(
                IFNULL(ed.ELEM_TOTAL_F, 0) + 
                IFNULL(ed.JHS_TOTAL_F, 0) + 
                IFNULL(ed.FS_SHS_TTL_F, 0) + 
                IFNULL(ed.SS_SHS_TTL_F, 0)
            ), 0) AS total_female,

            COALESCE(SUM(IFNULL(ed.ALL_GRADE_TTL, 0)), 0) AS total_enrollees,

            -- Principal Info (limited to top rank)
            COALESCE(MAX(pr.principal), 'N/A') AS principal,
            COALESCE(MAX(pr.principal_age), 'N/A') AS principal_age

        FROM schoolinfo s
        LEFT JOIN schooladd sa ON s.address_id = sa.address_id  
        LEFT JOIN barangay b ON sa.barangay_code = b.barangay_code  
        LEFT JOIN muncity c ON sa.city_id = c.city_id
        LEFT JOIN province p ON c.province_id = p.province_id
        LEFT JOIN region r ON sa.region_id = r.region_id
        LEFT JOIN citydistrict cd ON s.CDNum = cd.CDNum  
        LEFT JOIN schoolcoor sc ON s.SchoolID = sc.SchoolID

        LEFT JOIN enrollment_data ed 
            ON s.SchoolID = ed.SchoolID AND ed.SchoolYear = 2024

        LEFT JOIN principal_cte pr 
            ON s.SchoolID = pr.School_ID AND pr.rn = 1

        GROUP BY 
            s.SchoolID, s.Schoolname, s.Institution, s.curricularOffer, cd.CDName,
            r.region_name, p.province_name, c.city_name, b.barangay_name, sa.landmark,
            sc.latitude, sc.longitude

        ORDER BY cd.CDName ASC;

    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["error" => "SQL Prepare Failed: " . $conn->error], JSON_PRETTY_PRINT);
        exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $schoolData = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();

    if (!$schoolData) {
        echo json_encode(["error" => "No school records found"], JSON_PRETTY_PRINT);
        exit;
    }

    echo json_encode($schoolData, JSON_PRETTY_PRINT);
}
?>