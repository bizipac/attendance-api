<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

include "db.php";

$id          = $_POST['id'] ?? '';
$branch_name = $_POST['branch_name'] ?? '';
$distance    = $_POST['distance'] ?? '';
$lat         = $_POST['branch_lat'] ?? null;
$long        = $_POST['branch_long'] ?? null;

if ($id=="") {
    echo json_encode(["status"=>false,"message"=>"Branch ID required"]);
    exit;
}

$sql = "UPDATE branch SET
        branch_name='$branch_name',
        distance='$distance',
        branch_lat='$lat',
        branch_long='$long'
        WHERE id='$id'";

if ($conn->query($sql)) {
    echo json_encode(["status"=>true,"message"=>"Branch updated"]);
} else {
    echo json_encode(["status"=>false,"message"=>"Update failed"]);
}
?>
