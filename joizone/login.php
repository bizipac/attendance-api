<?php
include "db.php";
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
$user_id  = $_POST['user_id'] ?? '';
$password = $_POST['password'] ?? '';

if ($user_id == "" || $password == "") {
    echo json_encode([
        "status" => false,
        "message" => "User ID & Password required"
    ]);
    exit;
}

$sql = "SELECT * FROM admin_users WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();

    if ($password === $row['password']) {
        echo json_encode([
            "status" => true,
            "message" => "Login Successful",
            "data" => [
                "cid" => $row['cid'],
                "user_id" => $row['user_id'],
                "role" => $row['role']
            ]
        ]);
    } else {
        echo json_encode(["status" => false, "message" => "Wrong Password"]);
    }
} else {
    echo json_encode(["status" => false, "message" => "User not found"]);
}
?>
