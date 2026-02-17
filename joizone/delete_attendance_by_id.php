<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
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

// 🔍 Check if attendance exists
$checkSql = "SELECT id FROM attendance WHERE id='$attendance_id' LIMIT 1";
$checkRes = mysqli_query($conn, $checkSql);

if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    echo json_encode([
        "status" => false,
        "message" => "Attendance record not found"
    ]);
    exit;
}

// 🗑 Delete attendance row
$deleteSql = "DELETE FROM attendance WHERE id='$attendance_id'";
$deleteRes = mysqli_query($conn, $deleteSql);

if (!$deleteRes) {
    echo json_encode([
        "status" => false,
        "message" => "Failed to delete attendance"
    ]);
    exit;
}

echo json_encode([
    "status" => true,
    "message" => "Attendance deleted successfully",
    "attendance_id" => $attendance_id
]);
?>
