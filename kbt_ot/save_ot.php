<?php
// รับค่าจากฟอร์ม
$employee_name = $_POST['employee_name'];
$ot_hours = $_POST['ot_hours'];
$allowance = $_POST['allowance'];

$host = "localhost";
$user = "root";
$password = "";
$dbname = "kbt_ot";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$sql = "INSERT INTO overtime_records (employee_name, ot_hours, allowance) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sds", $employee_name, $ot_hours, $allowance);

if ($stmt->execute()) {
    header("Location: index.php?success=true");
} else {
    header("Location: index.php?error=true");
}

$conn->close();
exit;
?>

