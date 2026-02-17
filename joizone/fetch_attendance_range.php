<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

date_default_timezone_set("Asia/Kolkata");

$cid  = $_REQUEST['cid'] ?? '';
$from = $_REQUEST['from_date'] ?? '';
$to   = $_REQUEST['to_date'] ?? '';

if ($cid === '' || $from === '' || $to === '') {
    echo json_encode([
        "status" => false,
        "message" => "Missing parameters"
    ]);
    exit;
}

$sql = "
SELECT a.*, totals.total_working_minutes
FROM attendance a
JOIN (
    SELECT 
        uid,
        SUM(IFNULL(working_minutes,0)) as total_working_minutes,
        MAX(created_at) as last_created
    FROM attendance
    WHERE cid = ?
    AND DATE(created_at) BETWEEN ? AND ?
    GROUP BY uid
) totals 
ON a.uid = totals.uid 
AND a.created_at = totals.last_created
WHERE a.cid = ?
AND DATE(a.created_at) BETWEEN ? AND ?
ORDER BY a.created_at DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ississ", $cid, $from, $to, $cid, $from, $to);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = [];
$present = 0;
$absent = 0;

while ($row = mysqli_fetch_assoc($result)) {

    if ($row['status'] === 'Present') $present++;
    if ($row['status'] === 'Absent') $absent++;

    $data[] = [
        "id" => $row['id'],
        "uid" => $row['uid'],
        "name" => $row['name'],
        "department" => $row['department'],
        "office_name" => $row['office_name'],
        "status" => $row['status'],

        "punch_in_time" => $row['punch_in_time'],
        "punch_in_lat" => $row['punch_in_lat'],
        "punch_in_lng" => $row['punch_in_lng'],

        "punch_out_time" => $row['punch_out_time'],
        "punch_out_lat" => $row['punch_out_lat'],
        "punch_out_lng" => $row['punch_out_lng'],

        "shift_start" => $row['shift_start'],
        "shift_end" => $row['shift_end'],

        "punch_in_image" => $row['punch_in_image'],
        "punch_out_image" => $row['punch_out_image'],

        "punch_in_remark" => $row['punch_in_remark'],
        "punch_out_remark" => $row['punch_out_remark'],

        "late" => $row['late'],

        "total_break_minutes" => $row['total_break_minutes'],
        "working_minutes" => $row['working_minutes'], // last record ka
        "total_working_minutes" => $row['total_working_minutes'], // multiple punch ka total

        "created_at" => $row['created_at'],
    ];
}

echo json_encode([
    "status" => true,
    "summary" => [
        "total_users" => count($data),
        "present" => $present,
        "absent" => $absent
    ],
    "data" => $data
]);
?>
