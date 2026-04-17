<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
include "db.php";
header("Content-Type: application/json");

$uid = $_POST['uid'] ?? '';
$cid = $_POST['cid'] ?? '';
$status = $_POST['status'] ?? '';
$late = $_POST['late'];
$distance = $_POST['distance'] ?? '';
$department = $_POST['department'] ?? '';
$name = $_POST['name'] ?? '';
$office = $_POST['office_name'] ?? '';
$shift_start = $_POST['shift_start'] ?? '';
$shift_end = $_POST['shift_end'] ?? '';
$lat = $_POST['lat'] ?? '';
$lng = $_POST['lng'] ?? '';
$remark = $_POST['remark'] ?? '';
$image = $_POST['image'] ?? '';

date_default_timezone_set('Asia/Kolkata');

$punchInTime = date('Y-m-d H:i:s');

if ($uid == '' || $cid == '') {
  echo json_encode([
    "status" => false,
    "message" => "Missing parameters"
  ]);
  exit;
}

# 🔒 Check if already punched in today
$checkQuery = "
  SELECT id 
  FROM attendance 
  WHERE uid='$uid' 
    AND cid='$cid'
    AND DATE(punch_in_time) = CURDATE()
    AND punch_out_time IS NULL
";

$check = mysqli_query($conn, $checkQuery);
if (mysqli_num_rows($check) > 0) {
  echo json_encode([
    "status" => false,
    "message" => "Already punched in. Please punch out first."
  ]);
  exit;
}
# 🚫 Check if user is on HOLIDAY today
$holidayCheckQuery = "
  SELECT id
  FROM attendance
  WHERE uid='$uid'
    AND cid='$cid'
    AND status='HOLYDAY'
    AND DATE(created_at) = CURDATE()
  LIMIT 1
";

$holidayCheck = mysqli_query($conn, $holidayCheckQuery);

if (mysqli_num_rows($holidayCheck) > 0) {
  echo json_encode([
    "status" => false,
    "message" => "Punch not allowed. User is on HOLIDAY today"
  ]);
  exit;
}


# ✅ Insert punch in
$insertQuery = "
  INSERT INTO attendance
  (cid, uid, name, department, office_name,
   status, late, distance, shift_start, shift_end,
   punch_in_time, punch_in_lat, punch_in_lng,
   punch_in_remark, punch_in_image)
  VALUES
  ('$cid','$uid','$name','$department','$office',
   '$status', '$late','$distance','$shift_start','$shift_end','$punchInTime','$lat','$lng','$remark','$image')
";

mysqli_query($conn, $insertQuery);

$attendance_id = mysqli_insert_id($conn);

echo json_encode([
  "status" => true,
  "message" => "Punch In successful",
  "attendance_id" => $attendance_id
]);
?>
