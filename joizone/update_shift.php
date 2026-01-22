<?php
include 'db.php';

$shift_id    = $_POST['shift_id'] ?? '';
$shift_start = $_POST['shift_start'] ?? '';
$shift_end   = $_POST['shift_end'] ?? '';

if ($shift_id == '') {
    echo json_encode([
        "status" => false,
        "message" => "Shift ID required"
    ]);
    exit;
}

$sql = "UPDATE shifts 
        SET shift_start='$shift_start', shift_end='$shift_end'
        WHERE shift_id='$shift_id'";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => true,
        "message" => "Shift updated"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Update failed"
    ]);
}
?>
