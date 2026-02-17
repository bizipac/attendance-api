<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

if(!isset($_GET['uid']) || !isset($_GET['date'])){
    echo json_encode([
        "status" => false,
        "message" => "uid and date are required"
    ]);
    exit;
}

$uid  = mysqli_real_escape_string($conn, $_GET['uid']);
$date = mysqli_real_escape_string($conn, $_GET['date']); // YYYY-MM-DD

$sql = "
SELECT *
FROM form_reports 
WHERE uid = '$uid'
AND report_date = '$date'
ORDER BY report_time DESC
";

$result = mysqli_query($conn, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {

    $report_id = $row['id'];

    // 🔹 Fetch images for this report
    $imageQuery = "
        SELECT image_url 
        FROM form_report_images 
        WHERE report_id = '$report_id'
    ";

    $imageResult = mysqli_query($conn, $imageQuery);

    $images = [];

    while ($imgRow = mysqli_fetch_assoc($imageResult)) {
        $images[] = $imgRow['image_url'];
    }

    // 🔹 Attach images array
    $row['image_urls'] = $images;

    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);

$conn->close();
?>
