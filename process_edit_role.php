<?php
session_start();
require_once('assets/database/connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'] ?? $_SESSION['id'];
$member_id = isset($_POST['member_id']) ? (int)$_POST['member_id'] : 0;
$club_id = isset($_POST['club_id']) ? (int)$_POST['club_id'] : 0;
$vai_tro = isset($_POST['vai_tro']) ? trim($_POST['vai_tro']) : '';

// Kiểm tra dữ liệu
if ($member_id <= 0 || $club_id <= 0 || $vai_tro === '') {
    echo "<script>alert('Dữ liệu không hợp lệ!'); window.history.back();</script>";
    exit;
}

// Kiểm tra quyền (chỉ chủ nhiệm mới được sửa)
$check_owner = $conn->prepare("SELECT chu_nhiem_id FROM clubs WHERE id = ?");
$check_owner->bind_param("i", $club_id);
$check_owner->execute();
$owner = $check_owner->get_result()->fetch_assoc();
$check_owner->close();

if (!$owner || $owner['chu_nhiem_id'] != $user_id) {
    echo "<script>alert('Bạn không có quyền thực hiện thao tác này!'); window.history.back();</script>";
    exit;
}

// Cập nhật chức vụ
$stmt = $conn->prepare("UPDATE club_members SET vai_tro = ? WHERE id = ? AND club_id = ?");
$stmt->bind_param("sii", $vai_tro, $member_id, $club_id);
$result = $stmt->execute();
$stmt->close();

if ($result) {
    echo "<script>alert('Cập nhật chức vụ thành công!'); window.location.href='taopb.php?id=$club_id';</script>";
} else {
    echo "<script>alert('Có lỗi xảy ra! Vui lòng thử lại.'); window.history.back();</script>";
}
?>
