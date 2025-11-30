<?php
session_start();
require_once __DIR__ . "/assets/database/connect.php";

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập']);
    exit;
}

// Kiểm tra POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$event_id = (int)($_POST['event_id'] ?? 0);

if ($event_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID sự kiện không hợp lệ']);
    exit;
}

// Kiểm tra quyền
$user_id = $_SESSION['user_id'];
$check_sql = "SELECT e.club_id, e.created_by, e.anh_bia, cm.vai_tro 
              FROM events e
              LEFT JOIN club_members cm ON e.club_id = cm.club_id AND cm.user_id = ?
              WHERE e.id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $event_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Sự kiện không tồn tại']);
    exit;
}

$event_info = $result->fetch_assoc();
$can_delete = ($event_info['created_by'] == $user_id) || 
              in_array($event_info['vai_tro'], ['Chủ nhiệm', 'Phó chủ nhiệm']);

if (!$can_delete) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xóa sự kiện này']);
    exit;
}

// Xóa ảnh bìa nếu có
if (!empty($event_info['anh_bia'])) {
    $image_path = __DIR__ . '/' . $event_info['anh_bia'];
    if (file_exists($image_path)) {
        @unlink($image_path);
    }
}

// Xóa các bản ghi liên quan trước (nếu có)
$delete_participants = "DELETE FROM event_participants WHERE event_id = ?";
$stmt_participants = $conn->prepare($delete_participants);
$stmt_participants->bind_param("i", $event_id);
$stmt_participants->execute();
$stmt_participants->close();

// Xóa sự kiện
$delete_sql = "DELETE FROM events WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $event_id);

if ($delete_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Xóa sự kiện thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi xóa: ' . $conn->error]);
}

$delete_stmt->close();
$check_stmt->close();
$conn->close();
?>
