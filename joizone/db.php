<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type");
// header("Content-Type: application/json");
// // $conn = new mysqli("localhost", "root", "", "joizone_admin");

// // if ($conn->connect_error) {
// //     die(json_encode([
// //         "status" => false,
// //         "message" => "Database connection failed"
// //     ]));
// // }

/************************ YOUR DATABASE CONNECTION START HERE   ****************************/

define ("DB_HOST", "teamunited123.cuumr2tulvkm.ap-south-1.rds.amazonaws.com:3306"); // set database host
define ("DB_USER", "Teamunited123"); // set database user
define ("DB_PASS","Teamunited123"); // set database password
define ("DB_NAME","joizone_admin"); // set database name
date_default_timezone_set('UTC');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die(json_encode([
        "status" => false,
        "message" => "Database connection failed"
    ]));
}
/*$query=mysqli_query($con,"select * from branch_master");
while($raw=mysqli_fetch_assoc($query)){
	echo $raw['branch_name'];
}

*/
?>
