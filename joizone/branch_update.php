<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "db.php";

/* ----------------------------------------
   🔹 GET DATA (Supports JSON + FORM-DATA)
-----------------------------------------*/

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    $id          = $data['id'] ?? '';
    $branch_name = $data['branch_name'] ?? '';
    $distance    = $data['distance'] ?? '';
    $lat         = $data['branch_lat'] ?? null;
    $long        = $data['branch_long'] ?? null;
} else {
    $id          = $_POST['id'] ?? '';
    $branch_name = $_POST['branch_name'] ?? '';
    $distance    = $_POST['distance'] ?? '';
    $lat         = $_POST['branch_lat'] ?? null;
    $long        = $_POST['branch_long'] ?? null;
}

/* ----------------------------------------
   🔹 VALIDATION
-----------------------------------------*/

if (empty($id)) {
    echo json_encode([
        "status" => false,
        "message" => "Branch ID required"
    ]);
    exit;
}

/* Convert empty lat/long to NULL */
$lat  = ($lat === '' || $lat === null) ? null : (float)$lat;
$long = ($long === '' || $long === null) ? null : (float)$long;
$distance = ($distance === '' || $distance === null) ? null : (float)$distance;

/* ----------------------------------------
   🔹 START TRANSACTION
-----------------------------------------*/

$conn->begin_transaction();

try {

    /* ----------------------------------------
       1️⃣ Update branch table
    -----------------------------------------*/
    $stmt1 = $conn->prepare("
        UPDATE branch SET
            branch_name = ?,
            distance    = ?,
            branch_lat  = ?,
            branch_long = ?
        WHERE id = ?
    ");

    $stmt1->bind_param(
        "sdddi",
        $branch_name,
        $distance,
        $lat,
        $long,
        $id
    );

    if (!$stmt1->execute()) {
        throw new Exception("Branch update failed: " . $stmt1->error);
    }

    /* ----------------------------------------
       2️⃣ Update users table
    -----------------------------------------*/
    $stmt2 = $conn->prepare("
        UPDATE users SET
            branch_name     = ?,
            branch_distance = ?,
            branch_lat      = ?,
            branch_long     = ?
        WHERE branch_id = ?
    ");

    $stmt2->bind_param(
        "sdddi",
        $branch_name,
        $distance,
        $lat,
        $long,
        $id
    );

    if (!$stmt2->execute()) {
        throw new Exception("Users update failed: " . $stmt2->error);
    }

    /* ----------------------------------------
       🔹 COMMIT
    -----------------------------------------*/
    $conn->commit();

    echo json_encode([
        "status"  => true,
        "message" => "Branch and related users updated successfully"
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        "status"  => false,
        "message" => "Update failed",
        "error"   => $e->getMessage()
    ]);
}

$conn->close();
?>