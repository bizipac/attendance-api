<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['rows'])) {
    echo json_encode(["status"=>false,"message"=>"No data received"]);
    exit;
}

$conn->query("CREATE TEMPORARY TABLE temp_excel (
    application_no VARCHAR(50),
    remark VARCHAR(255)
)");

$stmt = $conn->prepare("INSERT INTO temp_excel (application_no,remark) VALUES (?,?)");

foreach ($data['rows'] as $row) {

    $application_no = $row['application_number'];
    $remark = $row['remark'];
 

    $stmt->bind_param("ss",$application_no,$remark);
    $stmt->execute();
}

$conn->query("
UPDATE form_reports fr
JOIN temp_excel te ON fr.application_no = te.application_no
SET 
fr.bank_remark_status = te.remark,
fr.bank_update_status = 'success'
");

echo json_encode([
    "status"=>true,
    "message"=>"Excel remark update completed"
]);

$conn->close();
?>