<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "kbt_ot";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Bangkok');
$currentDate = date("Y-m-d");

$employeeName = isset($_POST['employee_name']) ? trim($_POST['employee_name']) : "";

// ตั้งค่าหัวข้อให้เป็น Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=overtime_records_" . date("Y-m-d") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th>ลำดับ</th>
        <th>ชื่อพนักงาน</th>
        <th>ชั่วโมง OT</th>
        <th>เบี้ยเลี้ยง (บาท)</th>
        <th>วันที่บันทึก</th>
      </tr>";

if ($employeeName !== "") {
    $sql = "SELECT * FROM overtime_records WHERE employee_name LIKE ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $searchName = '%' . $employeeName . '%';
    $stmt->bind_param("s", $searchName);
} else {
    $sql = "SELECT * FROM overtime_records WHERE DATE(created_at) = ? ORDER BY created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $currentDate);
}

$stmt->execute();
$result = $stmt->get_result();

$num = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$num}</td>
            <td>" . htmlspecialchars($row['employee_name']) . "</td>
            <td>" . htmlspecialchars($row['ot_hours']) . "</td>
            <td>" . htmlspecialchars($row['allowance']) . "</td>
            <td>" . htmlspecialchars($row['created_at']) . "</td>
          </tr>";
    $num++;
}

echo "</table>";
$conn->close();
?>
