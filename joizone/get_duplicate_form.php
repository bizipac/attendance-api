<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

$sql = "
SELECT 
    c.id,
    c.uid,
    c.user_id,
    c.user_name,
    c.city_name,
    c.report_date,
    c.report_time,
    c.application_no,
    c.relation,
    c.variant,
    c.status,
    c.remarks,
    c.contact_no,
    GROUP_CONCAT(i.image_url) as image_urls,
    c.gps_location,
    c.kiosk_name,
    c.created_at
FROM form_reports c
LEFT JOIN form_report_images i 
ON c.id = i.report_id
WHERE c.duplicate_from = 'yes'
GROUP BY c.id
ORDER BY c.id DESC
";

$result = $conn->query($sql);

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {

        // Convert comma-separated images to array
        $row['image_urls'] = $row['image_urls']
            ? explode(",", $row['image_urls'])
            : [];

        $data[] = $row;
    }
}

echo json_encode([
    "status" => true,
    "data" => $data
]);

$conn->close();
?>