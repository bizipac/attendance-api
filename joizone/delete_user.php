<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
include 'db.php';
mysqli_query($conn, "SET time_zone = '+05:30'");
date_default_timezone_set('Asia/Kolkata');


$uid = $_POST['uid'] ?? $_GET['uid'] ?? '';

if ($uid == '') {
    echo json_encode(["status"=>false,"message"=>"User ID required"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE uid=?");
$stmt->bind_param("s", $uid);

if ($stmt->execute()) {
    echo json_encode(["status"=>true,"message"=>"User deleted"]);
} else {
    echo json_encode(["status"=>false,"message"=>"Delete failed"]);
}
?>
