<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

if(!isset($_GET['kiosk_name']) || !isset($_GET['date'])){
    echo json_encode([
        "status" => false,
        "message" => "kiosk_name and date required"
    ]);
    exit;
}

$kiosk_name = mysqli_real_escape_string($conn, $_GET['kiosk_name']);
$date = mysqli_real_escape_string($conn, $_GET['date']);

$sql = "
SELECT *
FROM form_reports
WHERE kiosk_name = '$kiosk_name'
AND report_date = '$date'
ORDER BY report_time DESC
";

$result = mysqli_query($conn, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {

    $report_id = $row['id'];

    // 🔥 fetch images
    $imgQuery = "
        SELECT image_url 
        FROM form_report_images 
        WHERE report_id = '$report_id'
    ";

    $imgResult = mysqli_query($conn, $imgQuery);

    $images = [];

    while ($imgRow = mysqli_fetch_assoc($imgResult)) {
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
