<?php
session_start();
require_once('assets/database/connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "Lỗi: chưa đăng nhập.";
    exit;
}

$user_id = $_POST['user_id'] ?? null;
$pb_id   = $_POST['pb_id'] ?? null;

if (!$user_id || !$pb_id) {
    echo "Thiếu dữ liệu!";
    exit;
}

/* ============================================
   1) KIỂM TRA NGƯỜI DÙNG CÓ TỒN TẠI KHÔNG
   ============================================ */
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$exists = $stmt->get_result()->fetch_assoc();

if (!$exists) {
    echo "Người dùng không tồn tại.";
    exit;
}

/* ============================================
   2) LẤY club_id TỪ PHÒNG BAN
   ============================================ */
$stmt = $conn->prepare("SELECT club_id FROM phong_ban WHERE id = ?");
$stmt->bind_param("i", $pb_id);
$stmt->execute();
$pbInfo = $stmt->get_result()->fetch_assoc();

if (!$pbInfo) {
    echo "Phòng ban không tồn tại.";
    exit;
}

$club_id = $pbInfo['club_id'];

/* ============================================
   3) KIỂM TRA USER ĐÃ Ở TRONG PHÒNG BAN CHƯA
   ============================================ */
$check = $conn->prepare("
    SELECT id FROM club_members 
    WHERE user_id = ? 
      AND phong_ban_id = ?
");
$check->bind_param("ii", $user_id, $pb_id);
$check->execute();
$dup = $check->get_result()->fetch_assoc();

if ($dup) {
    echo "Thành viên này đã ở trong ban.";
    exit;
}

/* ============================================
   4) THÊM USER VÀO club_members
   ============================================ */
$checkClub = $conn->prepare("
    SELECT id FROM club_members
    WHERE user_id = ? AND club_id = ?
");
$checkClub->bind_param("ii", $user_id, $club_id);
$checkClub->execute();
$clubRow = $checkClub->get_result()->fetch_assoc();

/* 3A - Nếu đã tồn tại → UPDATE phòng ban */
if ($clubRow) {
    $up = $conn->prepare("
        UPDATE club_members 
        SET phong_ban_id = ?, trang_thai = 'dang_hoat_dong'
        WHERE id = ?
    ");
    $up->bind_param("ii", $pb_id, $clubRow['id']);
    $up->execute();

    echo "Đã chuyển thành viên vào phòng ban!";
    exit;
}

/* 4 - Nếu chưa có → INSERT */
$add = $conn->prepare("
    INSERT INTO club_members (club_id, user_id, phong_ban_id, trang_thai)
    VALUES (?, ?, ?, 'dang_hoat_dong')
");
$add->bind_param("iii", $club_id, $user_id, $pb_id);

if ($add->execute()) {
    echo "Đã thêm thành viên!";
} else {
    echo "SQL Error: " . $conn->error;
}
?>
