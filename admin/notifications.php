<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../assets/database/connect.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$items_per_page = 50;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$read_filter = isset($_GET['read']) ? $_GET['read'] : '';

$where_conditions = [];
if (!empty($type_filter)) {
    $where_conditions[] = "type = '" . $conn->real_escape_string($type_filter) . "'";
}
if ($read_filter !== '') {
    $where_conditions[] = "is_read = " . (int)$read_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$count_sql = "SELECT COUNT(*) as total FROM notifications $where_clause";
$result = $conn->query($count_sql);
$total_notifications = $result->fetch_assoc()['total'];
$total_pages = ceil($total_notifications / $items_per_page);

$sql = "SELECT n.*, u.ho_ten, u.username 
        FROM notifications n
        LEFT JOIN users u ON n.user_id = u.id
        $where_clause
        ORDER BY n.created_at DESC
        LIMIT $items_per_page OFFSET $offset";
$notifications = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thông báo - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <?php include 'includes/header.php'; ?>
        
        <div class="admin-content">
            <div class="page-header">
                <div>
                    <h1>Quản lý Thông báo</h1>
                    <p>Tổng cộng: <strong><?= number_format($total_notifications) ?></strong> thông báo</p>
                </div>
            </div>
            
            <div class="filter-bar">
                <form method="GET" class="filter-form">
                    <select name="type" class="filter-select">
                        <option value="">Tất cả loại</option>
                        <option value="club_join" <?= $type_filter === 'club_join' ? 'selected' : '' ?>>Tham gia CLB</option>
                        <option value="event_invite" <?= $type_filter === 'event_invite' ? 'selected' : '' ?>>Mời sự kiện</option>
                        <option value="system" <?= $type_filter === 'system' ? 'selected' : '' ?>>Hệ thống</option>
                    </select>
                    
                    <select name="read" class="filter-select">
                        <option value="">Tất cả</option>
                        <option value="0" <?= $read_filter === '0' ? 'selected' : '' ?>>Chưa đọc</option>
                        <option value="1" <?= $read_filter === '1' ? 'selected' : '' ?>>Đã đọc</option>
                    </select>
                    
                    <button type="submit" class="btn-primary">Lọc</button>
                    <?php if (!empty($type_filter) || $read_filter !== ''): ?>
                    <a href="notifications.php" class="btn-secondary">Xóa bộ lọc</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người nhận</th>
                            <th>Loại</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $notif): ?>
                            <tr>
                                <td><?= $notif['id'] ?></td>
                                <td><?= htmlspecialchars($notif['ho_ten'] ?? $notif['username'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge badge-info"><?= htmlspecialchars($notif['type']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($notif['title']) ?></td>
                                <td><?= htmlspecialchars(mb_substr($notif['message'] ?? '', 0, 50)) ?><?= mb_strlen($notif['message'] ?? '') > 50 ? '...' : '' ?></td>
                                <td>
                                    <span class="badge <?= $notif['is_read'] ? 'badge-success' : 'badge-warning' ?>">
                                        <?= $notif['is_read'] ? 'Đã đọc' : 'Chưa đọc' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                <a href="?page=<?= $current_page - 1 ?><?= !empty($type_filter) ? '&type=' . urlencode($type_filter) : '' ?><?= $read_filter !== '' ? '&read=' . $read_filter : '' ?>" class="page-btn">Trước</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                <a href="?page=<?= $i ?><?= !empty($type_filter) ? '&type=' . urlencode($type_filter) : '' ?><?= $read_filter !== '' ? '&read=' . $read_filter : '' ?>" 
                   class="page-num <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?= $current_page + 1 ?><?= !empty($type_filter) ? '&type=' . urlencode($type_filter) : '' ?><?= $read_filter !== '' ? '&read=' . $read_filter : '' ?>" class="page-btn">Sau</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>

