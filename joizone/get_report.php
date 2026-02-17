<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include 'db.php';

$sql = "SELECT * FROM form_reports ORDER BY id DESC";
$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {

    $report_id = $row['id'];

    // 🔹 Get images for this report
    $imgQuery = "
        SELECT image_url 
        FROM form_report_images 
        WHERE report_id = '$report_id'
    ";

    $imgResult = $conn->query($imgQuery);

    $images = [];

    while ($imgRow = $imgResult->fetch_assoc()) {
        $images[] = $imgRow['image_url'];
    }

    // Attach image array
    $row['image_urls'] = $images;

    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);

$conn->close();
?>
