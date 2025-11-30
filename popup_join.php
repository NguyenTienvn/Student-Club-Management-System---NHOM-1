<?php
session_start();
include __DIR__ . '/assets/database/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT ho_ten, username, email, so_dien_thoai FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Lấy ID CLB từ URL
$club_id = $_GET['club_id'] ?? 0;

// Lấy thông tin CLB
$stmt = $conn->prepare("SELECT ten_clb FROM clubs WHERE id = ?");
$stmt->bind_param("i", $club_id);
$stmt->execute();
$club = $stmt->get_result()->fetch_assoc();

// Kiểm tra xem người dùng đã là thành viên chưa
$stmt = $conn->prepare("SELECT id FROM club_members WHERE club_id = ? AND user_id = ?");
$stmt->bind_param("ii", $club_id, $user_id);
$stmt->execute();
$is_member = $stmt->get_result()->num_rows > 0;

if ($is_member) {
      echo "<div class='modal show' id='joinClubModal'><div class='modal-content'><p>Bạn đã là thành viên của CLB này.</p><div style='text-align:center;margin-top:20px'><button class='btn-submit-full' onclick='closeJoinModal()'>Đóng</button></div></div></div>";
    exit;
}

// Lấy lỗi từ session (nếu có)
$error = $_SESSION['join_error'] ?? '';
unset($_SESSION['join_error']);
?>

<link rel="stylesheet" href="assets/css/club-detail.css">

<div class="modal" id="joinClubModal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Trở thành thành viên Câu Lạc Bộ</h2>
      <p class="modal-subtitle">Hoàn thành thông tin để gửi yêu cầu tham gia</p>
      <button class="close-btn" onclick="closeJoinModal()">×</button>
    </div>

    <!-- Thông tin người đăng ký -->
    <div class="member-info">
      <div class="member-avatar">
        <?= !empty($user['ho_ten']) ? mb_substr($user['ho_ten'], 0, 1, 'UTF-8') : 'U' ?>
      </div>
      <div class="member-details">
        <h4><?= htmlspecialchars($user['ho_ten'] ?? 'Người dùng') ?></h4>
        <p>Thành viên đăng ký</p>
      </div>
    </div>

    <?php if ($error): ?>
        <p class="err-msg" style="display:block; margin-bottom:16px; text-align:center; font-weight:500;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form id="formJoinClub" method="POST" action="process_join.php">
      <input type="hidden" name="club_id" value="<?= (int)$club_id ?>">
      <input type="hidden" name="user_id" value="<?= (int)$user_id ?>">

      <div class="form-group">
        <label>Họ và tên <span class="required">*</span></label>
        <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" readonly style="background: #F9FAFB;">
      </div>

      <div class="form-group">
        <label>Số điện thoại <span class="required">*</span></label>
        <input type="tel" name="so_dien_thoai" value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>" maxlength="15">
      </div>

      <div class="form-group">
        <label>Email <span class="required">*</span></label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly style="background: #F9FAFB;">
      </div>

      <div class="form-group">
        <label>Lời nhắn</label>
        <textarea name="loi_nhan" rows="4" placeholder="Nhập lời nhắn của bạn (tùy chọn)"><?= htmlspecialchars($_POST['loi_nhan'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn-submit-full">Gửi yêu cầu</button>
    </form>
  </div>
</div>