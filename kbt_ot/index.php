<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "kbt_ot";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ดึงรายชื่อพนักงานจากตาราง users
$user_result = $conn->query("SELECT id, name FROM users");
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>บันทึก OT / เบี้ยเลี้ยง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts: Noto Sans Thai -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background-color: #f0f4f8;
            padding-top: 80px;
        }

        .form-container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: rgb(18, 74, 51);
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: bold;
            margin-top: 10px;
        }

        select,
        button {
            border-radius: 8px !important;
        }

        button {
            margin-top: 20px;
        }
    </style>
</head>

<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top" style="padding: 1.5rem 2rem;">
    <div class="container-fluid">
        <!-- Logo แทนข้อความ -->
        <a class="navbar-brand" href="./index.php">
           <h4>KBT OT / ALLOWANCE</h4>
        </a>
        
        <!-- ปุ่มย่อขยาย navbar -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- เนื้อหาของเมนู -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php" style="font-size: 1.2rem; padding: 0.8rem 1.5rem; border-radius: 10px; border: 2px solid white;">หน้าแรก</a>
                </li>&nbsp; &nbsp;
                <li class="nav-item">
                    <a class="nav-link" href="ot_view.php" style="font-size: 1.2rem; padding: 0.8rem 1.5rem; border-radius: 10px; border: 2px solid transparent;" onmouseover="this.style.borderColor='#fff'" onmouseout="this.style.borderColor='transparent'">ดูข้อมูล OT และ เบี้ยเลี้ยง</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<br>

    <br>
    <br>
    <br>

    <!-- ฟอร์ม -->
    <div class="form-container">
        <h2>บันทึก OT / เบี้ยเลี้ยง</h2>
        <form method="post" action="save_ot.php">
            <!-- ชื่อพนักงาน -->
            <label for="employee_name" class="form-label">ชื่อพนักงาน:</label>
            <select class="form-select" name="employee_name" id="employee_name" required>
                <option value="">-- กรุณาเลือกพนักงาน --</option>
                <?php
                if ($user_result->num_rows > 0) {
                    while ($user = $user_result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($user['name']) . '">' . htmlspecialchars($user['name']) . '</option>';
                    }
                } else {
                    echo '<option value="">ไม่พบข้อมูลพนักงาน</option>';
                }
                ?>
            </select>

            <!-- ชั่วโมง OT -->
            <label for="ot_hours" class="form-label">ชั่วโมง OT:</label>
            <select class="form-select" name="ot_hours" id="ot_hours" required>
                <option value="ไม่ระบุ">-- กรุณาเลือกชั่วโมง OT --</option>
                <?php
                for ($i = 1.0; $i <= 6.5; $i += 0.5) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
                ?>
            </select>

            <!-- เบี้ยเลี้ยง -->
            <label for="allowance" class="form-label">เบี้ยเลี้ยง:</label>
            <select class="form-select" name="allowance" id="allowance" required>
                <option value="ไม่ระบุ">-- กรุณาเลือกเบี้ยเลี้ยง --</option>
                <option value="150">150 บาท</option>
                <option value="300">300 บาท</option>
            </select>

            <!-- ปุ่มบันทึก -->
            <button type="submit" class="btn btn-success w-100">บันทึก</button>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === 'true') {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกข้อมูลสำเร็จ',
                    showConfirmButton: false,
                    timer: 1400
                });
            } else if (urlParams.get('error') === 'true') {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาดในการบันทึก',
                    showConfirmButton: true
                });
            }
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>