<?php
session_start();
require 'site.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Vui lòng đăng nhập!";
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$club_id = $_SESSION['club_id'] ?? 0;

if ($club_id <= 0) {
    $_SESSION['error'] = "Không tìm thấy câu lạc bộ!";
    header("Location: myclub.php");
    exit;
}

// Kết nối database để lấy thông tin CLB
require 'assets/database/connect.php';

// Lấy thông tin CLB hiện có
$sql = "SELECT * FROM clubs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$club = $stmt->get_result()->fetch_assoc();

// Lấy thông tin trang đại diện nếu đã có
$club_page = null;
$table_check = $conn->query("SHOW TABLES LIKE 'club_pages'");
if ($table_check && $table_check->num_rows > 0) {
    $sql = "SELECT * FROM club_pages WHERE club_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $club_page = $result->fetch_assoc();
    }
}

load_top();
load_header();
?>

<link rel="stylesheet" href="assets/css/tao_trang_dai_dien.css">

<div class="page-container">
    <div class="page-header">
        <h1>
            <span class="back-btn" onclick="window.location.href='Dashboard.php?id=<?= $club_id ?>'">←</span>
            Tạo Trang Đại Diện
        </h1>
        <p class="subtitle">Tùy chỉnh trang công khai cho Câu Lạc Bộ của bạn</p>
    </div>

    <form action="tao_trang_dai_dien_xuli.php" method="POST" enctype="multipart/form-data" class="page-form">
        
        <!-- Preview Section -->
        <div class="preview-section">
            <h2>👁️ Xem trước trang của bạn</h2>
            <div class="preview-box">
                <div class="preview-banner" id="previewBanner" <?php if (!empty($club_page['banner_url'])): ?>style="background-image: url('<?= htmlspecialchars($club_page['banner_url']) ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
                    <?php if (empty($club_page['banner_url'])): ?>
                        <span class="preview-placeholder">Ảnh bìa CLB</span>
                    <?php endif; ?>
                </div>
                <div class="preview-content">
                    <div class="preview-avatar" id="previewAvatar" <?php if (!empty($club['logo']) && file_exists($club['logo'])): ?>style="background-image: url('<?= htmlspecialchars($club['logo']) ?>'); background-size: cover;"<?php endif; ?>>
                        <?php if (empty($club['logo']) || !file_exists($club['logo'])): ?>
                            <span>Logo</span>
                        <?php endif; ?>
                    </div>
                    <h3 id="previewName"><?= htmlspecialchars($club['ten_clb'] ?? 'Tên Câu Lạc Bộ') ?></h3>
                    <p id="previewSlogan"><?= htmlspecialchars($club_page['slogan'] ?? 'Slogan của bạn sẽ hiển thị ở đây') ?></p>
                </div>
            </div>
        </div>

        <!-- Form Settings -->
        <div class="settings-section">
            <h2>⚙️ Cài đặt trang</h2>

            <div class="form-group">
                <label>🎨 Ảnh bìa trang</label>
                <div class="upload-area">
                    <?php if (!empty($club_page['banner_url'])): ?>
                        <img src="<?= htmlspecialchars($club_page['banner_url']) ?>" id="bannerPreview" class="image-preview" alt="Banner preview">
                    <?php else: ?>
                        <img id="bannerPreview" class="image-preview" alt="Banner preview" style="display:none;">
                    <?php endif; ?>
                    <label class="upload-btn">
                        📷 <?= !empty($club_page['banner_url']) ? 'Thay đổi ảnh bìa' : 'Chọn ảnh bìa' ?>
                        <input type="file" name="banner" id="bannerInput" accept="image/*" style="display:none">
                    </label>
                    <p class="hint">Kích thước đề xuất: 1200x400px (ảnh ngang)</p>
                </div>
            </div>

            <div class="form-group">
                <label>🖼️ Logo/Avatar CLB</label>
                <div class="upload-area">
                    <?php 
                    $current_logo = '';
                    if (!empty($club_page['logo_url'])) {
                        $current_logo = $club_page['logo_url'];
                    } elseif (!empty($club['logo']) && file_exists($club['logo'])) {
                        $current_logo = $club['logo'];
                    }
                    ?>
                    <?php if ($current_logo): ?>
                        <img src="<?= htmlspecialchars($current_logo) ?>" id="avatarPreview" class="image-preview avatar-preview" alt="Avatar preview">
                    <?php else: ?>
                        <img id="avatarPreview" class="image-preview avatar-preview" alt="Avatar preview" style="display:none;">
                    <?php endif; ?>
                    <label class="upload-btn">
                        📷 <?= $current_logo ? 'Thay đổi logo' : 'Chọn logo' ?>
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none">
                    </label>
                    <p class="hint">Kích thước đề xuất: 200x200px (hình vuông)</p>
                </div>
            </div>

            <div class="form-group">
                <label>✨ Slogan/Khẩu hiệu</label>
                <input type="text" name="slogan" id="sloganInput" placeholder="VD: Nơi đam mê được thăng hoa" maxlength="100" value="<?= htmlspecialchars($club_page['slogan'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>📝 Giới thiệu ngắn</label>
                <textarea name="description" id="descriptionInput" rows="4" placeholder="Mô tả ngắn gọn về CLB của bạn..."><?= htmlspecialchars($club_page['description'] ?? $club['mo_ta'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>🎨 Màu chủ đạo</label>
                <div class="color-picker-group">
                    <input type="color" name="primary_color" id="primaryColor" value="<?= htmlspecialchars($club_page['primary_color'] ?? $club['color'] ?? '#667eea') ?>">
                    <span class="color-label">Màu chính</span>
                </div>
            </div>

            <div class="form-group">
                <label>🔗 Liên kết mạng xã hội</label>
                <div class="social-inputs">
                    <div class="social-input">
                        <span class="social-icon">📘</span>
                        <input type="url" name="facebook" placeholder="Link Facebook" value="<?= htmlspecialchars($club_page['facebook'] ?? '') ?>">
                    </div>
                    <div class="social-input">
                        <span class="social-icon">📷</span>
                        <input type="url" name="instagram" placeholder="Link Instagram" value="<?= htmlspecialchars($club_page['instagram'] ?? '') ?>">
                    </div>
                    <div class="social-input">
                        <span class="social-icon">🐦</span>
                        <input type="url" name="twitter" placeholder="Link Twitter" value="<?= htmlspecialchars($club_page['twitter'] ?? '') ?>">
                    </div>
                    <div class="social-input">
                        <span class="social-icon">🌐</span>
                        <input type="url" name="website" placeholder="Website" value="<?= htmlspecialchars($club_page['website'] ?? $club['website'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="toggle-label">
                    <input type="checkbox" name="is_public" value="1" <?= (!isset($club_page['is_public']) || $club_page['is_public'] == 1) ? 'checked' : '' ?>>
                    <span class="toggle-text">🌍 Công khai trang (cho phép mọi người xem)</span>
                </label>
            </div>
        </div>

        <input type="hidden" name="club_id" value="<?= $club_id ?>">

        <div class="button-group">
            <button type="submit" class="btn-save">💾 Lưu và Xuất bản</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='Dashboard.php?id=<?= $club_id ?>'">
                ❌ Hủy
            </button>
        </div>
    </form>
</div>

<script>
// Preview Banner
document.getElementById('bannerInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('bannerPreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('previewBanner').style.backgroundImage = `url(${e.target.result})`;
            document.getElementById('previewBanner').innerHTML = '';
        }
        reader.readAsDataURL(file);
    }
});

// Preview Avatar
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('previewAvatar').style.backgroundImage = `url(${e.target.result})`;
            document.getElementById('previewAvatar').innerHTML = '';
        }
        reader.readAsDataURL(file);
    }
});

// Preview Slogan
document.getElementById('sloganInput').addEventListener('input', function(e) {
    document.getElementById('previewSlogan').textContent = e.target.value || 'Slogan của bạn sẽ hiển thị ở đây';
});

// Preview Color
document.getElementById('primaryColor').addEventListener('input', function(e) {
    document.querySelector('.preview-box').style.borderColor = e.target.value;
});
</script>

<?php
load_footer();
?>
