<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

// ✅ Prefer POST / GET explicitly
$office_name = $_POST['office_name'] ?? $_GET['office_name'] ?? '';
$from        = $_POST['from_date']   ?? $_GET['from_date']   ?? '';
$to          = $_POST['to_date']     ?? $_GET['to_date']     ?? '';

if ($office_name === '' || $from === '' || $to === '') {
  echo json_encode([
    "status"  => false,
    "message" => "Missing parameters"
  ]);
  exit;
}

// ✅ Avoid DATE() on column, use range
$sql = "
  SELECT *
  FROM attendance
  WHERE office_name = ?
    AND created_at BETWEEN ? AND ?
  ORDER BY created_at DESC
";

$stmt = mysqli_prepare($conn, $sql);

// ✅ ALL params are strings
$fromDate = $from . " 00:00:00";
$toDate   = $to   . " 23:59:59";

mysqli_stmt_bind_param(
  $stmt,
  "sss",
  $office_name,
  $fromDate,
  $toDate
);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data    = [];
$present = 0;
$absent  = 0;

while ($row = mysqli_fetch_assoc($result)) {

  if ($row['status'] === 'Present') $present++;
  if ($row['status'] === 'Absent')  $absent++;

  $data[] = [
    "id"                   => $row['id'],
    "uid"                  => $row['uid'],
    "name"                 => $row['name'],
    "department"           => $row['department'],
    "office_name"          => $row['office_name'],
    "status"               => $row['status'],
    "punch_in_time"        => $row['punch_in_time'],
    "punch_in_lat"         => $row['punch_in_lat'],
    "punch_in_lng"         => $row['punch_in_lng'],
    "punch_out_time"       => $row['punch_out_time'],
    "punch_out_lat"        => $row['punch_out_lat'],
    "punch_out_lng"        => $row['punch_out_lng'],
    "shift_start"          => $row['shift_start'],
    "shift_end"            => $row['shift_end'],
    "punch_in_image"       => $row['punch_in_image'],
    "punch_out_image"      => $row['punch_out_image'],
    "punch_in_remark"      => $row['punch_in_remark'],
    "punch_out_remark"     => $row['punch_out_remark'],
    "late"                 => $row['late'],
    "total_break_minutes"  => $row['total_break_minutes'],
    "working_minutes"      => $row['working_minutes'],
    "created_at"           => $row['created_at'],
  ];
}

echo json_encode([
  "status"  => true,
  "summary" => [
    "total"   => count($data),
    "present"=> $present,
    "absent" => $absent
  ],
  "data" => $data
]);
?>