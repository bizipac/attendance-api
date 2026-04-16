<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include 'db.php';

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
