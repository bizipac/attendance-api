<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

date_default_timezone_set('Asia/Kolkata');
// Get POST data
$id = isset($_POST['id']) ? $_POST['id'] : null;
$duplicate_from = isset($_POST['duplicate_from']) ? $_POST['duplicate_from'] : null;

// Validate input
if (!$id || !$duplicate_from) {
    echo json_encode([
        "status" => false,
        "message" => "Missing required parameters"
    ]);
    exit;
}

// Check if report exists
$checkSql = "SELECT * FROM form_reports WHERE id = ?";
$stmtCheck = $conn->prepare($checkSql);
$stmtCheck->bind_param("i", $id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows === 0) {
    echo json_encode([
        "status" => false,
        "message" => "Report not found"
    ]);
    $stmtCheck->close();
    $conn->close();
    exit;
}
$stmtCheck->close();

// Update duplicate_from column
$updateSql = "UPDATE form_reports SET duplicate_from = ? WHERE id = ?";
$stmtUpdate = $conn->prepare($updateSql);
$stmtUpdate->bind_param("si", $duplicate_from, $id);

if ($stmtUpdate->execute()) {
    echo json_encode([
        "status" => true,
        "message" => "Duplicate status updated successfully",
        "id" => $id,
        "duplicate_from" => $duplicate_from
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Failed to update duplicate status"
    ]);
}

$stmtUpdate->close();
$conn->close();
?>