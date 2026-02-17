<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "db.php";

date_default_timezone_set("Asia/Kolkata");

if (!isset($_POST['uid'])) {
    echo json_encode([
        "status" => false,
        "message" => "uid required"
    ]);
    exit;
}

$uid = $_POST['uid'];

$sql = "
SELECT 
    al.id,
    a.name,
    u.branch_lat,
    u.branch_long,
    al.latitude,
    al.longitude,
    al.created_at
FROM attendance a
INNER JOIN users u
    ON a.uid = u.uid
INNER JOIN attendance_locations al
    ON a.id = al.attendance_id
WHERE a.uid = ?
AND DATE(a.created_at) = CURDATE()
AND DATE(al.created_at) = CURDATE()
ORDER BY al.created_at ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
?>
