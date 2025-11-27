<?php
error_reporting(0);
header("Content-Type: application/json; charset=UTF-8");
ob_clean();
session_start();

require_once(__DIR__ . "/../assets/database/connect.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']);
    exit;
}

$club_id = (int)($_POST['club_id'] ?? 0);
$user_id = (int)($_POST['user_id'] ?? 0);

if ($club_id < 1 || $user_id < 1) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
    exit;
}

// Kiểm tra người dùng tồn tại
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
    exit;
}

// Kiểm tra đã là thành viên chưa
$stmt = $conn->prepare("SELECT 1 FROM club_members WHERE club_id = ? AND user_id = ?");
$stmt->bind_param("ii", $club_id, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Người này đã trong CLB']);
    exit;
}

// Thêm thành viên (chờ duyệt)
$stmt = $conn->prepare("
    INSERT INTO club_members (club_id, user_id, vai_tro, joined_at, trang_thai) 
    VALUES (?, ?, 'thanh_vien', NOW(), 'cho_duyet')
");
$stmt->bind_param("ii", $club_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Đã gửi yêu cầu tham gia thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại']);
}

exit;
?>
