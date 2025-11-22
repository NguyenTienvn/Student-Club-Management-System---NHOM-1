<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Đơn giản hóa: kiểm tra mật khẩu hiện tại
        if ($current_password !== 'admin123') {
            $error = "Mật khẩu hiện tại không đúng!";
        } elseif ($new_password !== $confirm_password) {
            $error = "Mật khẩu xác nhận không khớp!";
        } elseif (strlen($new_password) < 6) {
            $error = "Mật khẩu mới phải có ít nhất 6 ký tự!";
        } else {
            // TODO: Lưu mật khẩu mới vào database
            $success = "Đổi mật khẩu thành công! (Demo mode - chưa lưu thực tế)";
        }
    }
}

$page_title = "Cài đặt";
include 'includes/header.php';
?>

<div class="settings-container">
    <div class="settings-grid">
        <!-- Change Password -->
        <div class="settings-card">
            <h3>🔐 Đổi mật khẩu</h3>
            
            <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="settings-form">
                <input type="hidden" name="action" value="change_password">
                
                <div class="form-group">
                    <label>Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label>Mật khẩu mới</label>
                    <input type="password" name="new_password" required>
                </div>
                
                <div class="form-group">
                    <label>Xác nhận mật khẩu mới</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn-primary">Đổi mật khẩu</button>
            </form>
        </div>

        <!-- System Info -->
        <div class="settings-card">
            <h3>ℹ️ Thông tin hệ thống</h3>
            <div class="info-list">
                <div class="info-item">
                    <span class="info-label">Phiên bản:</span>
                    <span class="info-value">1.0.0</span>
                </div>
                <div class="info-item">
                    <span class="info-label">PHP Version:</span>
                    <span class="info-value"><?php echo phpversion(); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Server:</span>
                    <span class="info-value"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Database:</span>
                    <span class="info-value">MySQL</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="settings-card">
            <h3>⚡ Thao tác nhanh</h3>
            <div class="actions-list">
                <a href="../trangchu.php" target="_blank" class="action-btn">
                    <span>🏠</span>
                    <span>Xem trang chủ</span>
                </a>
                <a href="export-data.php" class="action-btn">
                    <span>📥</span>
                    <span>Export dữ liệu</span>
                </a>
                <a href="backup.php" class="action-btn">
                    <span>💾</span>
                    <span>Sao lưu database</span>
                </a>
                <a href="clear-cache.php" class="action-btn">
                    <span>🗑️</span>
                    <span>Xóa cache</span>
                </a>
            </div>
        </div>

        <!-- Admin Info -->
        <div class="settings-card">
            <h3>👤 Thông tin admin</h3>
            <div class="admin-profile">
                <div class="admin-avatar">A</div>
                <div class="admin-details">
                    <h4><?php echo $_SESSION['admin_username']; ?></h4>
                    <p>Quản trị viên hệ thống</p>
                    <span class="admin-badge">Super Admin</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
}

.settings-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.settings-card h3 {
    font-size: 20px;
    font-weight: 800;
    color: #2d3748;
    margin-bottom: 25px;
}

.settings-form .form-group {
    margin-bottom: 20px;
}

.settings-form label {
    display: block;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
    font-size: 14px;
}

.settings-form input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.settings-form input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.btn-primary {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 12px;
    background: #f7fafc;
    border-radius: 10px;
}

.info-label {
    font-weight: 600;
    color: #718096;
}

.info-value {
    font-weight: 700;
    color: #2d3748;
}

.actions-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px;
    background: #f7fafc;
    border-radius: 15px;
    text-decoration: none;
    color: #2d3748;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: #edf2f7;
    transform: translateY(-3px);
}

.action-btn span:first-child {
    font-size: 32px;
}

.admin-profile {
    display: flex;
    align-items: center;
    gap: 20px;
}

.admin-avatar {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    font-weight: 900;
}

.admin-details h4 {
    font-size: 20px;
    font-weight: 800;
    color: #2d3748;
    margin-bottom: 5px;
}

.admin-details p {
    color: #718096;
    font-size: 14px;
    margin-bottom: 10px;
}

.admin-badge {
    display: inline-block;
    padding: 6px 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: 600;
}

.alert-error {
    background: #fed7d7;
    color: #c53030;
    border: 2px solid #fc8181;
}

.alert-success {
    background: #c6f6d5;
    color: #22543d;
    border: 2px solid #9ae6b4;
}

@media (max-width: 1024px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-list {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
