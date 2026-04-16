<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

$uid  = $_REQUEST['uid'] ?? '';
$date = $_REQUEST['date'] ?? '';

if ($uid == '' || $date == '') {
    echo json_encode([
        "status" => false,
        "message" => "Missing parameters"
    ]);
    exit;
}

$startDate = $date . " 00:00:00";
$endDate   = $date . " 23:59:59";

$stmt = $conn->prepare("
    SELECT 
        a.id,
        a.name,
        a.punch_in_time,
        a.punch_out_time,
        a.created_at,
        u.branch_lat,
        u.branch_long
    FROM attendance a
    LEFT JOIN users u ON a.uid = u.uid
    WHERE a.uid = ?
    AND a.created_at BETWEEN ? AND ?
    ORDER BY a.created_at ASC
");

$stmt->bind_param("sss", $uid, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = [
        "attendance_id" => $row['id'],
        "date" => date("Y-m-d", strtotime($row['created_at'])),
        "fullname" => $row['name'],
        "punch_in_time" => $row['punch_in_time'],
        "punch_out_time" => $row['punch_out_time'],
        "branch_lat" => $row['branch_lat'],
        "branch_long" => $row['branch_long']
    ];
}

echo json_encode([
    "status" => true,
    "count" => count($data),
    "data" => $data
]);
?>