<?php
// กำหนด Timezone เป็น Asia/Bangkok
date_default_timezone_set('Asia/Bangkok');

$host = "localhost";
$user = "root";
$password = "";
$dbname = "kbt_ot";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// รับค่าจาก GET
$employeeName = isset($_GET['employee_name']) ? trim($_GET['employee_name']) : "";
$currentDate = date("Y-m-d");

// ตรวจสอบว่าเลือกชื่อหรือไม่
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

// ดึงรายชื่อพนักงานทั้งหมด (สำหรับ dropdown)
$user_sql = "SELECT name FROM users ORDER BY name ASC";
$user_result = $conn->query($user_sql);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ข้อมูลโอทีพนักงาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background-color: #f0f4f8;
            padding-top: 80px;
        }

        .table-container {
            max-width: 95%;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: rgb(13, 66, 37);
            margin-bottom: 30px;
        }

        table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        thead th {
            background-color: rgb(5, 87, 38);
            color: white;
            text-align: center;
        }

        tbody td {
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top" style="padding: 1.5rem 2rem;">
        <div class="container-fluid">
            <a class="navbar-brand" href="./index.php">
                <h4>KBT OT / ALLOWANCE</h4>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php" style="font-size: 1.2rem; padding: 0.8rem 1.5rem; border-radius: 10px; border: 2px solid transparent;" onmouseover="this.style.borderColor='#fff'" onmouseout="this.style.borderColor='transparent'">หน้าแรก</a>
                    </li>&nbsp;&nbsp;
                    <li class="nav-item">
                        <a class="nav-link active" href="ot_view.php" style="font-size: 1.2rem; padding: 0.8rem 1.5rem; border-radius: 10px; border: 2px solid white;">ดูข้อมูล OT และ เบี้ยเลี้ยง</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <br>
    <br>

    <!-- ฟอร์มค้นหา -->
    <div class="container mb-4">
        <form method="GET" class="row g-3 justify-content-center">
            <div class="col-auto">
                <label for="employee_name" class="col-form-label">เลือกพนักงาน:</label>
            </div>
            <div class="col-auto">
                <select name="employee_name" id="employee_name" class="form-select">
                    <!-- <option value="">-- กรุณาเลือกพนักงาน --</option> -->
                    <option value="">-- พนักงานที่ทำโอทีในวันนี้ --</option>
                    <?php
                    if ($user_result->num_rows > 0) {
                        while ($user = $user_result->fetch_assoc()) {
                            $selected = ($employeeName === $user['name']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($user['name']) . '" ' . $selected . '>'
                                . htmlspecialchars($user['name']) . '</option>';
                        }
                    } else {
                        echo '<option value="">ไม่พบข้อมูลพนักงาน</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-success">ค้นหา</button>
            </div>
        </form>
    </div>

    <hr>
    <br>

    <!-- ตารางข้อมูล OT -->
    <div class="table-container">
        <h2>ตารางข้อมูลโอที / เบี้ยเลี้ยง</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>ลำดับ</th>
                        <th>ชื่อพนักงาน</th>
                        <th>ชั่วโมง OT</th>
                        <th>เบี้ยเลี้ยง (บาท)</th>
                        <th>วันที่บันทึก</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        $i = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$i}</td>
                                <td>" . htmlspecialchars($row['employee_name']) . "</td>
                                <td>" . htmlspecialchars($row['ot_hours']) . "</td>
                                <td>" . htmlspecialchars($row['allowance']) . "</td>
                                <td>" . htmlspecialchars($row['created_at']) . "</td>
                              </tr>";
                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>ยังไม่มีการลง OT สำหรับวันนี้</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ปุ่มดาวน์โหลด Excel -->
    <center>
        <?php if ($result && $result->num_rows > 0): ?>
            <form action="export_excel.php" method="post">
                <input type="hidden" name="employee_name" value="<?= htmlspecialchars($employeeName) ?>">
                <button id="exportExcelBtn" class="btn btn-success" style="margin-bottom: 10px;">
                    <i class="fas fa-file-excel"></i> ดาวน์โหลดเป็น Excel
                </button>
            </form>
        <?php endif; ?>
    </center>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php
$conn->close();
?>