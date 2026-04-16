<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";
date_default_timezone_set("Asia/Kolkata");

if (!isset($_POST['uid']) || !isset($_POST['date'])) {
    echo json_encode([
        "status" => false,
        "message" => "uid and date required"
    ]);
    exit;
}

$uid  = $_POST['uid'];
$date = $_POST['date'];

$sql = "SELECT * FROM attendance
        WHERE uid = ?
        AND DATE(created_at) = ?
        ORDER BY id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $uid, $date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode([
        "status" => false,
        "message" => "No attendance found"
    ]);
    exit;
}

$totalWorkingMinutes = 0;
$totalPunches = 0;
$data = [];

while ($row = $result->fetch_assoc()) {

    $totalPunches++;

    $punchIn  = $row['punch_in_time'];
    $punchOut = $row['punch_out_time'];

    if (!empty($punchIn) && !empty($punchOut)) {

        $inTime  = strtotime($punchIn);
        $outTime = strtotime($punchOut);

        if ($outTime > $inTime) {
            $diffMinutes = ($outTime - $inTime) / 60;
            $totalWorkingMinutes += $diffMinutes;
        }
    }

    $data[] = [
        "id" => $row['id'],
        "uid" => $row['uid'],
        "name" => $row['name'],
        "department" => $row['department'],
        "office_name" => $row['office_name'],
        "status" => $row['status'],
        "punch_in_time" => $row['punch_in_time'],
        "punch_out_time" => $row['punch_out_time'],
        "punch_in_remark" => $row['punch_in_remark'],
        "punch_out_remark" => $row['punch_out_remark'],
        "punch_in_image" => $row['punch_in_image'],
        "punch_out_image" => $row['punch_out_image'],
        "working_minutes" => $row['working_minutes'],
        "total_break_minutes" => $row['total_break_minutes'],
        "created_at" => $row['created_at'],
    ];
}

// Convert minutes to HH:MM
$hours = floor($totalWorkingMinutes / 60);
$minutes = $totalWorkingMinutes % 60;
$netWorkingHours = sprintf("%02d:%02d", $hours, $minutes);

echo json_encode([
    "status" => true,
    "summary" => [
        "total_punches" => $totalPunches,
        "total_working_minutes" => $totalWorkingMinutes,
        "net_working_hours" => $netWorkingHours
    ],
    "data" => $data
]);
?>
