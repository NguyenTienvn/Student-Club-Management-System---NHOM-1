<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require '../assets/database/connect.php';

// Xử lý xóa club
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM clubs WHERE id = $id");
    header('Location: clubs.php');
    exit();
}

// Lấy danh sách clubs
$clubs = $conn->query("SELECT * FROM clubs ORDER BY created_at DESC");

$page_title = "Quản lý câu lạc bộ";
include 'includes/header.php';
?>

<div class="clubs-container">
    <div class="table-container">
        <div style="margin-bottom: 20px;">
            <a href="add-club.php" class="btn-add">➕ Thêm CLB mới</a>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên CLB</th>
                    <th>Mô tả</th>
                    <th>Thành viên</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($club = $clubs->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $club['id']; ?></td>
                    <td><?php echo htmlspecialchars($club['ten_clb']); ?></td>
                    <td><?php echo htmlspecialchars(substr($club['mo_ta'] ?? '', 0, 50)); ?>...</td>
                    <td><?php echo $club['so_thanh_vien'] ?? 0; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($club['ngay_thanh_lap'] ?? $club['created_at'] ?? 'now')); ?></td>
                    <td class="actions">
                        <a href="../club-detail.php?id=<?php echo $club['id']; ?>" class="btn-action btn-view" title="Xem" target="_blank">👁️</a>
                        <a href="edit-club.php?id=<?php echo $club['id']; ?>" class="btn-action btn-read" title="Sửa">✏️</a>
                        <a href="?action=delete&id=<?php echo $club['id']; ?>" class="btn-action btn-delete" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa CLB này?')">🗑️</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.btn-add {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 700;
    transition: all 0.3s ease;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}
</style>

<?php include 'includes/footer.php'; ?>
