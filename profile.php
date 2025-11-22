<?php
$page_css = "profile.css";
require 'site.php';
load_top();
load_header();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require('assets/database/connect.php');
$user_id = $_SESSION['user_id'];

$success_message = '';
$error_message = '';

// Xử lý upload avatar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    
    // Kiểm tra lỗi upload
    if ($file['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Kiểm tra định dạng file
        if (in_array($filetype, $allowed)) {
            // Kiểm tra kích thước (max 5MB)
            if ($file['size'] <= 5000000) {
                // Tạo tên file unique
                $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $filetype;
                $upload_path = 'assets/img/avatars/' . $new_filename;
                
                // Tạo thư mục nếu chưa có
                if (!file_exists('assets/img/avatars')) {
                    mkdir('assets/img/avatars', 0777, true);
                }
                
                // Upload file
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Xóa avatar cũ nếu có
                    if (!empty($user['avatar']) && file_exists($user['avatar'])) {
                        unlink($user['avatar']);
                    }
                    
                    // Cập nhật database
                    $sql = "UPDATE users SET avatar = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $upload_path, $user_id);
                    
                    if ($stmt->execute()) {
                        $success_message = "Cập nhật avatar thành công!";
                        $_SESSION['avatar'] = $upload_path;
                    } else {
                        $error_message = "Lỗi cập nhật database!";
                    }
                } else {
                    $error_message = "Lỗi upload file!";
                }
            } else {
                $error_message = "File quá lớn! Tối đa 5MB.";
            }
        } else {
            $error_message = "Chỉ chấp nhận file JPG, JPEG, PNG, GIF!";
        }
    } else {
        $error_message = "Lỗi upload file!";
    }
}

// Lấy thông tin user
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="profile-container">
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <div class="profile-header">
        <div class="avatar-upload-wrapper">
            <?php if (!empty($user['avatar']) && file_exists($user['avatar'])): ?>
                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="profile-avatar-large">
            <?php else: ?>
                <div class="profile-avatar-large">
                    <?php echo strtoupper(substr($user['ho_ten'], 0, 1)); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="avatarForm">
                <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display: none;">
                <button type="button" class="btn-change-avatar" onclick="document.getElementById('avatarInput').click()">
                    📷 Đổi ảnh
                </button>
            </form>
        </div>
        
        <h1><?php echo htmlspecialchars($user['ho_ten']); ?></h1>
        <p class="username">@<?php echo htmlspecialchars($user['username']); ?></p>
    </div>

    <div class="profile-content">
        <div class="info-card">
            <h2>Thông tin cá nhân</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">📧 Email</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">📱 Số điện thoại</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['so_dien_thoai'] ?? 'Chưa cập nhật'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">🎓 Mã sinh viên</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['student_id'] ?? 'Chưa cập nhật'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">🏫 Lớp</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['class'] ?? 'Chưa cập nhật'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">📚 Khoa</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['faculty'] ?? 'Chưa cập nhật'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">👤 Giới tính</span>
                    <span class="info-value"><?php echo ucfirst($user['gender'] ?? 'Khác'); ?></span>
                </div>
            </div>
            <button class="btn-edit" onclick="location.href='edit-profile.php'">Chỉnh sửa hồ sơ</button>
        </div>

        <div class="stats-card">
            <h2>Thống kê hoạt động</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">0</div>
                    <div class="stat-label">CLB đã tham gia</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Sự kiện đã tham gia</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Điểm rèn luyện</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('avatarInput').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        // Preview ảnh trước khi upload
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarElement = document.querySelector('.profile-avatar-large');
            if (avatarElement.tagName === 'IMG') {
                avatarElement.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'profile-avatar-large';
                avatarElement.parentNode.replaceChild(img, avatarElement);
            }
        }
        reader.readAsDataURL(this.files[0]);
        
        // Auto submit form
        document.getElementById('avatarForm').submit();
    }
});
</script>

<?php
load_footer();
?>
