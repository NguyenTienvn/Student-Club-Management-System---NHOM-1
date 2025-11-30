<?php 
session_start();
require 'site.php';
require_once(__DIR__ . "/assets/database/connect.php");

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Vui lòng đăng nhập!";
    header("Location: login.php");
    exit;
}

$club_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($club_id <= 0) {
    $_SESSION['error'] = "ID câu lạc bộ không hợp lệ";
    header("Location: myclub.php");
    exit;
}

$page_css = "list_sukien.css";
load_top();
load_header();

global $conn;

// Lấy tên câu lạc bộ
$sql_club = "SELECT ten_clb FROM clubs WHERE id = ?";
$stmt = $conn->prepare($sql_club);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$club = $stmt->get_result()->fetch_assoc();
$ten_clb = $club['ten_clb'] ?? 'Câu lạc bộ';
$stmt->close();

// Thống kê
$stats_sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN trang_thai = 'sap_dien_ra' THEN 1 ELSE 0 END) as sap_dien_ra,
                SUM(CASE WHEN trang_thai = 'dang_dien_ra' THEN 1 ELSE 0 END) as dang_dien_ra,
                SUM(CASE WHEN trang_thai = 'da_ket_thuc' THEN 1 ELSE 0 END) as da_ket_thuc
            FROM events 
            WHERE club_id = ?";
