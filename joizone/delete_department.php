<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
include 'db.php';

$id = $_POST['id'] ?? '';

if ($id == '') {
    echo json_encode([
        "status" => false,
        "message" => "Department ID required"
    ]);
    exit;
}

$sql = "DELETE FROM department WHERE id='$id'";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => true,
        "message" => "Department deleted"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Delete failed"
    ]);
}
?>
