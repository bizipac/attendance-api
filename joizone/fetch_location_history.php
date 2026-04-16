<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

$attendance_id = $_REQUEST['attendance_id'] ?? '';

if ($attendance_id == '') {
    echo json_encode([
        "status" => false,
        "message" => "Attendance ID missing"
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT id, attendance_id, latitude, longitude, created_at
    FROM attendance_locations
    WHERE attendance_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("s", $attendance_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id" => $row['id'],
        "attendance_id" => $row['attendance_id'],
        "latitude" => $row['latitude'],
        "longitude" => $row['longitude'],
        "created_at" => $row['created_at']
    ];
}

echo json_encode([
    "status" => true,
    "count" => count($data),
    "data" => $data
]);
?>