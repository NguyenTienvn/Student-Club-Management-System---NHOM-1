<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page_css = "myclub.css";
require 'site.php';
load_top();
load_header();

// Kết nối database
require_once('assets/database/connect.php');

$user_id = $_SESSION['user_id'];

// Lấy danh sách CLB mà user là chủ nhiệm (dùng prepared statement để bảo mật)
$stmt = $conn->prepare("SELECT id, ten_clb, logo_url, linh_vuc FROM clubs WHERE chu_nhiem_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="myclub-wrapper">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="hero-icon">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <h1 class="hero-title">Câu lạc bộ của tôi</h1>
            <p class="hero-subtitle">Quản lý và phát triển các câu lạc bộ của bạn</p>
        </div>
    </div>

    <div class="container-myclub">
        <?php if ($result && $result->num_rows > 0): ?>
            
            <!-- Stats Overview -->
            <div class="stats-section">
                <div class="stat-card">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-info">
                        <h3><?= $result->num_rows ?></h3>
                        <p>Câu lạc bộ</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3>Chủ nhiệm</h3>
                        <p>Vai trò của bạn</p>
                    </div>
                </div>
                <div class="stat-card stat-card-action">
                    <a href="createCLB.php" class="stat-create-btn">
                        <span class="plus-icon">+</span>
                        <span>Tạo CLB mới</span>
                    </a>
                </div>
            </div>

            <!-- Club Grid -->
            <div class="clubs-section">
                <h2 class="section-title">
                    <span class="title-icon">📚</span>
                    Danh sách câu lạc bộ
                </h2>
                <div class="club-grid">
                    <?php 
                    $result->data_seek(0); // Reset pointer
                    while($row = $result->fetch_assoc()): 
                    ?>
                        <div class="club-card">
                            <div class="club-card-header">
                                <img src="<?= htmlspecialchars($row['logo_url']) ?>" 
                                     alt="<?= htmlspecialchars($row['ten_clb']) ?>" 
                                     class="club-avatar"
                                     onerror="this.src='assets/img/default-club.png'">
                                <span class="club-badge"><?= htmlspecialchars($row['linh_vuc']) ?></span>
                            </div>
                            <div class="club-card-body">
                                <h3 class="club-title"><?= htmlspecialchars($row['ten_clb']) ?></h3>
                                <div class="club-actions">
                                    <a href="Dashboard.php?id=<?= $row['id'] ?>" class="btn-primary">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="7" height="7"></rect>
                                            <rect x="14" y="3" width="7" height="7"></rect>
                                            <rect x="14" y="14" width="7" height="7"></rect>
                                            <rect x="3" y="14" width="7" height="7"></rect>
                                        </svg>
                                        Quản lý
                                    </a>
                                    <a href="edit_inf_CLB.php?id=<?= $row['id'] ?>" class="btn-secondary">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        Chỉnh sửa
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

        <?php else: ?>

            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-illustration">
                    <svg width="200" height="200" viewBox="0 0 200 200" fill="none">
                        <circle cx="100" cy="100" r="80" fill="#f0f4ff" opacity="0.5"/>
                        <circle cx="100" cy="100" r="60" fill="#e0e7ff" opacity="0.5"/>
                        <path d="M100 60 L100 100 L130 100" stroke="#6366f1" stroke-width="4" stroke-linecap="round"/>
                        <circle cx="100" cy="100" r="8" fill="#6366f1"/>
                    </svg>
                </div>
                <h2 class="empty-title">Chưa có câu lạc bộ nào</h2>
                <p class="empty-description">
                    Bắt đầu hành trình của bạn bằng cách tạo câu lạc bộ đầu tiên<br>
                    hoặc tham gia vào một cộng đồng sẵn có
                </p>
                <div class="empty-actions">
                    <a href="createCLB.php" class="btn-create-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tạo câu lạc bộ mới
                    </a>
                    <a href="DanhsachCLB.php" class="btn-explore">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        Khám phá CLB
                    </a>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>


<?php
load_footer();
?>