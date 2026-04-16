<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
include 'db.php';
mysqli_query($conn, "SET time_zone = '+05:30'");
date_default_timezone_set('Asia/Kolkata');

// Get all fields from POST
$uid = $_POST['uid'] ?? '';
$cid = $_POST['cid'] ?? '';
$userid = $_POST['userid'] ?? '';
$password = $_POST['password'] ?? '';
$user_token = $_POST['user_token'] ?? '';
$user_img = $_POST['user_img'] ?? '';
$imei_no = $_POST['imei_no'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$city_name = $_POST['city_name'] ?? '';
$district_name = $_POST['district_name'] ?? '';
$pin_code = $_POST['pin_code'] ?? '';
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
$last_working_date = $_POST['last_working_date'] ?? '';
$status = $_POST['status'] ?? '';
$role = $_POST['role'] ?? '';

$updatedAt = date("Y-m-d H:i:s"); // update timestamp automatically

if ($uid == '') {
    echo json_encode(["status"=>false,"message"=>"User ID required"]);
    exit;
}

// Update all fields
$sql = "UPDATE users SET
        cid='$cid',
        userid='$userid',
        password='$password',
        user_token='$user_token',
        user_img='$user_img',
        imei_no='$imei_no',
        full_name='$full_name',
        city_name='$city_name',
        district_name='$district_name',
        pin_code='$pin_code',
        user_email='$user_email',
        user_phone='$user_phone',
        gender='$gender',
        full_address='$full_address',
        branch_id='$branch_id',
        branch_name='$branch_name',
        branch_distance='$branch_distance',
        branch_lat='$branch_lat',
        branch_long='$branch_long',
        department_id='$department_id',
        department_name='$department_name',
        shift_id='$shift_id',
        shift_start='$shift_start',
        shift_end='$shift_end',
        date_of_joining='$date_of_joining',
        last_working_date='$last_working_date',
        status='$status',
        role='$role',

        updatedAt='$updatedAt'
        WHERE uid='$uid'";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["status"=>true,"message"=>"User updated successfully"]);
} else {
    echo json_encode(["status"=>false,"message"=>"Update failed"]);
}
?>
