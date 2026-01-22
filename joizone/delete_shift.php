<?php
include 'db.php';

$shift_id = $_POST['shift_id'] ?? '';

if ($shift_id == '') {
    echo json_encode([
        "status" => false,
        "message" => "Shift ID required"
    ]);
    exit;
}

$sql = "DELETE FROM shifts WHERE shift_id='$shift_id'";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => true,
        "message" => "Shift deleted"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Delete failed"
    ]);
}
?>
