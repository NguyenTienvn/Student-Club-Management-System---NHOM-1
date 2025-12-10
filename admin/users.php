<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../assets/database/connect.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Phân trang
$items_per_page = 20;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Tìm kiếm và filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(ho_ten LIKE ? OR username LIKE ? OR email LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

if (!empty($role_filter)) {
    $where_conditions[] = "vai_tro = ?";
    $params[] = $role_filter;
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Đếm tổng số users
$count_sql = "SELECT COUNT(*) as total FROM users $where_clause";
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $total_users = $count_stmt->get_result()->fetch_assoc()['total'];
    $count_stmt->close();
} else {
    $result = $conn->query($count_sql);
    $total_users = $result->fetch_assoc()['total'];
}
$total_pages = ceil($total_users / $items_per_page);

// Lấy danh sách users
$sql = "SELECT id, ho_ten, username, email, vai_tro, created_at, avatar FROM users $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $items_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $items_per_page, $offset);
}
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <?php include 'includes/header.php'; ?>
        
        <div class="admin-content">
            <div class="page-header">
                <div>
                    <h1>Quản lý Người dùng</h1>
                    <p>Tổng cộng: <strong><?= number_format($total_users) ?></strong> người dùng</p>
                </div>
            </div>
            
            <!-- Search and Filter -->
            <div class="filter-bar">
                <form method="GET" class="filter-form">
                    <div class="search-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text" name="search" placeholder="Tìm kiếm theo tên, username hoặc email..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    
                    <select name="role" class="filter-select">
                        <option value="">Tất cả vai trò</option>
                        <option value="ADMIN" <?= $role_filter === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                        <option value="THANH_VIEN" <?= $role_filter === 'THANH_VIEN' ? 'selected' : '' ?>>Thành viên</option>
                    </select>
                    
                    <button type="submit" class="btn-primary">Tìm kiếm</button>
                    
                    <?php if (!empty($search) || !empty($role_filter)): ?>
                    <a href="users.php" class="btn-secondary">Xóa bộ lọc</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Users Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <div class="user-cell">
                                        <img src="<?= !empty($user['avatar']) ? '../' . htmlspecialchars($user['avatar']) : '../assets/img/avatars/user.svg' ?>" 
                                             alt="<?= htmlspecialchars($user['ho_ten'] ?? 'User') ?>" 
                                             class="user-avatar-small">
                                        <span><?= htmlspecialchars($user['ho_ten'] ?? 'Chưa cập nhật') ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email'] ?? 'Chưa có') ?></td>
                                <td>
                                    <span class="badge <?= $user['vai_tro'] === 'ADMIN' ? 'badge-danger' : 'badge-primary' ?>">
                                        <?= $user['vai_tro'] === 'ADMIN' ? 'Admin' : 'Thành viên' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon btn-edit" onclick="editUser(<?= $user['id'] ?>)" title="Chỉnh sửa">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>
                                        <button class="btn-icon btn-delete" onclick="deleteUser(<?= $user['id'] ?>)" title="Xóa">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
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
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                <a href="?page=<?= $current_page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($role_filter) ? '&role=' . urlencode($role_filter) : '' ?>" class="page-btn">Trước</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                <a href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($role_filter) ? '&role=' . urlencode($role_filter) : '' ?>" 
                   class="page-num <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?= $current_page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($role_filter) ? '&role=' . urlencode($role_filter) : '' ?>" class="page-btn">Sau</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
    <script>
    function editUser(id) {
        // TODO: Implement edit user modal
        alert('Chức năng chỉnh sửa đang được phát triển');
    }
    
    function deleteUser(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa người dùng này?')) return;
        // TODO: Implement delete user API
        alert('Chức năng xóa đang được phát triển');
    }
    </script>
</body>
</html>

