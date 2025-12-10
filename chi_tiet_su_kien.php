<?php
session_start();
$page_css = "chi_tiet_su_kien.css";
require 'site.php';
load_top();
load_header();

require('assets/database/connect.php');

// Lấy ID sự kiện
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) {
    die("ID sự kiện không hợp lệ");
}

// Lấy thông tin sự kiện
$sql = "SELECT e.*, c.ten_clb, c.linh_vuc, c.id as club_id, 
               logo.file_path AS logo_path,
               anh_bia.file_path AS anh_bia_path
        FROM events e 
        LEFT JOIN clubs c ON e.club_id = c.id 
        LEFT JOIN club_pages cp ON cp.club_id = c.id
        LEFT JOIN media_library logo ON cp.logo_id = logo.id
        LEFT JOIN media_library anh_bia ON e.anh_bia_id = anh_bia.id
        WHERE e.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Không tìm thấy sự kiện");
}

$event = $result->fetch_assoc();

// Đếm số lượng đã đăng ký (tất cả đều đã được duyệt)
$registered_count = 0;
$sql_count = "SELECT COUNT(*) as total FROM event_registrations WHERE event_id = ?";
$count_stmt = $conn->prepare($sql_count);
$count_stmt->bind_param("i", $event_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
if ($count_result->num_rows > 0) {
    $count_data = $count_result->fetch_assoc();
    $registered_count = (int)$count_data['total'];
}
$count_stmt->close();

// Xử lý ảnh bìa - lấy từ media_library
$event_image = 'https://via.placeholder.com/1200x500?text=Event+Image';
if (!empty($event['anh_bia_path'])) {
    $anh_bia_path = $event['anh_bia_path'];
    // Kiểm tra file có tồn tại không
    if (file_exists($anh_bia_path) || file_exists(__DIR__ . '/' . $anh_bia_path)) {
        $event_image = htmlspecialchars($anh_bia_path);
    }
}

// Xử lý trạng thái
$status_class = '';
$status_text = '';
switch($event['trang_thai']) {
    case 'sap_dien_ra':
        $status_class = 'upcoming';
        $status_text = 'Sắp diễn ra';
        break;
    case 'dang_dien_ra':
        $status_class = 'ongoing';
        $status_text = 'Đang diễn ra';
        break;
    case 'da_ket_thuc':
        $status_class = 'ended';
        $status_text = 'Đã kết thúc';
        break;
    default:
        $status_class = 'upcoming';
        $status_text = 'Sắp diễn ra';
}

// Format ngày tháng
$event_date = date('d', strtotime($event['thoi_gian_bat_dau']));
$event_month = 'Tháng ' . date('m', strtotime($event['thoi_gian_bat_dau']));
$event_year = date('Y', strtotime($event['thoi_gian_bat_dau']));
?>

<link rel="stylesheet" href="assets/css/chi_tiet_su_kien.css">

<div class="event-detail-container">
    <!-- HEADER IMAGE -->
    <div class="event-header">
        <img src="<?php echo $event_image; ?>" alt="<?php echo htmlspecialchars($event['ten_su_kien']); ?>"
             onerror="this.src='https://via.placeholder.com/1200x500?text=Event+Image'">
        <div class="event-header-overlay">
            <div class="event-date-large">
                <div class="date-day"><?php echo $event_date; ?></div>
                <div class="date-month"><?php echo $event_month; ?></div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="event-content">
        <div class="event-main">
            <!-- STATUS BADGE -->
            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
            
            <!-- TITLE -->
            <h1 class="event-title"><?php echo htmlspecialchars($event['ten_su_kien']); ?></h1>
            
            <!-- META INFO -->
            <div class="event-meta-info">
                <div class="meta-item">
                    <i class="icon">🏛️</i>
                    <div>
                        <strong>Câu lạc bộ</strong>
                        <p><?php echo htmlspecialchars($event['ten_clb'] ?? 'Chưa có CLB'); ?></p>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="icon">📍</i>
                    <div>
                        <strong>Địa điểm</strong>
                        <p><?php echo htmlspecialchars($event['dia_diem'] ?: 'Chưa cập nhật'); ?></p>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="icon">🕒</i>
                    <div>
                        <strong>Thời gian</strong>
                        <p><?php echo date('d/m/Y H:i', strtotime($event['thoi_gian_bat_dau'])); ?></p>
                        <?php if ($event['thoi_gian_ket_thuc']): ?>
                        <p class="end-time">→ <?php echo date('d/m/Y H:i', strtotime($event['thoi_gian_ket_thuc'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="icon">👥</i>
                    <div>
                        <strong>Số lượng</strong>
                        <p class="participant-info">
                            <span class="registered-count"><?php echo $registered_count; ?></span> / 
                            <span class="max-count"><?php echo $event['so_luong_toi_da']; ?></span> người đã đăng ký
                        </p>
                    </div>
                </div>
                
                <?php if ($event['han_dang_ky']): ?>
                <div class="meta-item">
                    <i class="icon">⏰</i>
                    <div>
                        <strong>Hạn đăng ký</strong>
                        <p><?php echo date('d/m/Y H:i', strtotime($event['han_dang_ky'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- DESCRIPTION -->
            <div class="event-section">
                <h2>📝 Giới thiệu / Mô tả</h2>
                <p><?php echo nl2br(htmlspecialchars($event['mo_ta'])); ?></p>
            </div>

            <!-- DETAILED CONTENT -->
            <?php if (!empty($event['noi_dung_chi_tiet'])): ?>
            <div class="event-section">
                <h2>📌 Nội dung chi tiết</h2>
                <p><?php echo nl2br(htmlspecialchars($event['noi_dung_chi_tiet'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- ACTION BUTTONS -->
            <div class="event-actions">
                <?php if ($event['trang_thai'] !== 'da_ket_thuc'): ?>
                <button class="btn-join-large" onclick="joinEvent(<?php echo $event_id; ?>)">
                    Tham gia sự kiện
                </button>
                <?php else: ?>
                <button class="btn-ended" disabled>
                    Sự kiện đã kết thúc
                </button>
                <?php endif; ?>
                
                <button class="btn-back" onclick="window.history.back()">
                    ← Quay lại
                </button>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="event-sidebar">
            <!-- CLUB INFO -->
            <div class="sidebar-card">
                <h3>Thông tin Câu lạc bộ</h3>
                <div class="club-info">
                    <?php 
                    $club_logo = $event['logo_path'] ?? '';
                    if (!empty($club_logo)): ?>
                    <img src="<?php echo htmlspecialchars($club_logo); ?>" 
                         alt="<?php echo htmlspecialchars($event['ten_clb']); ?>"
                         class="club-logo">
                    <?php endif; ?>
                    <h4><?php echo htmlspecialchars($event['ten_clb'] ?? 'Chưa có CLB'); ?></h4>
                    <?php if (!empty($event['linh_vuc'])): ?>
                    <p class="club-category"><?php echo htmlspecialchars($event['linh_vuc']); ?></p>
                    <?php endif; ?>
                    <a href="club-detail.php?id=<?php echo $event['club_id']; ?>" class="btn-view-club">
                        Xem CLB
                    </a>
                </div>
            </div>

            <!-- QUICK INFO -->
            <div class="sidebar-card">
                <h3>Thông tin nhanh</h3>
                <div class="quick-info">
                    <div class="info-row">
                        <span class="label">Trạng thái:</span>
                        <span class="value <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Đã đăng ký:</span>
                        <span class="value"><strong><?php echo $registered_count; ?></strong> / <?php echo $event['so_luong_toi_da']; ?> người</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Ngày tạo:</span>
                        <span class="value"><?php echo date('d/m/Y', strtotime($event['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <!-- SHARE -->
            <div class="sidebar-card">
                <h3>Chia sẻ sự kiện</h3>
                <div class="share-buttons">
                    <button class="share-btn facebook" onclick="shareOnFacebook()">
                        <i>📘</i> Facebook
                    </button>
                    <button class="share-btn twitter" onclick="shareOnTwitter()">
                        <i>🐦</i> Twitter
                    </button>
                    <button class="share-btn copy" onclick="copyLink()">
                        <i>🔗</i> Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function joinEvent(eventId) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        alert('Vui lòng đăng nhập để tham gia sự kiện!');
        window.location.href = 'login.php';
        return;
    <?php endif; ?>
    
    if (confirm('Bạn có chắc chắn muốn tham gia sự kiện này?')) {
        // Gửi request tham gia sự kiện
        fetch('process_join_event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'event_id=' + eventId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Đăng ký tham gia sự kiện thành công!');
                location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi đăng ký!');
        });
    }
}

function shareOnFacebook() {
    const url = window.location.href;
    window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank');
}

function shareOnTwitter() {
    const url = window.location.href;
    const text = '<?php echo htmlspecialchars($event['ten_su_kien']); ?>';
    window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(text), '_blank');
}

function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Đã copy link sự kiện!');
    }).catch(err => {
        console.error('Error copying:', err);
    });
}
</script>

<?php
load_footer();
?>
