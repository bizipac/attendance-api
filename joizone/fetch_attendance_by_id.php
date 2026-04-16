<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

date_default_timezone_set('Asia/Kolkata');

$cid  = $_POST['cid'] ?? '';
$uid  = $_POST['uid'] ?? '';
$date = $_POST['punch_in_time'] ?? '';

if ($cid === '' || $uid === '' || $date === '') {
  echo json_encode([
    "status" => false,
    "message" => "Missing parameters"
  ]);
  exit;
}

/* ===============================
   CHECK ATTENDANCE RECORD
================================ */

$sql = "SELECT * FROM attendance
        WHERE cid='$cid'
        AND uid='$uid'
        AND DATE(created_at)='$date'
        ORDER BY id DESC
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result) {
  echo json_encode([
    "status" => false,
    "message" => "Query failed"
  ]);
  exit;
}

$row = mysqli_fetch_assoc($result);

/* ===============================
   IF RECORD FOUND
================================ */

if ($row) {

  // 🔥 Safe holiday check (case insensitive)
  if (strtoupper($row['status']) == 'HOLYDAY' || 
      strtoupper($row['status']) == 'HOLIDAY') {

    echo json_encode([
      "status" => true,
      "data" => [
        "status" => "HOLIDAY"
      ]
    ]);
    exit;
  }

  // Normal attendance data
  if ($row) {

  echo json_encode([
    "status" => true,
    "data" => [
      "punchIn" => [
        "time" => $row['punch_in_time'] ?? null,
        "remark" => $row['punch_in_remark'] ?? null,
        "image" => $row['punch_in_image'] ?? null
      ],
      "punchOut" => [
        "time" => $row['punch_out_time'] ?? null,
        "remark" => $row['punch_out_remark'] ?? null,
        "image" => $row['punch_out_image'] ?? null
      ],
      "currentLat" => $row['punch_in_lat'] ?? null,
      "currentLng" => $row['punch_in_lng'] ?? null,
      "shiftStart" => $row['shift_start'] ?? null,
      "shiftEnd" => $row['shift_end'] ?? null,
      "status" => $row['status'] ?? null,
      "late" => $row['late'] ?? null,
      "totalBreakMinutes" => $row['total_break_minutes'] ?? null
    ]
  ]);
  exit;
}

}

/* ===============================
   NO RECORD → ABSENT
================================ */

echo json_encode([
  "status" => true,
  "data" => [
    "status" => "-"
  ]
]);
exit;

?>