$stats_stmt = $conn->prepare($stats_sql);
$stats_stmt->bind_param("i", $club_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
$stats_stmt->close();

// Danh sách sự kiện
$sql = "SELECT 
            id, ten_su_kien, mo_ta, anh_bia, dia_diem,
            thoi_gian_bat_dau, thoi_gian_ket_thuc,
            so_luong_toi_da, han_dang_ky, trang_thai
        FROM events 
        WHERE club_id = ? 
        ORDER BY thoi_gian_bat_dau DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="list-sk-container">
    <!-- Header -->
    <div class="page-header">
        <div class="header-left">
            <button class="btn-back" onclick="window.location.href='Dashboard.php?id=<?= $club_id ?>'">
                ← Quay lại
            </button>
            <div class="header-title">
                <h1>Danh sách sự kiện</h1>
                <p class="club-name"><?= htmlspecialchars($ten_clb) ?></p>
            </div>
        </div>
        <a href="add_Su_kien.php?id=<?= $club_id ?>" class="btn-add">
            <span class="icon">+</span> Tạo sự kiện mới
        </a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Tổng sự kiện</div>
            </div>
        </div>
        <div class="stat-card upcoming">
            <div class="stat-icon">🕒</div>
            <div class="stat-content">
                <div class="stat-number"><?= $stats['sap_dien_ra'] ?></div>
                <div class="stat-label">Sắp diễn ra</div>
            </div>
        </div>
        <div class="stat-card ongoing">
            <div class="stat-icon">🎯</div>
            <div class="stat-content">
                <div class="stat-number"><?= $stats['dang_dien_ra'] ?></div>
                <div class="stat-label">Đang diễn ra</div>
            </div>
        </div>
        <div class="stat-card ended">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <div class="stat-number"><?= $stats['da_ket_thuc'] ?></div>
                <div class="stat-label">Đã kết thúc</div>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <?php if ($result->num_rows > 0): ?>
        <div class="events-grid">
            <?php while ($event = $result->fetch_assoc()): ?>
                <div class="event-card" data-event-id="<?= $event['id'] ?>">
                    <div class="event-image">
                        <?php if (!empty($event['anh_bia'])): ?>
                            <img src="<?= htmlspecialchars($event['anh_bia']) ?>" 
                                 alt="<?= htmlspecialchars($event['ten_su_kien']) ?>">
                        <?php else: ?>
                            <div class="placeholder-img">
                                <span>📅</span>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        $status_map = [
                            'sap_dien_ra' => ['text' => 'Sắp diễn ra', 'class' => 'upcoming'],
                            'dang_dien_ra' => ['text' => 'Đang diễn ra', 'class' => 'ongoing'],
                            'da_ket_thuc' => ['text' => 'Đã kết thúc', 'class' => 'ended'],
                            'da_huy' => ['text' => 'Đã hủy', 'class' => 'cancelled']
                        ];
                        $status = $status_map[$event['trang_thai']] ?? ['text' => 'Không xác định', 'class' => ''];
                        ?>
                        <span class="status-badge <?= $status['class'] ?>">
                            <?= $status['text'] ?>
                        </span>
                        
                        <!-- Quick Actions Menu -->
                        <div class="quick-actions">
                            <button class="btn-menu" onclick="toggleMenu(<?= $event['id'] ?>)">⋮</button>
                            <div class="actions-menu" id="menu-<?= $event['id'] ?>">
                                <button onclick="quickEditStatus(<?= $event['id'] ?>, '<?= $event['trang_thai'] ?>')">
                                    🔄 Đổi trạng thái
                                </button>
                                <a href="edit_sk.php?id=<?= $event['id'] ?>">
                                    ✏️ Chỉnh sửa đầy đủ
                                </a>
                                <button onclick="deleteEvent(<?= $event['id'] ?>)" class="danger">
                                    🗑️ Xóa sự kiện
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="event-content">
                        <h3 class="event-title"><?= htmlspecialchars($event['ten_su_kien']) ?></h3>
                        
                        <div class="event-details">
                            <div class="detail-item">
                                <span class="icon">📅</span>
                                <span><?= date('d/m/Y H:i', strtotime($event['thoi_gian_bat_dau'])) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="icon">📍</span>
                                <span><?= htmlspecialchars($event['dia_diem'] ?: 'Chưa cập nhật') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="icon">👥</span>
                                <span><?= $event['so_luong_toi_da'] ? $event['so_luong_toi_da'] . ' người' : 'Không giới hạn' ?></span>
                            </div>
                        </div>

                        <?php if ($event['mo_ta']): ?>
                            <p class="event-desc">
                                <?= mb_substr(htmlspecialchars($event['mo_ta']), 0, 100) ?>...
                            </p>
                        <?php endif; ?>

                        <div class="event-actions">
                            <a href="chi_tiet_su_kien.php?id=<?= $event['id'] ?>" class="btn-view">
                                Xem chi tiết
                            </a>
                            <a href="edit_sk.php?id=<?= $event['id'] ?>" class="btn-edit">
                                Chỉnh sửa
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <h3>Chưa có sự kiện nào</h3>
            <p>Hãy tạo sự kiện đầu tiên cho câu lạc bộ của bạn!</p>
            <a href="add_Su_kien.php?id=<?= $club_id ?>" class="btn-add-large">
                + Tạo sự kiện đầu tiên
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Edit Status Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🔄 Thay đổi trạng thái sự kiện</h3>
            <button class="btn-close" onclick="closeStatusModal()">×</button>
        </div>
        <form id="statusForm" onsubmit="updateStatus(event)">
            <input type="hidden" id="edit_event_id" name="event_id">
            
            <div class="form-group">
                <label>Trạng thái mới:</label>
                <select id="new_status" name="trang_thai" required>
                    <option value="sap_dien_ra">🕒 Sắp diễn ra</option>
                    <option value="dang_dien_ra">🎯 Đang diễn ra</option>
                    <option value="da_ket_thuc">✅ Đã kết thúc</option>
                    <option value="da_huy">❌ Đã hủy</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeStatusModal()">Hủy</button>
                <button type="submit" class="btn-submit">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle menu
function toggleMenu(eventId) {
    const menu = document.getElementById('menu-' + eventId);
    const allMenus = document.querySelectorAll('.actions-menu');
    
    allMenus.forEach(m => {
        if (m !== menu) m.classList.remove('show');
    });
    
    menu.classList.toggle('show');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.quick-actions')) {
        document.querySelectorAll('.actions-menu').forEach(m => m.classList.remove('show'));
    }
});

// Quick edit status
function quickEditStatus(eventId, currentStatus) {
    document.getElementById('edit_event_id').value = eventId;
    document.getElementById('new_status').value = currentStatus;
    document.getElementById('statusModal').style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

// Update status
function updateStatus(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    fetch('quick_update_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Đã cập nhật trạng thái thành công!');
            location.reload();
        } else {
            alert('❌ Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Lỗi kết nối: ' + error);
    });
}

// Delete event
function deleteEvent(eventId) {
    if (!confirm('⚠️ Bạn có chắc chắn muốn xóa sự kiện này?\nHành động này không thể hoàn tác!')) {
        return;
    }
    
    fetch('delete_event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'event_id=' + eventId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Đã xóa sự kiện thành công!');
            location.reload();
        } else {
            alert('❌ Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Lỗi kết nối: ' + error);
    });
}
</script>

<?php 
$stmt->close();
$conn->close();
load_footer();
?>
