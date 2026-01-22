<?php
include 'db.php';

$sql = "SELECT uid, userid, full_name, user_email, user_phone,
        branch_name, department_name, shift_start, shift_end,branch_lat,branch_long,
        status, createdAt
        FROM users ORDER BY uid DESC";

$res = mysqli_query($conn, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
?>
