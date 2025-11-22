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

// Kiểm tra user đã tham gia chưa
$sql = "SELECT * FROM club_members WHERE club_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $club_id, $user_id);
$stmt->execute();
$is_member = $stmt->get_result()->num_rows > 0;

// Lấy danh sách thành viên (top 12)
// Lấy danh sách thành viên (nếu bảng tồn tại)
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
    // Nếu bảng không tồn tại, tạo data mẫu
    $members = null;
}
?>

<div class="club-detail-container">
    <!-- Cover Image -->
    <div class="club-cover">
        <?php if (!empty($club['logo_url'])): ?>
            <img src="<?php echo htmlspecialchars($club['logo_url']); ?>" 
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
                <div class="club-stats">
                    <span>👥 <?php echo $member_count; ?> thành viên</span>
                    <span>📅 Thành lập <?php echo date('Y', strtotime($club['ngay_thanh_lap'] ?? 'now')); ?></span>
                </div>
            </div>
            <div class="club-actions">
                <?php if ($is_member): ?>
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
                <h2>🎯 Hoạt động</h2>
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

            <!-- Members Section -->
            <div class="section-card">
                <div class="section-header">
                    <h2>👥 Thành viên (<?php echo $member_count; ?>)</h2>
                    <a href="#" class="view-all">Xem tất cả →</a>
                </div>
                <div class="members-grid">
                    <?php if ($members && $members->num_rows > 0): ?>
                        <?php while ($member = $members->fetch_assoc()): ?>
                            <div class="member-card">
                                <img src="<?php echo !empty($member['avatar']) ? htmlspecialchars($member['avatar']) : 'assets/img/user.svg'; ?>" 
                                     alt="Avatar" onerror="this.src='assets/img/user.svg'">
                                <div class="member-info">
                                    <h4><?php echo htmlspecialchars($member['ho_ten']); ?></h4>
                                    <span class="member-role"><?php echo htmlspecialchars($member['vai_tro'] ?? 'Thành viên'); ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="grid-column: 1/-1; text-align: center; color: #718096;">Chưa có thành viên nào</p>
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
                    <?php endif; ?>
                    <?php if (!empty($club['phone'])): ?>
                        <div class="contact-item">
                            <span class="icon">📱</span>
                            <a href="tel:<?php echo htmlspecialchars($club['phone']); ?>">
                                <?php echo htmlspecialchars($club['phone']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($club['website'])): ?>
                        <div class="contact-item">
                            <span class="icon">🌐</span>
                            <a href="<?php echo htmlspecialchars($club['website']); ?>" target="_blank">
                                Website
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Info Card -->
            <div class="sidebar-card">
                <h3>ℹ️ Thông tin</h3>
                <div class="info-list">
                    <div class="info-item">
                        <span class="label">Lĩnh vực:</span>
                        <span class="value"><?php echo htmlspecialchars($club['linh_vuc'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Thành lập:</span>
                        <span class="value"><?php echo date('d/m/Y', strtotime($club['ngay_thanh_lap'] ?? 'now')); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Trạng thái:</span>
                        <span class="value status-active">Đang hoạt động</span>
                    </div>
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
</script>

<?php
load_footer();
?>
