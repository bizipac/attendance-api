<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

include "db.php";

$branch_name = $_POST['branch_name'] ?? '';
$distance    = $_POST['distance'] ?? '';
$cid         = $_POST['cid'] ?? '';
$lat         = $_POST['branch_lat'] ?? null;
$long        = $_POST['branch_long'] ?? null;

if ($branch_name=="" || $distance=="" || $cid=="") {
    echo json_encode(["status"=>false,"message"=>"Required fields missing"]);
    exit;
}

$sql = "INSERT INTO branch (branch_name,distance,cid,branch_lat,branch_long)
        VALUES ('$branch_name','$distance','$cid','$lat','$long')";

if ($conn->query($sql)) {
    echo json_encode(["status"=>true,"message"=>"Branch created successfully"]);
} else {
    echo json_encode(["status"=>false,"message"=>"Insert failed"]);
}
?>
