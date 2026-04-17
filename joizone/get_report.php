<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

date_default_timezone_set('Asia/Kolkata');

$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : null;
$toDate   = isset($_GET['to_date'])   ? $_GET['to_date']   : null;

// ✅ JOIN users table
$sql = "
SELECT 
    fr.*, 
    u.city_name AS site_name   -- users table se
FROM form_reports fr
LEFT JOIN users u ON fr.uid = u.uid
WHERE fr.duplicate_from = 'no'
";

// ✅ Date filter
if ($fromDate && $toDate) {
    $sql .= " AND fr.report_date BETWEEN '$fromDate' AND '$toDate'";
} else {
    $sql .= " AND DATE(fr.created_at) = CURDATE()";
}

$sql .= " ORDER BY fr.id DESC";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {

    $report_id = $row['id'];

    // 🔁 Images fetch (same as before)
    $imgQuery = "SELECT image_url FROM form_report_images WHERE report_id = '$report_id'";
    $imgResult = $conn->query($imgQuery);

    $images = [];

    while ($imgRow = $imgResult->fetch_assoc()) {
        $images[] = $imgRow['image_url'];
    }

    $row['image_urls'] = $images;

    $data[] = $row;
}

// ✅ Response
echo json_encode([
    "status" => true,
    "data" => $data
]);

$conn->close();
?>