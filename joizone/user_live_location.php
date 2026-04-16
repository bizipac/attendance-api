<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";
// ✅ Indian Timezone
date_default_timezone_set('Asia/Kolkata');
$cid  = $_REQUEST['cid'] ?? '';

if ($cid === '') {
  echo json_encode([
    "status" => false,
    "message" => "Missing cid"
  ]);
  exit;
}

$sql = "SELECT 
            a.*,
            l.latitude,
            l.longitude
        FROM attendance a
        INNER JOIN (
            SELECT uid, MAX(id) as max_id
            FROM attendance
            WHERE cid='$cid'
            GROUP BY uid
        ) b ON a.id = b.max_id
        LEFT JOIN attendance_locations l 
            ON l.id = (
                SELECT MAX(id)
                FROM attendance_locations
                WHERE attendance_id = a.id
            )
        ORDER BY a.id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
  echo json_encode([
    "status" => false,
    "message" => "Query failed"
  ]);
  exit;
}

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
    "punch_in_time" => $row['punch_in_time'],
    "punch_out_time" => $row['punch_out_time'],
    "latitude" => $row['latitude'] ?? null,
    "longitude" => $row['longitude'] ?? null,
    "created_at" => $row['created_at']
  ];
}

echo json_encode([
  "status" => true,
  "count" => count($data),
  "data" => $data
]);
?>