<?php
include 'db.php';

$cid = $_GET['cid'] ?? '';

$sql = "SELECT * FROM shifts WHERE cid='$cid' ORDER BY shift_id DESC";
$res = mysqli_query($conn, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
?>
