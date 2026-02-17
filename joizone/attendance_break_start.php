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

// check active break
$check = $conn->query("
    SELECT break_id FROM attendance_breaks
    WHERE attendance_id = $attendance_id
      AND break_end IS NULL
    LIMIT 1
");

if ($check->num_rows > 0) {
    echo json_encode(["status" => false, "message" => "Break already active"]);
    exit;
}

// insert break start
$conn->query("
    INSERT INTO attendance_breaks (attendance_id, break_start)
    VALUES ($attendance_id, NOW())
");

echo json_encode([
    "status" => true,
    "message" => "Break started",
    "break_id" => $conn->insert_id
]);
?>