<?php
// Khởi động session trước
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'site.php';

// Kiểm tra đăng nhập TRƯỚC khi load header
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require('assets/database/connect.php');
$user_id = $_SESSION['user_id'];

// Lấy ID CLB từ URL
$club_id = $_GET['id'] ?? 0;

// Lấy thông tin CLB
$sql = "SELECT * FROM clubs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();
$club = $result->fetch_assoc();

// Nếu không tìm thấy, redirect về danh sách
if (!$club) {
    header("Location: DanhsachCLB.php");
    exit();
}

// Lấy thông tin trang đại diện từ club_pages
$club_page = null;
try {
    // Kiểm tra xem bảng club_pages có tồn tại không
    $table_check = $conn->query("SHOW TABLES LIKE 'club_pages'");
    if ($table_check && $table_check->num_rows > 0) {
        $sql = "SELECT * FROM club_pages WHERE club_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $club_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $club_page = $result->fetch_assoc();
            // Merge thông tin từ club_pages vào $club
            // Banner được lưu riêng trong club_page, không ghi đè logo_url
            if ($club_page['banner_url']) $club['banner_url'] = $club_page['banner_url'];
            if ($club_page['logo_url']) $club['logo'] = $club_page['logo_url'];
            if ($club_page['description']) $club['mo_ta'] = $club_page['description'];
            if ($club_page['primary_color']) $club['color'] = $club_page['primary_color'];
            if ($club_page['website']) $club['website'] = $club_page['website'];
            if ($club_page['facebook']) $club['facebook'] = $club_page['facebook'];
            if ($club_page['instagram']) $club['instagram'] = $club_page['instagram'];
            if ($club_page['twitter']) $club['twitter'] = $club_page['twitter'];
        }
    }
} catch (Exception $e) {
    // Bảng chưa tồn tại, bỏ qua
    error_log("club_pages table not found: " . $e->getMessage());
}

// Bây giờ mới load header
$page_css = "club-detail.css";
load_top();
load_header();

// Đếm số thành viên
$sql = "SELECT COUNT(*) as total FROM club_members WHERE club_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$member_count = $stmt->get_result()->fetch_assoc()['total'];

// Kiểm tra user có phải chủ nhiệm không
$is_owner = ($club['chu_nhiem_id'] == $user_id);

// Kiểm tra user đã tham gia chưa
$sql = "SELECT * FROM club_members WHERE club_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $club_id, $user_id);
$stmt->execute();
$is_member = $stmt->get_result()->num_rows > 0;

// Lấy danh sách thành viên (top 12)
$members_result = [];
try {
    $sql = "SELECT u.id, u.ho_ten, u.avatar, cm.vai_tro 
            FROM club_members cm 
            JOIN users u ON cm.user_id = u.id 
            WHERE cm.club_id = ? 
            LIMIT 12";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $members = $stmt->get_result();
} catch (Exception $e) {
    $members = null;
}

// Lấy thống kê CLB
$stats = ['total_events' => 0, 'total_achievements' => 0, 'rating' => 0.0];
try {
    $sql = "SELECT * FROM club_stats WHERE club_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stats = $result->fetch_assoc();
    } else {
        // Debug: Nếu không có data, thử insert mẫu
        error_log("No stats found for club_id: " . $club_id);
    }
} catch (Exception $e) {
    // Nếu bảng chưa tồn tại, dùng giá trị mặc định
    error_log("Error loading stats: " . $e->getMessage());
}

// Lấy danh sách sự kiện sắp tới (3 events)
$events = [];
try {
    $sql = "SELECT * FROM events 
            WHERE club_id = ? AND trang_thai = 'sap_dien_ra' AND thoi_gian_bat_dau >= NOW()
            ORDER BY thoi_gian_bat_dau ASC
            LIMIT 3";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $events = $stmt->get_result();
} catch (Exception $e) {
    $events = null;
}

// Đếm số người đăng ký cho mỗi event
$event_participants = [];
if ($events && $events->num_rows > 0) {
    $events->data_seek(0);
    while ($event = $events->fetch_assoc()) {
        $sql = "SELECT COUNT(*) as total FROM event_registrations WHERE event_id = ? AND trang_thai = 'da_duyet'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $event['id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $event_participants[$event['id']] = $result['total'];
    }
    $events->data_seek(0);
}

