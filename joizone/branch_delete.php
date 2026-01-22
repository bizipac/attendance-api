<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

include "db.php";

$id = $_POST['id'] ?? '';

if ($id=="") {
    echo json_encode(["status"=>false,"message"=>"Branch ID required"]);
    exit;
}

$sql = "DELETE FROM branch WHERE id='$id'";

if ($conn->query($sql)) {
    echo json_encode(["status"=>true,"message"=>"Branch deleted"]);
} else {
    echo json_encode(["status"=>false,"message"=>"Delete failed"]);
}
?>
