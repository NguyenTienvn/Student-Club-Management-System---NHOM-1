<?php
require_once 'db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin user hiện tại
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();

// Lấy CLB mà user đang tham gia (giả sử mỗi người chỉ thuộc 1 CLB chính)
$stmt = $pdo->prepare("
    SELECT cm.*, c.ten_clb 
    FROM club_members cm
    JOIN clubs c ON cm.club_id = c.id
    WHERE cm.user_id = ?
");
$stmt->execute([$user_id]);
$membership = $stmt->fetch();

$club_id = $membership['club_id'] ?? null;

// Lấy danh sách thành viên trong CLB
$members = [];
if ($club_id) {
    $stmt = $pdo->prepare("
        SELECT u.ho_ten, u.username, u.email, u.so_dien_thoai, 
               cm.vai_tro, pb.ten_phong_ban
        FROM club_members cm
        JOIN users u ON cm.user_id = u.id
        LEFT JOIN phong_ban pb ON cm.phong_ban_id = pb.id
        WHERE cm.club_id = ?
        ORDER BY cm.vai_tro DESC, u.ho_ten
    ");
    $stmt->execute([$club_id]);
    $members = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thành viên - LeaderClub</title>
    <link rel="stylesheet" href="assets/css/taopb.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="page-header">
            <h1>Thành viên</h1>
            <div class="actions">
                <button class="btn-outline">Danh sách chờ</button>
                <button class="btn-outline active">Tạo phòng ban</button>
                <button class="btn-primary">+ Mời tham gia</button>
            </div>
        </header>

        <div class="main-content">
            <div class="sidebar">
                <h3>Phòng ban</h3>
                <p class="sidebar-desc">Quản lý danh sách thông tin thành viên theo từng phòng ban</p>
                <button class="btn-create-dept" onclick="openModal()">Tạo phòng ban</button>
            </div>

            <div class="content-area">
                <div class="illustration">
                    <img src="assets/images/team-illustration.svg" alt="Team">
                </div>

                <div class="table-card">
                    <table class="members-table">
                        <thead>
                            <tr>
                                <th>Thành viên CLB</th>
                                <th>Số điện thoại</th>
                                <th>Email</th>
                                <th>Phòng ban</th>
                                <th>Chức vụ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($members)): ?>
                            <tr><td colspan="5" style="text-align:center;padding:50px;color:#9CA3AF;">
                                Chưa có thành viên nào trong CLB
                            </td></tr>
                            <?php else: ?>
                                <?php foreach ($members as $m): ?>
                                <tr>
                                    <td class="member-info">
                                        <div class="avatar placeholder"></div>
                                        <div>
                                            <div class="name"><?= htmlspecialchars($m['ho_ten']) ?></div>
                                            <div class="id"><?= htmlspecialchars($m['username']) ?></div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($m['so_dien_thoai'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($m['email']) ?></td>
                                    <td><?= htmlspecialchars($m['ten_phong_ban'] ?? '-') ?></td>
                                    <td>
                                        <span class="role <?= ($m['vai_tro'] ?: 'thanh_vien') === 'chu_tich' ? 'president' : '' ?>">
                                            <?= $m['vai_tro'] === 'chu_tich' ? 'Chủ tịch' : ($m['vai_tro'] === 'chu_nhiem' ? 'Chủ nhiệm' : 'Thành viên') ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="modalContainer"></div>

    <script>
        function openModal() {
            fetch('popup_taopb.php?club_id=<?= $club_id ?>')
                .then(r => r.text())
                .then(html => {
                    document.getElementById('modalContainer').innerHTML = html;
                    document.getElementById('createDeptModal').classList.add('show');
                });
        }

        function closeModal() {
            const modal = document.getElementById('createDeptModal');
            if (modal) {
                modal.classList.remove('show');
                setTimeout(() => document.getElementById('modalContainer').innerHTML = '', 300);
            }
        }

        window.onclick = e => e.target.id === 'createDeptModal' && closeModal();
    </script>
</body>
</html>