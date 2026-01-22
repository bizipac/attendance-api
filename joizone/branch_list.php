<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "db.php";

$cid = $_GET['cid'] ?? '';

if ($cid=="") {
    echo json_encode(["status"=>false,"message"=>"CID required"]);
    exit;
}

$sql = "SELECT * FROM branch WHERE cid='$cid' ORDER BY id DESC";
$res = $conn->query($sql);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status"=>true,
    "branches"=>$data
]);
?>
