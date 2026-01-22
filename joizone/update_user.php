<?php
include 'db.php';

$uid = $_POST['uid'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$user_email = $_POST['user_email'] ?? '';
$user_phone = $_POST['user_phone'] ?? '';
$status = $_POST['status'] ?? 'active';

if ($uid == '') {
    echo json_encode(["status"=>false,"message"=>"User ID required"]);
    exit;
}

$sql = "UPDATE users SET
        full_name='$full_name',
        user_email='$user_email',
        user_phone='$user_phone',
        status='$status'
        WHERE uid='$uid'";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["status"=>true,"message"=>"User updated"]);
} else {
    echo json_encode(["status"=>false,"message"=>"Update failed"]);
}
?>
