<?php
session_start();
require_once('assets/database/connect.php');

$user_id = $_POST['user_id'];
$leader_id = $_SESSION['user_id'] ?? null;

if (!$leader_id) {
    echo "Lỗi: chưa đăng nhập.";
    exit;
}

// Lấy CLB mà leader quản lý
$stmt = $conn->prepare("SELECT id FROM clubs WHERE chu_nhiem_id = ?");
$stmt->bind_param("i", $leader_id);
$stmt->execute();
$club = $stmt->get_result()->fetch_assoc();
$club_id = $club['id'] ?? null;

if (!$club_id) {
    echo "Không tìm thấy CLB của bạn.";
    exit;
}

// Lấy thông tin người được mời
$stmt2 = $conn->prepare("SELECT ho_ten, so_dien_thoai, email FROM users WHERE id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$u = $stmt2->get_result()->fetch_assoc();

if (!$u) {
    echo "Người dùng không tồn tại.";
    exit;
}

// Lưu lời mời vào join_requests
$stmt3 = $conn->prepare("
    INSERT INTO join_requests (club_id, user_id, ho_ten, so_dien_thoai, email, trang_thai, requested_at)
    VALUES (?, ?, ?, ?, ?, 'cho_duyet', NOW())
");
$stmt3->bind_param("iisss", $club_id, $user_id, $u['ho_ten'], $u['so_dien_thoai'], $u['email']);

if ($stmt3->execute()) {
    echo "Đã gửi lời mời thành công!";
} else {
    echo "Lỗi khi gửi lời mời.";
}
?>
