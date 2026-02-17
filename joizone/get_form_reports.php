<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

/* 👉 Yahan sirf required columns likho */
$sql = "
SELECT 
    id,
    uid,
    user_id,
    user_name,
    city_name,
    report_date,
    report_time,
    application_no,
    relation,
    variant,
    status,
    remarks,
    contact_no,
    image_url,
    gps_location,
    kiosk_name,
    created_at
    FROM client_form_reports
    WHERE duplicate_from = 'no'
ORDER BY id DESC
";

$result = $conn->query($sql);

$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
?>
