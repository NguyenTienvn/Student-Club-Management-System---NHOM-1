<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . "/assets/database/connect.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng đăng nhập để tham gia sự kiện!'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

if ($event_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID sự kiện không hợp lệ!'
    ]);
    exit;
}

// Kiểm tra sự kiện có tồn tại không
$sql_check = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    echo json_encode([
        'success' => false,
        'message' => 'Sự kiện không tồn tại!'
    ]);
    exit;
}

// Kiểm tra trạng thái sự kiện
if ($event['trang_thai'] === 'da_ket_thuc') {
    echo json_encode([
        'success' => false,
        'message' => 'Sự kiện đã kết thúc!'
    ]);
    exit;
}

// Kiểm tra hạn đăng ký
if (!empty($event['han_dang_ky']) && strtotime($event['han_dang_ky']) < time()) {
    echo json_encode([
        'success' => false,
        'message' => 'Đã hết hạn đăng ký sự kiện!'
    ]);
    exit;
}

// Tạo bảng event_participants nếu chưa có
$create_table = "CREATE TABLE IF NOT EXISTS event_participants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    status VARCHAR(50) DEFAULT 'registered',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_participant (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($create_table);

// Kiểm tra đã đăng ký chưa
$sql_check_registered = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql_check_registered);
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$already_registered = $stmt->get_result()->fetch_assoc();

if ($already_registered) {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn đã đăng ký sự kiện này rồi!'
    ]);
    exit;
}

// Kiểm tra số lượng tối đa
$sql_count = "SELECT COUNT(*) as total FROM event_participants WHERE event_id = ?";
$stmt = $conn->prepare($sql_count);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$count = $stmt->get_result()->fetch_assoc()['total'];

if ($count >= $event['so_luong_toi_da']) {
    echo json_encode([
        'success' => false,
        'message' => 'Sự kiện đã đủ số lượng người tham gia!'
    ]);
    exit;
}

// Đăng ký tham gia
$sql_insert = "INSERT INTO event_participants (event_id, user_id, status) VALUES (?, ?, 'registered')";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("ii", $event_id, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Đăng ký tham gia sự kiện thành công!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
