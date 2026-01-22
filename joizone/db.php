<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
$conn = new mysqli("localhost", "root", "", "joizone_admin");

if ($conn->connect_error) {
    die(json_encode([
        "status" => false,
        "message" => "Database connection failed"
    ]));
}
?>
