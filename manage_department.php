<?php
session_start();
require_once('assets/database/connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Lấy ID phòng ban
$pb_id = isset($_GET['pb_id']) ? (int)$_GET['pb_id'] : 0;
if ($pb_id <= 0) {
    echo "Phòng ban không hợp lệ!";
    exit;
}

// Lấy thông tin phòng ban
$stmt = $conn->prepare("SELECT ten_phong_ban, chuc_nang_nhiem_vu FROM phong_ban WHERE id = ?");
$stmt->bind_param("i", $pb_id);
$stmt->execute();
$result = $stmt->get_result();
$phongban = $result->fetch_assoc();
$stmt->close();

if (!$phongban) {
    echo "Không tìm thấy phòng ban!";
    exit;
}

// Lấy danh sách thành viên phòng ban
$members = $conn->prepare("
    SELECT u.ho_ten AS fullname, u.email, u.so_dien_thoai AS phone
    FROM club_members cm
    JOIN users u ON u.id = cm.user_id
    WHERE cm.phong_ban_id = ?
      AND cm.trang_thai = 'dang_hoat_dong'
");
$members->bind_param("i", $pb_id);
$members->execute();
$listMembers = $members->get_result();
$members->close();

$so_thanh_vien = $listMembers->num_rows;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý ban <?= htmlspecialchars($phongban['ten_phong_ban']) ?></title>
<link rel="stylesheet" href="assets/css/manage_department.css">
<link rel="stylesheet" href="assets/css/popup_add_member.css">

</head>

<body>
<div class="header-row">
    <a href="taopb.php" class="btn-back">&#10094;</a>
    <h2>Quản lý ban <?= htmlspecialchars($phongban['ten_phong_ban']) ?></h2>
</div>

<div class="dept-box">
    <div class="dept-img">
        <img src="assets/img/hinhphongban.jpg" alt="Ảnh minh hoạ">
    </div>

    <div class="dept-info">
        <p>Phòng ban:</p>
        <h3><?= htmlspecialchars($phongban['ten_phong_ban']) ?></h3>

        <p>- <?= htmlspecialchars($phongban['chuc_nang_nhiem_vu']) ?></p>
        <p><?= $so_thanh_vien ?> thành viên</p>
    </div>

    <button class="btn-add" onclick="openAddMemberPopup(<?= $pb_id ?>)">
        Thêm thành viên
    </button>

</div>

<!-- Bảng danh sách thành viên -->
<table class="member-table">
    <thead>
        <tr>
            <th>Thành viên phòng ban</th>
            <th>Số điện thoại</th>
            <th>Email</th>
            <th>Phòng ban</th>
        </tr>
    </thead>

    <tbody>
        <?php if ($so_thanh_vien == 0): ?>
            <tr><td colspan="4" style="text-align:center;">Chưa có thành viên nào</td></tr>
        <?php else: ?>
            <?php while ($m = $listMembers->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($m['fullname']) ?></td>
                    <td><?= htmlspecialchars($m['phone']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($phongban['ten_phong_ban']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php include 'popup_add_member.php'; ?>
<script>
function openAddMemberPopup(pb_id) {
    document.getElementById("popup_add_member").classList.add("show");
    document.getElementById("pb_id_input").value = pb_id; // gán id phòng ban vào form popup
}

function closeAddMemberPopup() {
    document.getElementById("popup_add_member").classList.remove("show");
}
</script>
<script src="assets/js/popup_add_member.js"></script>

</body>
</html>
