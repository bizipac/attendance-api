<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

$attendance_id = $_POST['attendance_id'] ?? '';

if ($attendance_id === '') {
    echo json_encode([
        "status" => false,
        "message" => "attendance_id is required"
    ]);
    exit;
}

// 🔍 Check attendance record
$sql = "SELECT punch_out_time FROM attendance WHERE id='$attendance_id' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode([
        "status" => false,
        "message" => "Attendance record not found"
    ]);
    exit;
}

$row = mysqli_fetch_assoc($result);

// 🔥 Check punch_out_time
if (empty($row['punch_out_time']) || $row['punch_out_time'] == null) {
    echo json_encode([
        "status" => true,
        "attendance_status" => "active"
    ]);
} else {
    echo json_encode([
        "status" => true,
        "attendance_status" => "closed"
    ]);
}
?>