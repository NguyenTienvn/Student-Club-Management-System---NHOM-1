<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require '../assets/database/connect.php';

// Xử lý cập nhật trạng thái
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action == 'read') {
        $conn->query("UPDATE lienhe SET status = 'read' WHERE id = $id");
    } elseif ($action == 'replied') {
        $conn->query("UPDATE lienhe SET status = 'replied' WHERE id = $id");
    } elseif ($action == 'delete') {
        $conn->query("DELETE FROM lienhe WHERE id = $id");
    }
    
    header('Location: contacts.php');
    exit();
}

// Lấy danh sách tin nhắn
$filter = $_GET['filter'] ?? 'all';
$where = '';
if ($filter == 'new') {
    $where = "WHERE status = 'new'";
} elseif ($filter == 'read') {
    $where = "WHERE status = 'read'";
} elseif ($filter == 'replied') {
    $where = "WHERE status = 'replied'";
}

$contacts = $conn->query("SELECT * FROM lienhe $where ORDER BY created_at DESC");

$page_title = "Quản lý tin nhắn";
include 'includes/header.php';
?>

<div class="contacts-container">
    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="?filter=all" class="tab <?php echo $filter == 'all' ? 'active' : ''; ?>">
            Tất cả (<?php echo $conn->query("SELECT COUNT(*) as c FROM lienhe")->fetch_assoc()['c']; ?>)
        </a>
        <a href="?filter=new" class="tab <?php echo $filter == 'new' ? 'active' : ''; ?>">
            Mới (<?php echo $conn->query("SELECT COUNT(*) as c FROM lienhe WHERE status='new'")->fetch_assoc()['c']; ?>)
        </a>
        <a href="?filter=read" class="tab <?php echo $filter == 'read' ? 'active' : ''; ?>">
            Đã đọc (<?php echo $conn->query("SELECT COUNT(*) as c FROM lienhe WHERE status='read'")->fetch_assoc()['c']; ?>)
        </a>
        <a href="?filter=replied" class="tab <?php echo $filter == 'replied' ? 'active' : ''; ?>">
            Đã trả lời (<?php echo $conn->query("SELECT COUNT(*) as c FROM lienhe WHERE status='replied'")->fetch_assoc()['c']; ?>)
        </a>
    </div>

    <!-- Contacts Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Tiêu đề</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($contact = $contacts->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $contact['id']; ?></td>
                    <td><?php echo htmlspecialchars($contact['name']); ?></td>
                    <td><?php echo htmlspecialchars($contact['email']); ?></td>
                    <td><?php echo htmlspecialchars(substr($contact['subject'], 0, 50)); ?>...</td>
                    <td>
                        <span class="status-badge status-<?php echo $contact['status']; ?>">
                            <?php 
                            echo $contact['status'] == 'new' ? 'Mới' : 
                                ($contact['status'] == 'read' ? 'Đã đọc' : 'Đã trả lời'); 
                            ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></td>
                    <td class="actions">
                        <a href="view-contact.php?id=<?php echo $contact['id']; ?>" class="btn-action btn-view" title="Xem">👁️</a>
                        <?php if ($contact['status'] == 'new'): ?>
                        <a href="?action=read&id=<?php echo $contact['id']; ?>" class="btn-action btn-read" title="Đánh dấu đã đọc">✓</a>
                        <?php endif; ?>
                        <a href="?action=delete&id=<?php echo $contact['id']; ?>" class="btn-action btn-delete" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa?')">🗑️</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
