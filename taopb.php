<?php
$success = false;
$error = '';
$department_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_name = trim($_POST['department_name'] ?? '');
    if (empty($department_name)) {
        $error = 'Vui lòng nhập tên phòng ban!';
    } elseif (strlen($department_name) > 50) {
        $error = 'Tên phòng ban không quá 50 ký tự!';
    } else {
        $success = true;
        $department_name = htmlspecialchars($department_name);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo phòng ban - LeaderClub</title>
    <link rel="stylesheet" href="assets/css/taopb.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="page-header">
            <h1>Thành viên</h1>
            <div class="actions">
                <button class="btn-outline">Danh sách chờ</button>
                <button class="btn-outline active">Tạo phòng ban</button>
                <button class="btn-primary">+ Mời tham gia</button>
            </div>
        </header>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Sidebar -->
            <div class="sidebar">
                <h3>Phòng ban</h3>
                <p>Quản lý danh sách thông tin thành viên theo từng phòng ban</p>
                <button class="btn-create-dept" onclick="openModal()">Tạo phòng ban</button>
            </div>

        <!-- Illustration: 4 người cầm puzzle -->
        <div class="illustration">
            <img src="assets/images/hinh1.jpg" alt="Team Puzzle">
        </div>

        <!-- Bảng thành viên -->
        <div class="table-section">
            <div class="section-header">
                <h2>Phòng ban</h2>
                <p>Quản lý danh sách thông tin thành viên theo từng phòng ban</p>
            </div>

            <div class="members-table-container">
                <table class="members-table">
                    <thead>
                        <tr>
                            <th>Thành viên CLB</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Phòng ban</th>
                            <th>Chức nhiệm</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL CONTAINER -->
    <div id="modalContainer"></div>

    <script>
        function openCreateDeptModal() {
            fetch('create-department-modal.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalContainer').innerHTML = html;
                    document.getElementById('createDeptModal').classList.add('show');
                });
        }

        // Đóng modal từ file con
        function closeModal() {
            document.getElementById('createDeptModal')?.classList.remove('show');
            setTimeout(() => {
                document.getElementById('modalContainer').innerHTML = '';
            }, 300);
        }

        // Đóng khi nhấn ngoài
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('createDeptModal');
            if (modal && e.target === modal) closeModal();
        });
    </script>
</body>
</html>