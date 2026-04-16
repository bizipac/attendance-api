<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
include 'db.php';

$id    = $_POST['id'] ?? '';
$dname = trim($_POST['dname'] ?? '');

if ($id == '' || $dname == '') {
    echo json_encode([
        "status" => false,
        "message" => "Invalid input"
    ]);
    exit;
}

$sql = "UPDATE department SET dname='$dname' WHERE id='$id'";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => true,
        "message" => "Department updated"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Update failed"
    ]);
}
?>
