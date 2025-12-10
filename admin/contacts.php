<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../assets/database/connect.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$items_per_page = 20;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$where_clause = '';
if (!empty($status_filter)) {
    $where_clause = "WHERE status = '" . $conn->real_escape_string($status_filter) . "'";
}

$count_sql = "SELECT COUNT(*) as total FROM lienhe $where_clause";
$result = $conn->query($count_sql);
$total_contacts = $result->fetch_assoc()['total'];
$total_pages = ceil($total_contacts / $items_per_page);

$sql = "SELECT * FROM lienhe $where_clause ORDER BY created_at DESC LIMIT $items_per_page OFFSET $offset";
$contacts = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Liên hệ - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <?php include 'includes/header.php'; ?>
        
        <div class="admin-content">
            <div class="page-header">
                <div>
                    <h1>Quản lý Liên hệ</h1>
                    <p>Tổng cộng: <strong><?= number_format($total_contacts) ?></strong> liên hệ</p>
                </div>
            </div>
            
            <div class="filter-bar">
                <form method="GET" class="filter-form">
                    <select name="status" class="filter-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="new" <?= $status_filter === 'new' ? 'selected' : '' ?>>Mới</option>
                        <option value="read" <?= $status_filter === 'read' ? 'selected' : '' ?>>Đã đọc</option>
                        <option value="replied" <?= $status_filter === 'replied' ? 'selected' : '' ?>>Đã trả lời</option>
                    </select>
                    <button type="submit" class="btn-primary">Lọc</button>
                    <?php if (!empty($status_filter)): ?>
                    <a href="contacts.php" class="btn-secondary">Xóa bộ lọc</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Chủ đề</th>
                            <th>Trạng thái</th>
                            <th>Ngày gửi</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contacts)): ?>
                            <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?= $contact['id'] ?></td>
                                <td><?= htmlspecialchars($contact['name']) ?></td>
                                <td><?= htmlspecialchars($contact['email']) ?></td>
                                <td><?= htmlspecialchars($contact['subject']) ?></td>
                                <td>
                                    <span class="badge <?= $contact['status'] === 'new' ? 'badge-warning' : ($contact['status'] === 'replied' ? 'badge-success' : 'badge-info') ?>">
                                        <?= $contact['status'] === 'new' ? 'Mới' : ($contact['status'] === 'replied' ? 'Đã trả lời' : 'Đã đọc') ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($contact['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon btn-view" onclick="viewContact(<?= $contact['id'] ?>)" title="Xem chi tiết">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
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
                <a href="?page=<?= $current_page - 1 ?><?= !empty($status_filter) ? '&status=' . urlencode($status_filter) : '' ?>" class="page-btn">Trước</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                <a href="?page=<?= $i ?><?= !empty($status_filter) ? '&status=' . urlencode($status_filter) : '' ?>" 
                   class="page-num <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?= $current_page + 1 ?><?= !empty($status_filter) ? '&status=' . urlencode($status_filter) : '' ?>" class="page-btn">Sau</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
    <script>
    function viewContact(id) {
        alert('Chức năng xem chi tiết đang được phát triển');
    }
    </script>
</body>
</html>

