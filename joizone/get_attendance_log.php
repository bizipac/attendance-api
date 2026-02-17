<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";
date_default_timezone_set('Asia/Kolkata');

$uid = $_GET['uid'] ?? '';

if ($uid == '') {
  echo json_encode([
    "status" => false,
    "message" => "UID missing"
  ]);
  exit;
}

/* ---------- FETCH TODAY LOGS ---------- */

$query = "
  SELECT 
    id,
    uid,
    attendance_id,
    punch_type,
    punch_time,
    lat,
    lng,
    remark,
    image
  FROM attendance_logs
  WHERE uid = '$uid'
    AND DATE(punch_time) = CURDATE()
  ORDER BY punch_time ASC
";

$result = mysqli_query($conn, $query);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

/* ---------- RESPONSE ---------- */

echo json_encode([
  "status" => true,
  "count" => count($data),
  "data" => $data
]);
?>
