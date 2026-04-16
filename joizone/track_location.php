<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";
mysqli_query($conn, "SET time_zone = '+05:30'");
date_default_timezone_set('Asia/Kolkata');

$createdAt = date("Y-m-d H:i:s");
$attendance_id = $_POST['attendance_id'] ?? '';
$status = $_POST['status'] ?? 'active';
$lat = $_POST['lat'] ?? null;
$lng = $_POST['lng'] ?? null;

if ($attendance_id == '') {
  echo json_encode(["status"=>false,"message"=>"Attendance ID missing"]);
  exit;
}

/* 🛑 STOP TRACKING */
if ($status === 'stop') {

  mysqli_query($conn, "
    UPDATE attendance_locations
    SET tracking_status = 'stopped'
    WHERE id = '$attendance_id'
  ");

  echo json_encode([
    "status" => true,
    "message" => "Location tracking stopped"
  ]);
  exit;
}

/* ✅ ACTIVE TRACKING */
if ($lat == '' || $lng == '') {
  echo json_encode(["status"=>false,"message"=>"Lat/Lng missing"]);
  exit;
}

mysqli_query($conn, "
  INSERT INTO attendance_locations (attendance_id, latitude, longitude,created_at)
  VALUES ('$attendance_id','$lat','$lng','$createdAt')
");

echo json_encode(["status"=>true,"message"=>"Location saved"]);
?>