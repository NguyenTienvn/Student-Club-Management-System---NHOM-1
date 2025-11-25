<?php
session_start();
include __DIR__ . '/assets/database/dbleaderclub.php';

$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;
$stmt = $conn->prepare("SELECT id FROM clubs WHERE chu_nhiem_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$club = $res->fetch_assoc();
$club_id = $club['id'] ?? 0;

if (!$club_id) {
    echo "<div class='modal show' id='createDeptModal'><div class='modal-content'><p>Bạn chưa có CLB để tạo phòng ban.</p><div style='text-align:right;margin-top:12px'><button onclick=\"window.location.href='createCLB.php'\" class='btn-submit'>Tạo CLB</button><button class='btn-cancel' onclick='closeModal()'>Đóng</button></div></div></div>";
    exit;
}

// Lấy lỗi từ session
$error = $_SESSION['popup_error'] ?? '';
unset($_SESSION['popup_error']);
unset($_SESSION['popup_success']);
?>

<link rel="stylesheet" href="assets/css/popup_taopb.css">

<div class="modal" id="createDeptModal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Tạo phòng ban</h2>
      <button class="close-btn" onclick="closeModal()">×</button>
    </div>
    <p class="modal-desc">Quản lý danh sách thông tin thành viên theo từng phòng ban</p>

    <?php if ($error): ?>
        <p class="err-msg" style="display:block; margin-bottom:16px; text-align:center; font-weight:500;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form id="formTaoPB" method="POST" action="process_taopb.php">
      <input type="hidden" name="club_id" value="<?= (int)$club_id ?>">

      <div class="form-group">
        <label>Tên phòng ban <span style="color:#EF4444">*</span></label>
        <input type="text" name="ten_phong_ban" value="<?= htmlspecialchars($_POST['ten_phong_ban'] ?? '') ?>" maxlength="100">
      </div>

      <div class="form-group">
        <label>Chức năng, nhiệm vụ <span style="color:#EF4444">*</span></label>
        <textarea name="chuc_nang_nhiem_vu" rows="4"><?= htmlspecialchars($_POST['chuc_nang_nhiem_vu'] ?? '') ?></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
        <button type="submit" class="btn-submit">Tạo phòng ban</button>
      </div>
    </form>
  </div>
</div>