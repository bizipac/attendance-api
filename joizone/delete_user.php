<?php
include 'db.php';

$uid = $_POST['uid'] ?? '';

if ($uid == '') {
    echo json_encode(["status"=>false,"message"=>"User ID required"]);
    exit;
}

$sql = "DELETE FROM users WHERE uid='$uid'";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["status"=>true,"message"=>"User deleted"]);
} else {
    echo json_encode(["status"=>false,"message"=>"Delete failed"]);
}
?>
