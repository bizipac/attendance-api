<?php
include 'db.php';

$cid   = $_POST['cid'] ?? '';
$dname = trim($_POST['dname'] ?? '');

if ($cid == '' || $dname == '') {
    echo json_encode([
        "status" => false,
        "message" => "All fields required"
    ]);
    exit;
}

$sql = "INSERT INTO department (cid, dname)
        VALUES ('$cid', '$dname')";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => true,
        "message" => "Department added successfully"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Department already exists"
    ]);
}
?>
