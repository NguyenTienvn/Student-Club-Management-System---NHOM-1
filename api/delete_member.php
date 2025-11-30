<?php
session_start();
require_once __DIR__ . '/../assets/database/connect.php';

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

if (!isset($_POST['club_member_id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID thành viên']);
    exit;
}

$club_member_id = intval($_POST['club_member_id']);

$sql = "DELETE FROM club_members WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $club_member_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Xóa thành viên thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy thành viên']);
}
