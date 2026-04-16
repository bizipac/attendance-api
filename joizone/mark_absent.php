<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "db.php";

date_default_timezone_set('Asia/Kolkata');

// 🔁 Previous date
$checkDate = date('Y-m-d', strtotime('-1 day'));
$nextDate  = date('Y-m-d');

// 1️⃣ Fetch all active users with shift
$users = mysqli_query($conn, "
  SELECT 
    u.uid, u.cid, u.full_name, u.department_name, u.branch_name, u.shift_id,
    s.shift_start, s.shift_end
  FROM users u
  JOIN shifts s ON s.shift_id = u.shift_id
  WHERE u.status = 'active'
");

while ($u = mysqli_fetch_assoc($users)) {

  $uid    = $u['uid'];
  $cid    = $u['cid'];
  $name   = $u['full_name'];
  $dept   = $u['department_name'];
  $office = $u['branch_name'];

  $shiftStart = $u['shift_start']; 
  $shiftEnd   = $u['shift_end'];   

  // 🌙 Night shift detection
  $isNightShift = strtotime($shiftEnd) < strtotime($shiftStart);

  // 2️⃣ Fetch ALL attendance records of that date
  $att = mysqli_query($conn, "
    SELECT id, punch_in_time, punch_out_time
    FROM attendance
    WHERE uid = '$uid'
    AND DATE(created_at) = '$checkDate'
  ");

  // ❌ No attendance → mark absent (only for day shift)
  if (mysqli_num_rows($att) == 0) {

    if (!$isNightShift) {
      mysqli_query($conn, "
        INSERT INTO attendance
        (cid, uid, name, department, office_name, status, created_at)
        VALUES
        ('$cid', '$uid', '$name', '$dept', '$office', 'ABSENT', '$checkDate 00:00:00')
      ");
    }

    continue;
  }

  // 3️⃣ Loop through ALL records (multiple punch support)
  while ($row = mysqli_fetch_assoc($att)) {

    // ⚠ If punch-in exists but punch-out missing
    if (!empty($row['punch_in_time']) && empty($row['punch_out_time'])) {

      if ($isNightShift) {
        $autoPunchOutTime = $nextDate . ' ' . $shiftEnd;
      } else {
        $autoPunchOutTime = $checkDate . ' ' . $shiftEnd;
      }

      mysqli_query($conn, "
        UPDATE attendance
        SET
          punch_out_time = '$autoPunchOutTime',
          status = 'Present',
          punch_out_remark = 'Auto punch-out: You forgot to punch out'
        WHERE id = '{$row['id']}'
      ");
    }
  }
}

echo json_encode([
  "status" => true,
  "message" => "Auto attendance processed successfully"
]);
?>
