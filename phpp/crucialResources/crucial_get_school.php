<?php
include '../Connect/dataDB.php';

header('Content-Type: application/json');

try {
    $query = "
        SELECT 
            si.schoolID,
            si.schoolName,
            cd.CDName AS district,

            -- Enrollment logic from enrollment_data
            COALESCE(SUM(
                IFNULL(ed.ELEM_TOTAL_M, 0) +
                IFNULL(ed.JHS_TOTAL_M, 0) +
                IFNULL(ed.FS_SHS_TTL_M, 0) +
                IFNULL(ed.SS_SHS_TTL_M, 0) +
                IFNULL(ed.ELEM_TOTAL_F, 0) +
                IFNULL(ed.JHS_TOTAL_F, 0) +
                IFNULL(ed.FS_SHS_TTL_F, 0) +
                IFNULL(ed.SS_SHS_TTL_F, 0)
            ), 0) AS enrollment,

            COALESCE(cr.kinder_total + cr.grade16_total + cr.sped_total + cr.jhs_total + cr.shs_total, 0) AS classroom,
            COALESCE(sr.kinder_total + sr.grade16_total + sr.sped_total + sr.jhs_total + sr.shs_total, 0) AS seats,
            COALESCE(tr.kinder_total + tr.grade16_total + tr.sped_total + tr.jhs_total + tr.shs_total, 0) AS textbooks

        FROM schoolinfo si
        LEFT JOIN citydistrict cd ON si.CDNum = cd.CDNum

        -- Use enrollment_data instead of school_enrollment
        LEFT JOIN enrollment_data ed 
            ON si.schoolID = ed.SchoolID AND ed.SchoolYear = 2024

        LEFT JOIN school_resources cr ON si.schoolID = cr.schoolID AND cr.resource_type = 'classroom'
        LEFT JOIN school_resources sr ON si.schoolID = sr.schoolID AND sr.resource_type = 'seats'
        LEFT JOIN school_resources tr ON si.schoolID = tr.schoolID AND tr.resource_type = 'textbooks'

        GROUP BY si.schoolID, si.schoolName, cd.CDName, cr.kinder_total, cr.grade16_total, cr.sped_total, cr.jhs_total, cr.shs_total,
                 sr.kinder_total, sr.grade16_total, sr.sped_total, sr.jhs_total, sr.shs_total,
                 tr.kinder_total, tr.grade16_total, tr.sped_total, tr.jhs_total, tr.shs_total

        ORDER BY si.CDNum
    ";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($conn));
    }
    
    $schools = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $schools[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $schools]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn);
?>
