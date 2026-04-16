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
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : null;

$sql = "SELECT * FROM form_reports WHERE duplicate_from = 'no'";

if ($fromDate && $toDate) {
    $sql .= " AND report_date BETWEEN '$fromDate' AND '$toDate'";
} else {
    $sql .= " AND DATE(created_at) = CURDATE()";
}

$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {

    $report_id = $row['id'];

    $imgQuery = "SELECT image_url FROM form_report_images WHERE report_id = '$report_id'";
    $imgResult = $conn->query($imgQuery);

    $images = [];

    while ($imgRow = $imgResult->fetch_assoc()) {
        $images[] = $imgRow['image_url'];
    }

    $row['image_urls'] = $images;
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);

$conn->close();
?>