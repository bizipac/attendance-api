<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Stop HTML warnings from breaking JSON
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php';

/* ================= GET DATA ================= */

$cid = $_POST['cid'] ?? '';
$userid   = $_POST['userid'] ?? '';
$password = $_POST['password'] ?? '';
$user_token = $_POST['user_token'] ?? '';
$user_img = $_POST['user_img'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$user_email = $_POST['user_email'] ?? '';
$user_phone = $_POST['user_phone'] ?? '';
$gender = $_POST['gender'] ?? '';
$full_address = $_POST['full_address'] ?? '';

$branch_id = $_POST['branch_id'] ?? '';
$branch_name = $_POST['branch_name'] ?? '';
$branch_distance = $_POST['branch_distance'] ?? '';
$branch_lat = $_POST['branch_lat'] ?? '';
$branch_long = $_POST['branch_long'] ?? '';

$department_id = $_POST['department_id'] ?? '';
$department_name = $_POST['department_name'] ?? '';

$shift_id = $_POST['shift_id'] ?? '';
$shift_start = $_POST['shift_start'] ?? '';
$shift_end = $_POST['shift_end'] ?? '';

$date_of_joining = $_POST['date_of_joining'] ?? '';
$imei_no = $_POST['imei_no'] ?? '';

/* ================= VALIDATION ================= */

if ($cid == '' || $userid == '' || $password == '' || $full_name == '') {
    echo json_encode([
        "status" => false,
        "message" => "Required fields missing"
    ]);
    exit;
}

/* ================= INSERT ================= */

// ⚠️ For now simple query (later we can secure it)
$sql = "INSERT INTO users (
    cid, userid, password, user_token, user_img, imei_no,
    full_name, user_email, user_phone, gender, full_address,
    branch_id, branch_name, branch_distance, branch_lat, branch_long,
    department_id, department_name,
    shift_id, shift_start, shift_end,
    date_of_joining
) VALUES (
    '$cid','$userid','$password','$user_token','$user_img','$imei_no',
    '$full_name','$user_email','$user_phone','$gender','$full_address',
    '$branch_id','$branch_name','$branch_distance','$branch_lat','$branch_long',
    '$department_id','$department_name',
    '$shift_id','$shift_start','$shift_end',
    '$date_of_joining'
)";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => true,
        "message" => "User created successfully"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "User already exists"
    ]);
}
?>
