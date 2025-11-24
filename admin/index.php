<?php
session_start();

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require '../assets/database/connect.php';

// Lấy thống kê
$stats = [];

// Tổng số người dùng
$result = $conn->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $result->fetch_assoc()['total'];

// Tổng số CLB
$result = $conn->query("SELECT COUNT(*) as total FROM clubs");
$stats['clubs'] = $result->fetch_assoc()['total'];

// Tin nhắn liên hệ mới
$result = $conn->query("SELECT COUNT(*) as total FROM lienhe WHERE status = 'new'");
$stats['messages'] = $result->fetch_assoc()['total'];

// Hoạt động gần đây
$recent_users = $conn->query("SELECT username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$recent_contacts = $conn->query("SELECT name, email, subject, created_at FROM lienhe ORDER BY created_at DESC LIMIT 5");

$page_title = "Dashboard";
include 'includes/header.php';
?>

<div class="dashboard-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card users-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo $stats['users']; ?></h3>
                <p>Người dùng</p>
            </div>
            <a href="users.php" class="stat-link">Xem chi tiết →</a>
        </div>

        <div class="stat-card clubs-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-info">
                <h3><?php echo $stats['clubs']; ?></h3>
                <p>Câu lạc bộ</p>
            </div>
            <a href="clubs.php" class="stat-link">Xem chi tiết →</a>
        </div>

        <div class="stat-card messages-card">
            <div class="stat-icon">✉️</div>
            <div class="stat-info">
                <h3><?php echo $stats['messages']; ?></h3>
                <p>Tin nhắn mới</p>
            </div>
            <a href="contacts.php" class="stat-link">Xem chi tiết →</a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="activity-grid">
        <!-- Recent Users -->
        <div class="activity-card">
            <div class="card-header">
                <h2>Người dùng mới</h2>
                <a href="users.php" class="view-all">Xem tất cả</a>
            </div>
            <div class="activity-list">
                <?php while ($user = $recent_users->fetch_assoc()): ?>
                <div class="activity-item">
                    <div class="activity-avatar"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
                    <div class="activity-info">
                        <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="activity-time"><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Recent Contacts -->
        <div class="activity-card">
            <div class="card-header">
                <h2>Tin nhắn liên hệ</h2>
                <a href="contacts.php" class="view-all">Xem tất cả</a>
            </div>
            <div class="activity-list">
                <?php while ($contact = $recent_contacts->fetch_assoc()): ?>
                <div class="activity-item">
                    <div class="activity-avatar">✉️</div>
                    <div class="activity-info">
                        <h4><?php echo htmlspecialchars($contact['name']); ?></h4>
                        <p><?php echo htmlspecialchars($contact['subject']); ?></p>
                        <span class="activity-time"><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>