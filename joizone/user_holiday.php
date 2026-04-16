<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "db.php";

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['records']) || !is_array($data['records'])) {
    echo json_encode([
        "status" => false,
        "message" => "No records received"
    ]);
    exit;
}

$success = 0;
$failed  = 0;

foreach ($data['records'] as $row) {

    $cid        = $row['cid'] ?? '';
    $uid        = $row['uid'] ?? '';
    $name       = $row['name'] ?? '';
    $department = $row['user_type'] ?? '';
    $office     = $row['office_name'] ?? '';
    $status = strtoupper(trim($row['status'] ?? ''));

if ($status == 'WO') {
    $status = 'HOLYDAY';
}


    $shiftStart = $row['shift_start'] ?? null;
    $shiftEnd   = $row['shift_end'] ?? null;

    // created_at from Flutter (ISO / date)
    $createdAtRaw = $row['roster_date'] ?? date('Y-m-d');
    $createdAt   = date('Y-m-d', strtotime($createdAtRaw));
    $updatedAt   = date('Y-m-d H:i:s');

    //agr hum yha ek 

    // Basic validation
    if ($cid == '' || $uid == '') {
        $failed++;
        continue;
    }


    // 🔁 Duplicate check (same user, same date)
    $check = mysqli_query($conn, "
        SELECT id 
        FROM attendance 
        WHERE uid='$uid' 
        AND DATE(created_at)='$createdAt'
    ");

    if (mysqli_num_rows($check) > 0) {
        $failed++;
        continue;
    }

    // ✅ Insert attendance
    $insert = mysqli_query($conn, "
        INSERT INTO attendance
        (cid, uid, name, department, office_name, status, created_at,roster_date )
        VALUES
        ('$cid','$uid','$name','$department','$office','$status','$createdAt','$updatedAt')
    ");

    if ($insert) {
        //i want to shift update only monday
        // Update shift if provided
        if ($shiftStart && $shiftEnd) {
            mysqli_query($conn, "
                UPDATE users SET
                shift_start='$shiftStart',
                shift_end='$shiftEnd',
                updatedAt='$updatedAt' 
                WHERE uid='$uid'
            ");
        }

        $success++;
    } else {
        $failed++;
    }
}

// Final response
echo json_encode([
    "status"  => true,
    "message" => "Uploaded successfully",
    "success" => $success,
    "failed"  => $failed
]);
?>
