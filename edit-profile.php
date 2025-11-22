<?php
$page_css = "edit-profile.css";
require 'site.php';
load_top();
load_header();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require('assets/database/connect.php');
$user_id = $_SESSION['user_id'];

$success_message = '';
$error_message = '';

// Lấy thông tin user hiện tại
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $class = trim($_POST['class'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $gender = $_POST['gender'] ?? 'khac';
    
    // Validate
    if (empty($ho_ten)) {
        $error_message = "Vui lòng nhập họ tên!";
    } elseif (empty($email)) {
        $error_message = "Vui lòng nhập email!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Email không hợp lệ!";
    } else {
        // Kiểm tra email trùng (trừ chính user này)
        $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error_message = "Email này đã được sử dụng!";
        } else {
            // Update thông tin
            $sql = "UPDATE users SET ho_ten = ?, email = ?, so_dien_thoai = ?, student_id = ?, class = ?, faculty = ?, gender = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $ho_ten, $email, $so_dien_thoai, $student_id, $class, $faculty, $gender, $user_id);
            
            if ($stmt->execute()) {
                // Update session
                $_SESSION['ho_ten'] = $ho_ten;
                $_SESSION['email'] = $email;
                $_SESSION['so_dien_thoai'] = $so_dien_thoai;
                
                $success_message = "success";
                // Reload user data
                $user['ho_ten'] = $ho_ten;
                $user['email'] = $email;
                $user['so_dien_thoai'] = $so_dien_thoai;
                $user['student_id'] = $student_id;
                $user['class'] = $class;
                $user['faculty'] = $faculty;
                $user['gender'] = $gender;
            } else {
                $error_message = "Lỗi cập nhật thông tin!";
            }
        }
    }
}
?>

<!-- Success Modal -->
<div id="successModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="success-icon">✓</div>
        <h2>Cập nhật thành công!</h2>
        <p>Thông tin của bạn đã được cập nhật</p>
        <div class="redirect-text">Đang chuyển về trang hồ sơ...</div>
    </div>
</div>

<div class="edit-profile-container">
    <div class="page-header">
        <h1>Chỉnh sửa hồ sơ</h1>
        <p>Cập nhật thông tin cá nhân của bạn</p>
    </div>

    <div class="form-card">
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                ❌ <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="ho_ten">Họ và tên *</label>
                    <input type="text" id="ho_ten" name="ho_ten" 
                           value="<?php echo htmlspecialchars($user['ho_ten'] ?? ''); ?>" 
                           placeholder="Nhập họ và tên" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                           placeholder="Nhập email" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="so_dien_thoai">Số điện thoại</label>
                    <input type="tel" id="so_dien_thoai" name="so_dien_thoai" 
                           value="<?php echo htmlspecialchars($user['so_dien_thoai'] ?? ''); ?>" 
                           placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label for="student_id">Mã sinh viên</label>
                    <input type="text" id="student_id" name="student_id" 
                           value="<?php echo htmlspecialchars($user['student_id'] ?? ''); ?>" 
                           placeholder="Nhập mã sinh viên">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="class">Lớp</label>
                    <input type="text" id="class" name="class" 
                           value="<?php echo htmlspecialchars($user['class'] ?? ''); ?>" 
                           placeholder="Nhập lớp">
                </div>

                <div class="form-group">
                    <label for="faculty">Khoa</label>
                    <input type="text" id="faculty" name="faculty" 
                           value="<?php echo htmlspecialchars($user['faculty'] ?? ''); ?>" 
                           placeholder="Nhập khoa">
                </div>
            </div>

            <div class="form-group">
                <label>Giới tính</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="gender" value="nam" 
                               <?php echo ($user['gender'] ?? '') === 'nam' ? 'checked' : ''; ?>>
                        <span>Nam</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="gender" value="nu" 
                               <?php echo ($user['gender'] ?? '') === 'nu' ? 'checked' : ''; ?>>
                        <span>Nữ</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="gender" value="khac" 
                               <?php echo ($user['gender'] ?? 'khac') === 'khac' ? 'checked' : ''; ?>>
                        <span>Khác</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="history.back()">Hủy</button>
                <button type="submit" class="btn-submit">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
const isSuccess = <?php echo ($success_message === 'success') ? 'true' : 'false'; ?>;

if (isSuccess) {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.style.display = 'flex';
        setTimeout(() => {
            window.location.href = 'profile.php';
        }, 2000);
    }
}
</script>

<?php
load_footer();
?>
