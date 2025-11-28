<?php
session_start();
require('assets/database/connect.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'] ?? 0;
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$student_id = $_POST['student_id'] ?? '';
$note = $_POST['note'] ?? '';

// Validate
if (empty($event_id) || empty($fullname) || empty($email) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit();
}

// Kiểm tra sự kiện tồn tại
$sql = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    echo json_encode(['success' => false, 'message' => 'Sự kiện không tồn tại']);
    exit();
}

// Kiểm tra đã đăng ký chưa
$sql = "SELECT * FROM event_registrations WHERE event_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Bạn đã đăng ký sự kiện này rồi']);
    exit();
}

// Đăng ký
$sql = "INSERT INTO event_registrations (event_id, user_id, ho_ten, email, so_dien_thoai, ma_sinh_vien, ghi_chu, trang_thai) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'cho_duyet')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisssss", $event_id, $user_id, $fullname, $email, $phone, $student_id, $note);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Đăng ký thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $conn->error]);
}
?>