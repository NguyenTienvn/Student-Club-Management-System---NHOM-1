<?php
session_start();
require_once('assets/database/connect.php');

$user_id = isset($_GET['member_id']) ? (int)$_GET['member_id'] : 0;
$club_id = isset($_GET['club_id']) ? (int)$_GET['club_id'] : 0;

// Lấy thông tin thành viên
$stmt = $conn->prepare("
    SELECT cm.id, cm.vai_tro, u.ho_ten, u.username, pb.ten_phong_ban
    FROM club_members cm
    JOIN users u ON cm.user_id = u.id
    LEFT JOIN phong_ban pb ON cm.phong_ban_id = pb.id
    WHERE cm.user_id = ? AND cm.club_id = ?
");
$stmt->bind_param("ii", $user_id, $club_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$member) {
    echo "<script>alert('Không tìm thấy thành viên!'); window.close();</script>";
    exit;
}
?>

<div id="editRoleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Chỉnh sửa chức vụ</h3>
            <button class="close-btn" onclick="closeRoleModal()">&times;</button>
        </div>
        
        <form id="editRoleForm" action="process_edit_role.php" method="POST">
            <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
            <input type="hidden" name="club_id" value="<?= $club_id ?>">
            
            <div class="form-group">
                <label>Thành viên</label>
                <div class="member-display">
                    <strong><?= htmlspecialchars($member['ho_ten']) ?></strong>
                    <span class="text-muted">(<?= htmlspecialchars($member['username']) ?>)</span>
                </div>
            </div>

            <div class="form-group">
                <label>Phòng ban</label>
                <div class="member-display">
                    <?= htmlspecialchars($member['ten_phong_ban'] ?? 'Chưa có phòng ban') ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="vai_tro">Chức vụ <span class="required">*</span></label>
                <select id="vai_tro" name="vai_tro" required>
                    <option value="thanh_vien" <?= $member['vai_tro'] == 'thanh_vien' ? 'selected' : '' ?>>Thành viên</option>
                    <option value="doi_pho" <?= $member['vai_tro'] == 'doi_pho' ? 'selected' : '' ?>>Đội phó</option>
                    <option value="doi_truong" <?= $member['vai_tro'] == 'doi_truong' ? 'selected' : '' ?>>Đội trưởng</option>
                    <option value="truong_ban" <?= $member['vai_tro'] == 'truong_ban' ? 'selected' : '' ?>>Trưởng ban</option>
                    <option value="pho_chu_nhiem" <?= $member['vai_tro'] == 'pho_chu_nhiem' ? 'selected' : '' ?>>Phó chủ nhiệm</option>
                    <option value="chu_nhiem" <?= $member['vai_tro'] == 'chu_nhiem' ? 'selected' : '' ?>>Chủ nhiệm</option>
                </select>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeRoleModal()">Hủy</button>
                <button type="submit" class="btn-submit">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<style>
.member-display {
    padding: 10px;
    background: #f5f5f5;
    border-radius: 6px;
    margin-top: 5px;
}
.text-muted {
    color: #666;
    font-size: 0.9em;
}
</style>

<link rel="stylesheet" href="assets/css/popup_taopb.css">
