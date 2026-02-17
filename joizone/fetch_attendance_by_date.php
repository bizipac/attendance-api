<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

// 🔹 Inputs
$cid  = $_REQUEST['cid'] ?? '';
$date = $_REQUEST['date'] ?? '';

if ($cid === '' || $date === '') {
  echo json_encode([
    "status" => false,
    "message" => "Missing parameters"
  ]);
  exit;
}

// 🔹 Query
$sql = "SELECT a.*
        FROM attendance a
        INNER JOIN (
            SELECT uid, MAX(id) as max_id
            FROM attendance
            WHERE cid='$cid'
            AND DATE(created_at)='$date'
            GROUP BY uid
        ) b ON a.id = b.max_id
        ORDER BY a.id DESC";


$result = mysqli_query($conn, $sql);

if (!$result) {
  echo json_encode([
    "status" => false,
    "message" => "Query failed"
  ]);
  exit;
}

// 🔹 Data collect
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
  $data[] = [
    "id" => $row['id'],
    "cid" => $row['cid'],
    "uid" => $row['uid'],
    "name" => $row['name'],
    "department" => $row['department'],
    "office_name" => $row['office_name'],
    "status" => $row['status'],
    "late" => $row['late'],
    "distance" => $row['distance'],
    "shift_start" => $row['shift_start'],
    "shift_end" => $row['shift_end'],
    "punch_in_time" => $row['punch_in_time'],
    "punch_in_remark" => $row['punch_in_remark'],
    "punch_in_image" => $row['punch_in_image'],
    "punch_out_time" => $row['punch_out_time'],
    "punch_out_remark" => $row['punch_out_remark'],
    "punch_out_image" => $row['punch_out_image'],
    "total_break_minutes"=>$row['total_break_minutes'],
    "created_at" => $row['created_at'],
    "roster_date"=>$row['roster_date']
  ];
}

echo json_encode([
  "status" => true,
  "count" => count($data),
  "data" => $data
]);
