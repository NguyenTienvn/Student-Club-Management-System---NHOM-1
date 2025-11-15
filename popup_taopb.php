<?php
session_start();
$success = $_SESSION['success'] ?? false;
$error = $_SESSION['error'] ?? '';
$department_name = $_SESSION['department_name'] ?? '';

// Xử lý form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_name = trim($_POST['department_name'] ?? '');
    $duties = trim($_POST['duties'] ?? '');

    if (empty($department_name)) {
        $_SESSION['error'] = 'Vui lòng nhập tên phòng ban!';
    } elseif (strlen($department_name) > 50) {
        $_SESSION['error'] = 'Tên phòng ban không quá 50 ký tự!';
    } elseif (empty($duties)) {
        $_SESSION['error'] = 'Vui lòng nhập chức năng, nhiệm vụ!';
    } else {
        $_SESSION['success'] = true;
        $_SESSION['department_name'] = htmlspecialchars($department_name);
        // TODO: Lưu DB
    }
    // Không redirect → để JS xử lý
}

// Xóa session sau khi dùng
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['department_name']);
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
            <div class="alert success">
                Tạo phòng ban "<strong><?= $department_name ?></strong>" thành công!
            </div>
        <?php endif; ?>

        <form method="POST" id="createDeptForm">
            <div class="input-group">
                <label>Tên phòng ban, bộ phận <span class="required">*</span></label>
                <input type="text" name="department_name" placeholder="Ban sự kiện" maxlength="50" required value="<?= htmlspecialchars($department_name) ?>">
            </div>

            <div class="input-group">
                <label>Chức năng, nhiệm vụ <span class="required">*</span></label>
                <textarea name="duties" rows="3" placeholder="- Tổ chức sự kiện - Trang trí - Lên kế hoạch chương trình - Dẫn chương trình" required></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn-create">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5V19M5 12H19" stroke="white" stroke-width="2"/>
                    </svg>
                    Tạo phòng ban
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tự động reload modal sau khi submit (vì form POST)
    document.getElementById('createDeptForm')?.addEventListener('submit', function() {
        setTimeout(() => {
            fetch('create-department-modal.php')
                .then(r => r.text())
                .then(html => {
                    document.getElementById('modalContainer').innerHTML = html;
                    document.getElementById('createDeptModal').classList.add('show');
                });
        }, 100);
    });
</script>