<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "db.php";

$cid        = $_POST['cid'] ?? '';
$uid        = $_POST['uid'] ?? '';
$name       = $_POST['name'] ?? '';
$department = $_POST['department'] ?? '';
 $office     = $_POST['office_name'] ?? '';
// $shift_start = $_POST['shift_start'] ?? '';
// $shift_end     = $_POST['shift_end'] ?? '';
$status     = $_POST['status'] ?? 'HOLYDAY';
$date       = $_POST['date'] ?? date('Y-m-d');
// $updatedAt   = date('Y-m-d H:i:s')
if ($cid == '' || $uid == '' || $date == '') {
    echo json_encode([
        "status" => false,
        "message" => "Required fields missing"
    ]);
    exit;
}

// ❌ duplicate holiday check
$check = mysqli_query($conn, "
    SELECT id FROM attendance 
    WHERE uid='$uid' AND DATE(created_at)='$date'
");

if (mysqli_num_rows($check) > 0) {
    echo json_encode([
        "status" => false,
        "message" => "Attendance already exists for this date"
    ]);
    exit;
}

// ✅ insert holiday
$query = "
INSERT INTO attendance
(cid, uid, name, department, office_name, status, created_at)
VALUES
('$cid','$uid','$name','$department','$office','$status','$date')
";
// // ✅ update user shift
// $sql = "UPDATE users SET
//         shift_start='$shift_start',
//         shift_end='$shift_end',
//         updatedAt='$updatedAt'
//         WHERE uid='$uid'";

        
if (mysqli_query($conn, $query)) {
    echo json_encode([
        "status" => true,
        "message" => "Holiday assigned successfully"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => mysqli_error($conn)
    ]);
}
?>