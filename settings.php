<?php
$page_css = "settings.css";
require 'site.php';
load_top();
load_header();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="settings-container">
    <div class="page-header">
        <h1>Cài đặt</h1>
        <p>Quản lý tài khoản và tùy chỉnh cá nhân</p>
    </div>

    <div class="settings-content">
        <div class="settings-card">
            <h2>🔐 Bảo mật</h2>
            <div class="setting-item">
                <div class="setting-info">
                    <h3>Đổi mật khẩu</h3>
                    <p>Cập nhật mật khẩu để bảo mật tài khoản</p>
                </div>
                <button class="btn-action" onclick="location.href='change-password.php'">Thay đổi</button>
            </div>
        </div>

        <div class="settings-card">
            <h2>🔔 Thông báo</h2>
            <div class="setting-item">
                <div class="setting-info">
                    <h3>Thông báo email</h3>
                    <p>Nhận thông báo về sự kiện và hoạt động CLB</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="emailNotification" checked>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="setting-item">
                <div class="setting-info">
                    <h3>Thông báo sự kiện</h3>
                    <p>Nhận nhắc nhở về sự kiện sắp diễn ra</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="eventNotification" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="settings-card">
            <h2>🎨 Giao diện</h2>
            <div class="setting-item">
                <div class="setting-info">
                    <h3>Chế độ tối</h3>
                    <p>Chuyển sang giao diện tối để bảo vệ mắt</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="darkModeToggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="settings-card danger">
            <h2>⚠️ Vùng nguy hiểm</h2>
            <div class="setting-item">
                <div class="setting-info">
                    <h3>Xóa tài khoản</h3>
                    <p>Xóa vĩnh viễn tài khoản và tất cả dữ liệu</p>
                </div>
                <button class="btn-danger">Xóa tài khoản</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const emailNotification = document.getElementById('emailNotification');
    const eventNotification = document.getElementById('eventNotification');
    
    // Load settings từ localStorage
    try {
        // Dark mode
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
            darkModeToggle.checked = true;
        }
        
        // Email notification
        const emailNotif = localStorage.getItem('emailNotification');
        if (emailNotif !== null) {
            emailNotification.checked = emailNotif === 'true';
        }
        
        // Event notification
        const eventNotif = localStorage.getItem('eventNotification');
        if (eventNotif !== null) {
            eventNotification.checked = eventNotif === 'true';
        }
    } catch (e) {
        console.error('localStorage error:', e);
    }
    
    // Dark mode toggle
    darkModeToggle.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-mode');
            try {
                localStorage.setItem('darkMode', 'true');
            } catch (e) {}
        } else {
            document.body.classList.remove('dark-mode');
            try {
                localStorage.setItem('darkMode', 'false');
            } catch (e) {}
        }
    });
    
    // Email notification toggle
    emailNotification.addEventListener('change', function() {
        try {
            localStorage.setItem('emailNotification', this.checked);
            showToast(this.checked ? 'Đã bật thông báo email' : 'Đã tắt thông báo email');
        } catch (e) {}
    });
    
    // Event notification toggle
    eventNotification.addEventListener('change', function() {
        try {
            localStorage.setItem('eventNotification', this.checked);
            showToast(this.checked ? 'Đã bật thông báo sự kiện' : 'Đã tắt thông báo sự kiện');
        } catch (e) {}
    });
    
    // Toast notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }
});
</script>

<?php
load_footer();
?>
