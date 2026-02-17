<?php
error_reporting(0);
date_default_timezone_set('Asia/Kolkata'); // ✅ ADD THIS LINE
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

// Required fields
$required = [
    'uid','user_id','user_name','city_name','application_no',
    'relation','variant','status','remarks','contact_no',
    'gps_location','kiosk_name','image_urls'
];

foreach($required as $field){
    if(!isset($data[$field]) || empty($data[$field])){
        echo json_encode([
            "status"=>false,
            "message"=>"$field is required"
        ]);
        exit();
    }
}

// Assign variables
$uid = $data['uid'];
$user_id = $data['user_id'];
$user_name = $data['user_name'];
$city_name = $data['city_name'];
$application_no = $data['application_no'];
$relation = $data['relation'];
$variant = $data['variant'];
$status = $data['status'];
$remarks = $data['remarks'];
$contact_no = $data['contact_no'];
$gps_location = $data['gps_location'];
$kiosk_name = $data['kiosk_name'];
$image_urls = $data['image_urls']; // array

$report_date = date("Y-m-d");
$report_time = date("H:i:s");

$conn->begin_transaction();

try {

    // 1️⃣ Insert main report
    $stmt = $conn->prepare("
        INSERT INTO form_reports 
        (uid,user_id,user_name,city_name,report_date,report_time,
         application_no,relation,variant,status,remarks,contact_no,gps_location,kiosk_name)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "ssssssssssssss",
        $uid,$user_id,$user_name,$city_name,$report_date,$report_time,
        $application_no,$relation,$variant,$status,$remarks,$contact_no,$gps_location,$kiosk_name
    );

    $stmt->execute();

    // 2️⃣ Get inserted report ID
    $report_id = $conn->insert_id;

    // 3️⃣ Insert multiple images
    $imgStmt = $conn->prepare("
        INSERT INTO form_report_images (report_id,image_url)
        VALUES (?,?)
    ");

    foreach($image_urls as $url){
        $imgStmt->bind_param("is", $report_id, $url);
        $imgStmt->execute();
    }

    $conn->commit();

    echo json_encode([
        "status"=>true,
        "message"=>"Report added successfully",
        "report_id"=>$report_id
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        "status"=>false,
        "message"=>$e->getMessage()
    ]);
}

$conn->close();
?>
