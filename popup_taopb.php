<?php
require_once 'db.php';
session_start();

$club_id = $_GET['club_id'] ?? null;
$error = $success = $name = '';

if (!$club_id || !isset($_SESSION['user_id'])) die('Lỗi quyền truy cập');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['department_name'] ?? '');
    $duties = trim($_POST['duties'] ?? '');

    if (empty($name)) $error = 'Vui lòng nhập tên phòng ban!';
    elseif (strlen($name) > 50) $error = 'Tên không quá 50 ký tự!';
    elseif (empty($duties)) $error = 'Vui lòng nhập chức năng, nhiệm vụ!';
    else {
        $stmt = $pdo->prepare("INSERT INTO phong_ban (club_id, ten_phong_ban, chuc_nang_nhiem_vu) VALUES (?, ?, ?)");
        $stmt->execute([$club_id, $name, $duties]);
        $success = true;
        $name = htmlspecialchars($name);
    }
}
?>

<div class="modal" id="createDeptModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Tạo phòng ban</h2>
            <button type="button" class="close-btn" onclick="closeModal()">×</button>
        </div>
        <p class="modal-desc">Quản lý danh sách thông tin thành viên theo từng phòng ban</p>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success">Tạo phòng ban "<strong><?= $name ?></strong>" thành công!</div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Tên phòng ban, bộ phận <span class="required">*</span></label>
                <input type="text" name="department_name" placeholder="Ban sự kiện" maxlength="50" required value="<?= htmlspecialchars($name) ?>">
            </div>
            <div class="form-group">
                <label>Chức năng, nhiệm vụ <span class="required">*</span></label>
                <textarea name="duties" rows="4" placeholder="- Tổ chức sự kiện - Trang trí..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn-submit">Tạo phòng ban</button>
            </div>
        </form>
    </div>
</div>