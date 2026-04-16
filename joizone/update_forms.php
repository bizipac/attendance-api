<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

date_default_timezone_set('Asia/Kolkata');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(["status" => false, "message" => "Missing ID"]);
    exit;
}

$application_no = $_POST['application_no'] ?? null;
$relation = $_POST['relation'] ?? null;
$variant = $_POST['variant'] ?? null;
$status = $_POST['status'] ?? null;
$remarks = $_POST['remarks'] ?? null;
$remark_managers = $_POST['manager_remark'] ?? null;

$sql = "UPDATE form_reports SET
    application_no = ?,
    relation = ?,
    variant = ?,
    status = ?,
    remarks = ?,
    remark_manager  = ?
    WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssi",
    $application_no,
    $relation,
    $variant,
    $status,
    $remarks,
    $remark_managers,
    $id
);

if ($stmt->execute()) {
    echo json_encode(["status" => true, "message" => "Updated successfully"]);
} else {
    echo json_encode(["status" => false, "message" => "Update failed"]);
}

$stmt->close();
$conn->close();
?>