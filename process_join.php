<?php
session_start();
include __DIR__ . '/assets/database/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Nhận dữ liệu từ form
$club_id = isset($_POST['club_id']) ? (int)$_POST['club_id'] : 0;
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$so_dien_thoai = isset($_POST['so_dien_thoai']) ? trim($_POST['so_dien_thoai']) : '';
$loi_nhan = isset($_POST['loi_nhan']) ? trim($_POST['loi_nhan']) : '';

// Kiểm tra dữ liệu bắt buộc
if ($club_id <= 0 || $user_id <= 0 || empty($so_dien_thoai)) {
    $_SESSION['join_error'] = "Vui lòng điền đầy đủ thông tin bắt buộc!";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

// Kiểm tra xem người dùng đã là thành viên chưa
$check = $conn->prepare("SELECT id FROM club_members WHERE club_id = ? AND user_id = ?");
$check->bind_param("ii", $club_id, $user_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $_SESSION['join_error'] = "Bạn đã là thành viên của CLB này!";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}
$check->close();

try {
    $stmt = $conn->prepare("INSERT INTO club_members (club_id, user_id, so_dien_thoai, loi_nhan, vai_tro, trang_thai) VALUES (?, ?, ?, ?, 'thanh_vien', 'cho_duyet')");
    $stmt->bind_param("iiss", $club_id, $user_id, $so_dien_thoai, $loi_nhan);
    $result = $stmt->execute();
    $stmt->close();

if ($result) {
    $_SESSION['join_success'] = "Đã gửi yêu cầu tham gia CLB thành công! Vui lòng chờ duyệt.";
  } else {
        $_SESSION['join_error'] = "❌ Có lỗi xảy ra! Vui lòng thử lại.";
    }
} catch (Exception $e) {
    $_SESSION['join_error'] = "❌ Lỗi hệ thống: " . $e->getMessage();
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
?>