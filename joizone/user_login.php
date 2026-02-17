<?php
include "db.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get POST data
$userid  = $_POST['userid'] ?? '';
$password = $_POST['password'] ?? '';
$imei_no = $_POST['imei_no'] ?? ''; // Android device ID

if ($userid == "" || $password == "" || $imei_no == "") {
    echo json_encode([
        "status" => false,
        "message" => "User ID, Password & IMEI required"
    ]);
    exit;
}

// Check user exists and active
$sql = "SELECT * FROM users WHERE userid = '$userid' AND status='active' AND role='user'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();

    // Check password
    if ($password === $user['password']) {
        // Check imei_no
        if ($user['imei_no'] == "" || $user['imei_no'] == $imei_no) {
            // First time login or same device, update imei_no if empty
            if ($user['imei_no'] == "") {
                $update_sql = "UPDATE users SET imei_no='$imei_no' WHERE uid='".$user['uid']."'";
                $conn->query($update_sql);
            }

            // Login successful
            echo json_encode([
                "status" => true,
                "message" => "Login successful",
                "data" => [
                    "cid" => $user['cid'],
                    "uid" => $user['uid'],
                    "userid" => $user['userid'],
                    "userName" => $user['full_name'],
                     "userPassword" => $user['password'],
                      "user_token" => $user['user_token'],
                      "imei_no"=>$user['imei_no'],
                     "userEmail" => $user['user_email'],
                      "userPhone" => $user['user_phone'],
                       "userImg" => $user['user_img'],
                        "userGender" => $user['gender'],
                        "full_address"=>$user['full_address'],
                        "storeId"=>$user['branch_id'],
                         "storeName" => $user['branch_name'],
                         "storeLat"=>$user['branch_lat'],
                         "storeLong"=>$user['branch_long'],
                         "storeDistance"=>$user['branch_distance'],
                         "department_id"=>$user['department_id'],
                         "department_name"=>$user['department_name'],
                         "shift_id"=>$user['shift_id'],
                         "shift_start"=>$user['shift_start'],
                         "shift_end"=>$user['shift_end'],
                         "date_of_joining"=>$user['date_of_joining'],
                         "status"=>$user['status'],
                    "role" => $user['role'],
                    "createdAt"=>$user['createdAt'],
                    "updatedAt"=>$user['updatedAt']
                ]
            ]);
        } else {
            // Different device
            echo json_encode([
                "status" => false,
                "message" => "This account is already logged in on another device"
            ]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "Wrong password"]);
    }
} else {
    echo json_encode(["status" => false, "message" => "User not found or inactive"]);
}

?>
