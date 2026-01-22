<?php
include 'db.php';

$userid   = $_POST['userid'] ?? '';
$password = $_POST['password'] ?? '';
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

if ($userid == '' || $password == '' || $full_name == '') {
    echo json_encode(["status"=>false,"message"=>"Required fields missing"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$sql = "INSERT INTO users (
    userid, password, imei_no,
    full_name, user_email, user_phone, gender, full_address,
    branch_id, branch_name, branch_distance, branch_lat, branch_long,
    department_id, department_name,
    shift_id, shift_start, shift_end,
    date_of_joining
) VALUES (
    '$userid','$hashedPassword','$imei_no',
    '$full_name','$user_email','$user_phone','$gender','$full_address',
    '$branch_id','$branch_name','$branch_distance','$branch_lat','$branch_long',
    '$department_id','$department_name',
    '$shift_id','$shift_start','$shift_end',
    '$date_of_joining'
)";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["status"=>true,"message"=>"User created"]);
} else {
    echo json_encode(["status"=>false,"message"=>"User already exists"]);
}
?>
