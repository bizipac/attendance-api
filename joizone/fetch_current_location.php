<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
include "db.php";

$attendance_id = $_GET['attendance_id'] ?? 0;

$sql = "
SELECT latitude, longitude, created_at
FROM attendance_locations
WHERE attendance_id = '$attendance_id'
ORDER BY created_at DESC
LIMIT 1
";

$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
  echo json_encode([
    "status" => true,
    "data" => $row
  ]);
} else {
  echo json_encode([
    "status" => false,
    "message" => "No location found"
  ]);
}
?>