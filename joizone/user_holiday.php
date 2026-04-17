<?php
// ✅ Hide errors (important for JSON)
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "db.php";

// ✅ Helper function
function sendResponse($status, $message, $success = 0, $failed = 0) {
    echo json_encode([
        "status"  => $status,
        "message" => $message,
        "success" => $success,
        "failed"  => $failed
    ]);
    exit;
}

// ✅ Read JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['records']) || !is_array($data['records'])) {
    sendResponse(false, "No records received");
}

$success = 0;
$failed  = 0;

foreach ($data['records'] as $row) {

    $cid        = $row['cid'] ?? '';
    $uid        = $row['uid'] ?? '';
    $userid     = $row['userid'] ?? '';
    $department = $row['user_type'] ?? '';
    $office     = $row['office_name'] ?? '';
    $status     = strtoupper(trim($row['status'] ?? ''));

    // ✅ Only WO process
    if ($status !== 'WO') {
        continue;
    }

    $status = 'HOLIDAY';

    // ✅ Shift conversion (safe)
    $shiftStart = null;
    $shiftEnd   = null;

    if (!empty($row['shift_start'])) {
        $shiftStart = date("H:i", strtotime($row['shift_start']));
    }

    if (!empty($row['shift_end'])) {
        $shiftEnd = date("H:i", strtotime($row['shift_end']));
    }

    // ✅ Date conversion (ISO safe)
    $createdAtRaw = $row['roster_date'] ?? date('Y-m-d');
    $createdAt = date('Y-m-d', strtotime(str_replace('T', ' ', $createdAtRaw)));

    // current timestamp
    $updatedAt = date('Y-m-d H:i:s');

    // ✅ Validation
    if ($cid == '' || $uid == '') {
        $failed++;
        continue;
    }

    // ✅ Check duplicate
    $check = mysqli_query($conn, "
        SELECT id 
        FROM attendance 
        WHERE uid='$uid' 
        AND DATE(roster_date)='$createdAt'
    ");

    if (mysqli_num_rows($check) > 0) {
        $failed++;
        continue;
    }

    // ✅ Fetch name from users table
    $userRes = mysqli_query($conn, "
        SELECT name 
        FROM users 
        WHERE uid='$uid'
    ");

    $userData = mysqli_fetch_assoc($userRes);
    $name = $userData['name'] ?? '';

    // ✅ Insert attendance (FIXED mapping)
    $insert = mysqli_query($conn, "
        INSERT INTO attendance
        (cid, uid, name, department, office_name, status, created_at, roster_date)
        VALUES
        ('$cid','$uid','$name','$department','$office','$status','$updatedAt','$createdAt')
    ");

    if ($insert) {

        // ✅ Update shift ONLY for WO
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

// ✅ Final response
sendResponse(true, "Uploaded successfully", $success, $failed);
?>