// Lấy thành tựu (3 achievements gần nhất)
$achievements = [];
try {
    $sql = "SELECT * FROM club_achievements 
            WHERE club_id = ?
            ORDER BY achievement_date DESC
            LIMIT 3";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $achievements = $stmt->get_result();
} catch (Exception $e) {
    $achievements = null;
}

// Lấy gallery (4 ảnh gần nhất)
$gallery = [];
try {
    $sql = "SELECT * FROM club_gallery 
            WHERE club_id = ?
            ORDER BY uploaded_at DESC
            LIMIT 4";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $gallery = $stmt->get_result();
} catch (Exception $e) {
    $gallery = null;
}

// Lấy hoạt động gần đây (3 activities)
$activities = [];
try {
    $sql = "SELECT * FROM club_activities 
            WHERE club_id = ?
            ORDER BY created_at DESC
            LIMIT 3";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $activities = $stmt->get_result();
} catch (Exception $e) {
    $activities = null;
}
?>

<div class="club-detail-container">
    <!-- Cover Image -->
    <div class="club-cover">
        <?php if (!empty($club['banner_url'])): ?>
            <img src="<?php echo htmlspecialchars($club['banner_url']); ?>" 
                 alt="Cover" onerror="this.style.display='none'">
        <?php endif; ?>
        <div class="cover-overlay"></div>
    </div>

    <!-- Club Header -->
    <div class="club-header">
        <div class="club-header-content">
            <div class="club-badge" style="<?php echo !empty($club['logo']) && file_exists($club['logo']) ? 'background: white; padding: 8px;' : 'background: ' . htmlspecialchars($club['color'] ?? '#667eea') . ';'; ?>">
                <?php if (!empty($club['logo']) && file_exists($club['logo'])): ?>
                    <img src="<?php echo htmlspecialchars($club['logo']); ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                <?php else: ?>
                    <?php echo strtoupper(substr($club['ten_clb'], 0, 3)); ?>
                <?php endif; ?>
            </div>
            <div class="club-info">
                <div class="club-category"><?php echo htmlspecialchars($club['linh_vuc'] ?? 'Câu lạc bộ'); ?></div>
                <h1><?php echo htmlspecialchars($club['ten_clb']); ?></h1>
                <?php if ($club_page && $club_page['slogan']): ?>
                    <p class="club-slogan" style="font-style: italic; color: #667eea; margin: 8px 0;">
                        "<?php echo htmlspecialchars($club_page['slogan']); ?>"
                    </p>
                <?php endif; ?>
                <div class="club-stats">
                    <span>👥 <?php echo $member_count; ?> thành viên</span>
                    <span>📅 Thành lập <?php echo date('Y', strtotime($club['ngay_thanh_lap'] ?? 'now')); ?></span>
                </div>
            </div>
            <div class="club-actions">
                <?php if ($is_owner): ?>
                    <a href="Dashboard.php?id=<?php echo $club_id; ?>" class="btn-manage">
                        <span>⚙️</span> Quản lý CLB
                    </a>
                <?php elseif ($is_member): ?>
                    <button class="btn-joined" disabled>
                        <span>✓</span> Đã tham gia
                    </button>
                <?php else: ?>
                    <button class="btn-join" onclick="joinClub(<?php echo $club_id; ?>)">
                        <span>+</span> Tham gia
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="club-content">
        <div class="content-main">
            <!-- About Section -->
            <div class="section-card">
                <h2>📖 Giới thiệu</h2>
                <p class="club-description">
                    <?php echo nl2br(htmlspecialchars($club['mo_ta'] ?? 'Chưa có mô tả')); ?>
                </p>
            </div>



            <!-- Activities Section -->
            <div class="section-card">
                <h2>🎯 Hoạt động chính</h2>
                <div class="activities-grid">
                    <div class="activity-item">
                        <div class="activity-icon">📚</div>
                        <h3>Học tập</h3>
                        <p>Tổ chức các buổi workshop, seminar</p>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">🎉</div>
                        <h3>Sự kiện</h3>
                        <p>Tham gia và tổ chức các sự kiện</p>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">🤝</div>
                        <h3>Giao lưu</h3>
                        <p>Kết nối và chia sẻ kinh nghiệm</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="section-card">
                <div class="section-header">
                    <h2>📅 Sự kiện sắp tới</h2>
                    <a href="#" class="view-all">Xem tất cả →</a>
                </div>
                <div class="events-list">
                    <?php if ($events && $events->num_rows > 0): ?>
                        <?php while ($event = $events->fetch_assoc()): 
                            $event_date = new DateTime($event['thoi_gian_bat_dau']);
                            $start_time = $event_date->format('H:i');
                            $end_date = new DateTime($event['thoi_gian_ket_thuc']);
                            $end_time = $end_date->format('H:i');
                            $participants = $event_participants[$event['id']] ?? 0;
                        ?>
                        <div class="event-card">
                            <div class="event-date">
                                <div class="date-day"><?php echo $event_date->format('d'); ?></div>
                                <div class="date-month">Th<?php echo $event_date->format('m'); ?></div>
                            </div>
                            <div class="event-info">
                                <h4><?php echo htmlspecialchars($event['ten_su_kien']); ?></h4>
                                <p>🕐 <?php echo $start_time; ?> - <?php echo $end_time; ?> | 📍 <?php echo htmlspecialchars($event['dia_diem'] ?? 'Chưa xác định'); ?></p>
                                <div class="event-participants">
                                    <span>👥 <?php echo $participants; ?> người tham gia</span>
                                </div>
                            </div>
                            <button class="btn-event-join" onclick="joinEvent(<?php echo $event['id']; ?>)">Tham gia</button>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #718096; padding: 40px;">Chưa có sự kiện sắp tới</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Gallery -->
            <div class="section-card">
                <div class="section-header">
                    <h2>📸 Thư viện ảnh</h2>
                    <a href="club-gallery.php?id=<?= $club_id ?>&mode=view" class="view-all">Xem tất cả →</a>
                </div>
                <div class="gallery-grid">
                    <?php 
                    $gradients = [
                        'linear-gradient(135deg, #667eea, #764ba2)',
                        'linear-gradient(135deg, #f093fb, #f5576c)',
                        'linear-gradient(135deg, #4facfe, #00f2fe)',
                        'linear-gradient(135deg, #43e97b, #38f9d7)'
                    ];
                    $index = 0;
                    if ($gallery && $gallery->num_rows > 0): 
                        while ($photo = $gallery->fetch_assoc()): 
                    ?>
                        <a href="club-gallery.php?id=<?= $club_id ?>&mode=view" class="gallery-item" style="background: <?php echo $gradients[$index % 4]; ?>; <?php if (!empty($photo['image_url'])): ?>background-image: url('<?php echo htmlspecialchars($photo['image_url']); ?>'); background-size: cover; background-position: center;<?php endif; ?>">
                            <div class="gallery-overlay">
                                <span><?php echo htmlspecialchars($photo['title'] ?? 'Ảnh CLB'); ?></span>
                            </div>
                        </a>
                    <?php 
                        $index++;
                        endwhile; 
                    else: 
                        // Hiển thị placeholder nếu chưa có ảnh
                        for ($i = 0; $i < 4; $i++):
                    ?>
                        <a href="club-gallery.php?id=<?= $club_id ?>&mode=view" class="gallery-item" style="background: <?php echo $gradients[$i]; ?>;">
                            <div class="gallery-overlay">
                                <span>Chưa có ảnh</span>
                            </div>
                        </a>
                    <?php 
                        endfor;
                    endif; 
                    ?>
                </div>
            </div>

            <!-- Achievements -->
            <div class="section-card">
                <h2>🏆 Thành tựu</h2>
                <div class="achievements-list">
                    <?php if ($achievements && $achievements->num_rows > 0): ?>
                        <?php while ($achievement = $achievements->fetch_assoc()): 
                            $ach_date = new DateTime($achievement['achievement_date']);
                        ?>
                        <div class="achievement-item">
                            <div class="achievement-icon"><?php echo $achievement['icon'] ?? '🏆'; ?></div>
                            <div class="achievement-info">
                                <h4><?php echo htmlspecialchars($achievement['title']); ?></h4>
                                <p><?php echo htmlspecialchars($achievement['description']); ?></p>
                                <span class="achievement-date">Tháng <?php echo $ach_date->format('m, Y'); ?></span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #718096; padding: 40px;">Chưa có thành tựu nào</p>
                    <?php endif; ?>
                </div>
            </div>


        </div>

        <!-- Sidebar -->
        <div class="content-sidebar">


            <!-- Contact Card -->
            <div class="sidebar-card">
                <h3>📞 Liên hệ</h3>
                <div class="contact-info">
                    <?php if (!empty($club['email'])): ?>
                        <div class="contact-item">
                            <span class="icon">📧</span>
                            <a href="mailto:<?php echo htmlspecialchars($club['email']); ?>">
                                <?php echo htmlspecialchars($club['email']); ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="contact-item">
                            <span class="icon">📧</span>
                            <a href="mailto:club@example.com">club@example.com</a>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($club['phone'])): ?>
                        <div class="contact-item">
                            <span class="icon">📱</span>
                            <a href="tel:<?php echo htmlspecialchars($club['phone']); ?>">
                                <?php echo htmlspecialchars($club['phone']); ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="contact-item">
                            <span class="icon">📱</span>
                            <a href="tel:0123456789">0123 456 789</a>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($club['website'])): ?>
                        <div class="contact-item">
                            <span class="icon">🌐</span>
                            <a href="<?php echo htmlspecialchars($club['website']); ?>" target="_blank">
                                Website
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="contact-item">
                            <span class="icon">🌐</span>
                            <a href="#" target="_blank">Website</a>
                        </div>
                    <?php endif; ?>
                    <div class="contact-item">
                        <span class="icon">📍</span>
                        <span style="color: #667eea; font-weight: 600;">Tòa nhà A, Tầng 3</span>
                    </div>
                </div>
            </div>

            <!-- Quick Info Card -->
            <div class="sidebar-card">
                <h3>ℹ️ Thông tin</h3>
                <div class="info-list">
                    <div class="info-item">
                        <span class="label">Lĩnh vực:</span>
                        <span class="value"><?php echo htmlspecialchars($club['linh_vuc'] ?? 'Công nghệ'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Thành lập:</span>
                        <span class="value"><?php echo date('d/m/Y', strtotime($club['ngay_thanh_lap'] ?? 'now')); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Trạng thái:</span>
                        <span class="value status-active">Đang hoạt động</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Độ tuổi:</span>
                        <span class="value">18-21</span>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="sidebar-card">
                <h3>🔗 Mạng xã hội</h3>
                <div class="social-links">
                    <?php if ($club_page && $club_page['facebook']): ?>
                        <a href="<?php echo htmlspecialchars($club_page['facebook']); ?>" target="_blank" class="social-btn facebook">
                            <span>📘</span>
                            <span>Facebook</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($club_page && $club_page['instagram']): ?>
                        <a href="<?php echo htmlspecialchars($club_page['instagram']); ?>" target="_blank" class="social-btn instagram">
                            <span>📷</span>
                            <span>Instagram</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($club_page && $club_page['twitter']): ?>
                        <a href="<?php echo htmlspecialchars($club_page['twitter']); ?>" target="_blank" class="social-btn youtube">
                            <span>🐦</span>
                            <span>Twitter</span>
                        </a>
                    <?php endif; ?>
                    <?php if (!$club_page || (!$club_page['facebook'] && !$club_page['instagram'] && !$club_page['twitter'])): ?>
                        <p style="text-align: center; color: #718096; padding: 20px;">Chưa có liên kết mạng xã hội</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="sidebar-card">
                <h3>⏰ Hoạt động gần đây</h3>
                <div class="timeline">
                    <?php if ($activities && $activities->num_rows > 0): ?>
                        <?php while ($activity = $activities->fetch_assoc()): 
                            $activity_date = new DateTime($activity['created_at']);
                            $now = new DateTime();
                            $diff = $now->diff($activity_date);
                            
                            if ($diff->d == 0 && $diff->h < 24) {
                                $time_ago = $diff->h . ' giờ trước';
                            } elseif ($diff->d == 1) {
                                $time_ago = '1 ngày trước';
                            } else {
                                $time_ago = $diff->d . ' ngày trước';
                            }
                        ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <p><strong><?php echo htmlspecialchars($activity['description']); ?></strong></p>
                                <span><?php echo $time_ago; ?></span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #718096; padding: 20px;">Chưa có hoạt động gần đây</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function joinClub(clubId) {
    if (confirm('Bạn có muốn tham gia CLB này không?')) {
        // TODO: Implement join club functionality
        alert('Chức năng đang phát triển!');
    }
}

function joinEvent(eventId) {
    if (confirm('Bạn có muốn đăng ký tham gia sự kiện này không?')) {
        // TODO: Implement join event functionality
        alert('Chức năng đăng ký sự kiện đang phát triển!');
    }
}
</script>

<?php
load_footer();
?>

