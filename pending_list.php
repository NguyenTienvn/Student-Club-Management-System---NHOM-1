<?php
session_start();
require_once('assets/database/connect.php');

$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if (!$user_id) {
    echo "<p>Lỗi: Chưa đăng nhập.</p>";
    exit;
}

// Lấy CLB mà user là chủ nhiệm
$stmt = $conn->prepare("SELECT id FROM clubs WHERE chu_nhiem_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$club = $stmt->get_result()->fetch_assoc();
$club_id = $club['id'] ?? null;

if (!$club_id) {
    echo "<p>Lỗi: Chưa xác định CLB.</p>";
    exit;
}

// Lấy danh sách yêu cầu tham gia
$stmt2 = $conn->prepare("
    SELECT ho_ten, so_dien_thoai, email, loi_nhan, requested_at 
    FROM join_requests 
    WHERE club_id = ? AND trang_thai = 'cho_duyet'
    ORDER BY requested_at DESC
");
$stmt2->bind_param("i", $club_id);
$stmt2->execute();
$rs = $stmt2->get_result();

if ($rs->num_rows === 0) {
    echo "<p>Không có yêu cầu nào.</p>";
    exit;
}

while ($row = $rs->fetch_assoc()) {
    echo "
    <div style='padding:12px;border-bottom:1px solid #eee; text-align:left'>
        <b>".htmlspecialchars($row['ho_ten'])."</b><br>
        📞 ".htmlspecialchars($row['so_dien_thoai'])."<br>
        ✉️ ".htmlspecialchars($row['email'])."<br>
        <i>Lời nhắn:</i> ".htmlspecialchars($row['loi_nhan'] ?: '—')."<br>
        <small>Gửi lúc: ".$row['requested_at']."</small>
    </div>";
}
?>