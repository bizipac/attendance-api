<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

$from = $_POST['from_date'] ?? $input['from_date'] ?? '';
$to   = $_POST['to_date']   ?? $input['to_date']   ?? '';

if ($from === '' || $to === '') {
    echo json_encode([
        "status" => false,
        "message" => "from_date and to_date are required"
    ]);
    exit;
}

$fromDate = $from . " 00:00:00";
$toDate   = $to   . " 23:59:59";

$sql = "
SELECT 
    a.uid,
    a.name,
    u.userid,
    u.city_name,
    a.department,
    a.office_name,

    COUNT(*) AS total_days,
    SUM(a.day_present) AS total_present,
    SUM(a.day_late) AS total_late,
    SUM(a.day_halfday) AS total_halfday,
    SUM(a.day_absent) AS total_absent,
    SUM(a.day_holiday) AS total_holiday,
    SUM(a.day_autopunch) AS missed_punchOut,

    SUM(a.day_work_minutes) AS total_minutes,
    ROUND(SUM(a.day_work_minutes) / 60, 2) AS total_hour,
    SEC_TO_TIME(SUM(a.day_work_minutes) * 60) AS total_time_format,

    ROUND(SUM(
        CASE 
            WHEN a.day_work_minutes > 480 
            THEN a.day_work_minutes - 480 
            ELSE 0 
        END
    ) / 60, 2) AS overtime_hour,

    SUM(a.gps_auto) AS total_gps_auto,
    SUM(a.internet_auto) AS total_internet_auto,
    SUM(a.outside_radius) AS total_outside_radius,

    IFNULL(b.total_break_minutes,0) AS total_break_minutes,
    SEC_TO_TIME(IFNULL(b.total_break_minutes,0) * 60) AS break_time_format

FROM (

    SELECT 
        uid,
        name,
        department,
        office_name,
        DATE(created_at) as att_date,

        MAX(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS day_present,
        MAX(CASE WHEN late = 'Late' THEN 1 ELSE 0 END) AS day_late,
        MAX(CASE WHEN late = 'Half Day' THEN 1 ELSE 0 END) AS day_halfday,
        MAX(CASE WHEN status = 'ABSENT' THEN 1 ELSE 0 END) AS day_absent,
        MAX(CASE WHEN status = 'HOLYDAY' THEN 1 ELSE 0 END) AS day_holiday,
        MAX(CASE WHEN status = 'AUTO_PUNCH_OUT' THEN 1 ELSE 0 END) AS day_autopunch,

        SUM(
            CASE 
                WHEN punch_in_time IS NOT NULL 
                     AND punch_out_time IS NOT NULL
                     AND punch_out_time > punch_in_time
                THEN TIMESTAMPDIFF(MINUTE, punch_in_time, punch_out_time)
                ELSE 0
            END
        ) AS day_work_minutes,

        SUM(CASE 
            WHEN punch_out_remark = 'GPS Turn Off - Auto Punch' 
            THEN 1 ELSE 0 END) AS gps_auto,

        SUM(CASE 
            WHEN punch_out_remark = 'Internet turned off - Auto Punch Out' 
            THEN 1 ELSE 0 END) AS internet_auto,

        SUM(CASE 
            WHEN punch_out_remark = 'You are outside Kiosk radius' 
            THEN 1 ELSE 0 END) AS outside_radius

    FROM attendance
    WHERE created_at BETWEEN ? AND ?
    GROUP BY uid, DATE(created_at)

) a

LEFT JOIN users u ON a.uid = u.uid

LEFT JOIN (

    SELECT 
        uid,
        SUM(duration_minutes) AS total_break_minutes
    FROM attendance_breaks
    WHERE created_at BETWEEN ? AND ?
    GROUP BY uid

) b ON a.uid = b.uid

GROUP BY a.uid, a.name, a.department, a.office_name
ORDER BY a.name
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        "status" => false,
        "message" => "Query preparation failed",
        "error" => mysqli_error($conn)
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "ssss", $fromDate, $toDate, $fromDate, $toDate);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {

    $data[] = [

        "uid" => (int)$row['uid'],
        "name" => $row['name'],
        "userid" => $row['userid'],
"city_name" => $row['city_name'],
        "department" => $row['department'],
        "office_name" => $row['office_name'],

        "total_days" => (int)$row['total_days'],
        "total_present" => (int)$row['total_present'],
        "total_late" => (int)$row['total_late'],
        "total_halfday" => (int)$row['total_halfday'],
        "total_absent" => (int)$row['total_absent'],
        "total_holiday" => (int)$row['total_holiday'],

        "missed_punchOut" => (int)$row['missed_punchOut'],

        "total_minutes" => (int)$row['total_minutes'],
        "total_hour" => (float)$row['total_hour'],
        "total_time_format" => $row['total_time_format'],

        "total_break_minutes" => (int)$row['total_break_minutes'],
        "break_time_format" => $row['break_time_format'],

        "overtime_hour" => (float)$row['overtime_hour'],

        "gps_auto_count" => (int)$row['total_gps_auto'],
        "internet_auto_count" => (int)$row['total_internet_auto'],
        "outside_radius_count" => (int)$row['total_outside_radius'],

        "from_date" => $from,
        "to_date" => $to
    ];
}

echo json_encode([
    "status" => true,
    "range" => [
        "from" => $from,
        "to" => $to
    ],
    "total_users" => count($data),
    "data" => $data
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>