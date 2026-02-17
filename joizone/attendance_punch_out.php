<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";
date_default_timezone_set('Asia/Kolkata');

/* ---------- INPUT ---------- */

$uid = $_POST['uid'] ?? '';
$status = $_POST['status'] ?? '';
$lat = $_POST['lat'] ?? '';
$lng = $_POST['lng'] ?? '';
$remark = $_POST['remark'] ?? '';
$image = $_POST['image'] ?? '';

$punchOutTime = date('Y-m-d H:i:s');

/* ---------- VALIDATION ---------- */

if ($uid == '') {
  echo json_encode([
    "status" => false,
    "message" => "UID missing"
  ]);
  exit;
}

/* ---------- FIND ACTIVE ATTENDANCE ---------- */

$check = mysqli_query($conn,"
  SELECT id, cid, punch_in_time
  FROM attendance
  WHERE uid='$uid'
    AND DATE(punch_in_time) = CURDATE()
    AND punch_out_time IS NULL
  LIMIT 1
");

if (mysqli_num_rows($check) == 0) {
  echo json_encode([
    "status" => false,
    "message" => "No active punch-in found"
  ]);
  exit;
}

$row = mysqli_fetch_assoc($check);

$attendance_id = $row['id'];
$cid = $row['cid'];

/* ---------- TIME CALC ---------- */

$punchIn = strtotime($row['punch_in_time']);
$punchOut = time();

$totalMinutes = floor(($punchOut - $punchIn) / 60);

$netHours = sprintf(
  "%02d:%02d",
  floor($totalMinutes / 60),
  $totalMinutes % 60
);

/* ---------- UPDATE ATTENDANCE ---------- */

$update = mysqli_query($conn,"
  UPDATE attendance SET
    punch_out_time = '$punchOutTime',
    status='$status',
    punch_out_lat='$lat',
    punch_out_lng='$lng',
    punch_out_remark='$remark',
    punch_out_image='$image',
    working_minutes='$totalMinutes',
    net_working_hours='$netHours'
  WHERE id='$attendance_id'
");

if (!$update) {
  echo json_encode([
    "status" => false,
    "message" => "Update failed",
    "error" => mysqli_error($conn)
  ]);
  exit;
}

/* ---------- INSERT LOG (OUT) ---------- */

mysqli_query($conn,"
  INSERT INTO attendance_logs
  (attendance_id, uid, cid, punch_type, punch_time, lat, lng, remark, image)
  VALUES
  ('$attendance_id','$uid','$cid','OUT',
   '$punchOutTime','$lat','$lng','$remark','$image')
");

/* ---------- SUCCESS ---------- */

echo json_encode([
  "status" => true,
  "message" => "Punch Out successful",
  "attendance_id" => $attendance_id,
  "working_minutes" => $totalMinutes,
  "net_hours" => $netHours
]);
?>
