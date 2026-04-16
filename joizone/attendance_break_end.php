<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require 'db.php';

// ✅ Indian Timezone
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

$conn->begin_transaction();

try {

    // ✅ Verify attendance belongs to user
    $check = $conn->prepare("
        SELECT id FROM attendance 
        WHERE id = ? AND uid = ?
    ");
    $check->bind_param("ii", $attendance_id, $uid);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        throw new Exception("Invalid attendance record");
    }

    // ✅ Get active break
    $stmt = $conn->prepare("
        SELECT break_id, break_start 
        FROM attendance_breaks
        WHERE attendance_id = ?
          AND break_end IS NULL
        LIMIT 1
    ");
    $stmt->bind_param("i", $attendance_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        throw new Exception("No active break found");
    }

    $row = $res->fetch_assoc();
    $break_id = $row['break_id'];
    $break_start = $row['break_start'];

    // ✅ Calculate duration in PHP (Indian time safe)
    $start = new DateTime($break_start);
    $end = new DateTime(); // current IST time
    $interval = $start->diff($end);
    $duration = ($interval->h * 60) + $interval->i;

    // ✅ Update break record
    $updateBreak = $conn->prepare("
        UPDATE attendance_breaks
        SET break_end = ?, duration_minutes = ?
        WHERE break_id = ?
    ");

    $currentTime = date('Y-m-d H:i:s');
    $updateBreak->bind_param("sii", $currentTime, $duration, $break_id);
    $updateBreak->execute();

    // ✅ Update total break time in attendance
    $updateAttendance = $conn->prepare("
        UPDATE attendance
        SET total_break_minutes = IFNULL(total_break_minutes,0) + ?
        WHERE id = ?
    ");
    $updateAttendance->bind_param("ii", $duration, $attendance_id);
    $updateAttendance->execute();

    $conn->commit();

    echo json_encode([
        "status" => true,
        "message" => "Break ended successfully",
        "break_minutes" => $duration,
        "break_end_time" => $currentTime
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        "status" => false,
        "message" => $e->getMessage()
    ]);
}
?>
