<?php
include 'db.php';

$cid         = $_POST['cid'] ?? '';
$shift_start = $_POST['shift_start'] ?? '';
$shift_end   = $_POST['shift_end'] ?? '';

if ($cid == '' || $shift_start == '' || $shift_end == '') {
    echo json_encode([
        "status" => false,
        "message" => "All fields required"
    ]);
    exit;
}

$sql = "INSERT INTO shifts (cid, shift_start, shift_end)
        VALUES ('$cid', '$shift_start', '$shift_end')";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => true,
        "message" => "Shift added successfully"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Failed to add shift"
    ]);
}
?>
