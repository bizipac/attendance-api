<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require 'db.php';

// ✅ Set Indian Timezone
date_default_timezone_set('Asia/Kolkata');

$attendance_id = $_POST['attendance_id'] ?? null;
$uid = $_POST['uid'] ?? null;

if (!$attendance_id || !$uid) {
    echo json_encode([
        "status" => false,
        "message" => "Attendance ID and UID required"
    ]);
    exit;
}

// Check attendance
$check = $conn->prepare("SELECT id FROM attendance WHERE id = ? AND uid = ?");
$check->bind_param("ii", $attendance_id, $uid);
$check->execute();
$result = $check->get_result();

if ($result->num_rows == 0) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid attendance record"
    ]);
    exit;
}

// Check active break
$activeBreak = $conn->prepare("
    SELECT break_id 
    FROM attendance_breaks 
    WHERE attendance_id = ? 
      AND break_end IS NULL
    LIMIT 1
");
$activeBreak->bind_param("i", $attendance_id);
$activeBreak->execute();
$activeResult = $activeBreak->get_result();

if ($activeResult->num_rows > 0) {
    echo json_encode([
        "status" => false,
        "message" => "User already on break"
    ]);
    exit;
}

// ✅ Get Indian current datetime from PHP
$currentTime = date('Y-m-d H:i:s');

// Insert break
$stmt = $conn->prepare("
    INSERT INTO attendance_breaks 
    (uid, attendance_id, break_start, created_at)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("iiss", $uid, $attendance_id, $currentTime, $currentTime);

if ($stmt->execute()) {
    echo json_encode([
        "status" => true,
        "message" => "Break started successfully",
        "break_id" => $stmt->insert_id,
        "break_start_time" => $currentTime
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Failed to start break"
    ]);
}
?>
