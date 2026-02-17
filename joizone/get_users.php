<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");


include 'db.php';

$sql = "SELECT * FROM users ORDER BY uid DESC";
$res = mysqli_query($conn, $sql);

$data = [];

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode([
        "status" => true,
        "data" => $data
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Query failed"
    ]);
}
?>
