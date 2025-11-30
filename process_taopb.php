<?php
session_start();
include __DIR__ . '/assets/database/dbleaderclub.php'; // Kết nối database mysqli

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Nhận dữ liệu từ form và lọc input
$club_id = isset($_POST['club_id']) ? (int)$_POST['club_id'] : 0;
$ten_pb = isset($_POST['ten_phong_ban']) ? trim($_POST['ten_phong_ban']) : '';
$chuc_nang = isset($_POST['chuc_nang_nhiem_vu']) ? trim($_POST['chuc_nang_nhiem_vu']) : '';

// Kiểm tra dữ liệu rỗng
if ($club_id <= 0 || $ten_pb === '' || $chuc_nang === '') {
    $_SESSION['popup_error'] = "Vui lòng điền đầy đủ thông tin!";
    header("Location: taopb.php?openPopup=1");
    exit;
}


// Kiểm tra trùng tên phòng ban theo CLB
$check = $conn->prepare("SELECT id FROM phong_ban WHERE club_id = ? AND ten_phong_ban = ?");
$check->bind_param("is", $club_id, $ten_pb);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<script>alert('Tên phòng ban này đã tồn tại trong CLB!'); window.history.back();</script>";
    exit;
}
$check->close();

// Thêm phòng ban
$stmt = $conn->prepare("INSERT INTO phong_ban (club_id, ten_phong_ban, chuc_nang_nhiem_vu) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $club_id, $ten_pb, $chuc_nang);
$result = $stmt->execute();
$pb_id = $conn->insert_id; 
$stmt->close();

// Thông báo và chuyển trang
if ($result) {
    echo "<script>alert('Tạo phòng ban thành công!'); window.location.href='manage_department.php?pb_id=$pb_id';</script>";
} else {
    echo "<script>alert('Có lỗi xảy ra! Vui lòng thử lại.'); window.history.back();</script>";
}
?>            