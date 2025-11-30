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
$trang_thai = $_POST['trang_thai'] ?? '';

// Validate
if ($event_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID sự kiện không hợp lệ']);
    exit;
}

$valid_statuses = ['sap_dien_ra', 'dang_dien_ra', 'da_ket_thuc', 'da_huy'];
if (!in_array($trang_thai, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
    exit;
}

// Kiểm tra quyền
$user_id = $_SESSION['user_id'];
$check_sql = "SELECT e.club_id, e.created_by, cm.vai_tro 
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
$can_edit = ($event_info['created_by'] == $user_id) || 
            in_array($event_info['vai_tro'], ['Chủ nhiệm', 'Phó chủ nhiệm']);

if (!$can_edit) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền chỉnh sửa']);
    exit;
}

// Cập nhật trạng thái
$update_sql = "UPDATE events SET trang_thai = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $trang_thai, $event_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật: ' . $conn->error]);
}

$update_stmt->close();
$check_stmt->close();
$conn->close();
?>
