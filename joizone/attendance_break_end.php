<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
require 'db.php';

$attendance_id = $_POST['attendance_id'] ?? null;

if (!$attendance_id) {
    echo json_encode(["status" => false, "message" => "Attendance ID required"]);
    exit;
}

// get active break
$res = $conn->query("
    SELECT break_id, break_start
    FROM attendance_breaks
    WHERE attendance_id = $attendance_id
      AND break_end IS NULL
    LIMIT 1
");

if ($res->num_rows == 0) {
    echo json_encode(["status" => false, "message" => "No active break found"]);
    exit;
}

$row = $res->fetch_assoc();
$break_id = $row['break_id'];

// calculate duration
$durationRes = $conn->query("
    SELECT TIMESTAMPDIFF(
        MINUTE,
        break_start,
        NOW()
    ) AS minutes
    FROM attendance_breaks
    WHERE break_id = $break_id
");

$duration = $durationRes->fetch_assoc()['minutes'];

// update break end
$conn->query("
    UPDATE attendance_breaks
    SET break_end = NOW(),
        duration_minutes = $duration
    WHERE break_id = $break_id
");

// update attendance total_break_minutes
$conn->query("
    UPDATE attendance
    SET total_break_minutes = total_break_minutes + $duration
    WHERE attendance_id = $attendance_id
");

echo json_encode([
    "status" => true,
    "message" => "Break ended",
    "break_minutes" => $duration
]);
?>