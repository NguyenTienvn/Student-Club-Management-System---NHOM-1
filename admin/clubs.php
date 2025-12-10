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

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';
$params = [];
$types = '';

if (!empty($search)) {
    $where_clause = "WHERE c.ten_clb LIKE ? OR c.linh_vuc LIKE ?";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $types = 'ss';
}

$count_sql = "SELECT COUNT(*) as total FROM clubs c $where_clause";
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $total_clubs = $count_stmt->get_result()->fetch_assoc()['total'];
    $count_stmt->close();
} else {
    $result = $conn->query($count_sql);
    $total_clubs = $result->fetch_assoc()['total'];
}
$total_pages = ceil($total_clubs / $items_per_page);

$sql = "SELECT c.id, c.ten_clb, c.linh_vuc, c.so_thanh_vien, c.created_at, u.ho_ten as doi_truong
        FROM clubs c
        LEFT JOIN users u ON c.chu_nhiem_id = u.id
        $where_clause
        ORDER BY c.created_at DESC
        LIMIT ? OFFSET ?";
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
$clubs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Câu lạc bộ - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <?php include 'includes/header.php'; ?>
        
        <div class="admin-content">
            <div class="page-header">
                <div>
                    <h1>Quản lý Câu lạc bộ</h1>
                    <p>Tổng cộng: <strong><?= number_format($total_clubs) ?></strong> câu lạc bộ</p>
                </div>
            </div>
            
            <div class="filter-bar">
                <form method="GET" class="filter-form">
                    <div class="search-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text" name="search" placeholder="Tìm kiếm theo tên hoặc lĩnh vực..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <button type="submit" class="btn-primary">Tìm kiếm</button>
                    <?php if (!empty($search)): ?>
                    <a href="clubs.php" class="btn-secondary">Xóa bộ lọc</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên CLB</th>
                            <th>Lĩnh vực</th>
                            <th>Đội trưởng</th>
                            <th>Số thành viên</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($clubs)): ?>
                            <?php foreach ($clubs as $club): ?>
                            <tr>
                                <td><?= $club['id'] ?></td>
                                <td><?= htmlspecialchars($club['ten_clb']) ?></td>
                                <td><?= htmlspecialchars($club['linh_vuc']) ?></td>
                                <td><?= htmlspecialchars($club['doi_truong'] ?? 'Chưa có') ?></td>
                                <td><?= number_format($club['so_thanh_vien']) ?></td>
                                <td><?= date('d/m/Y', strtotime($club['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="../club-detail.php?id=<?= $club['id'] ?>" class="btn-icon btn-view" title="Xem">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                        <button class="btn-icon btn-delete" onclick="deleteClub(<?= $club['id'] ?>)" title="Xóa">
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
            
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                <a href="?page=<?= $current_page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-btn">Trước</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                <a href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   class="page-num <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?= $current_page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-btn">Sau</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
    <script>
    function deleteClub(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa câu lạc bộ này?')) return;
        alert('Chức năng xóa đang được phát triển');
    }
    </script>
</body>
</html>

