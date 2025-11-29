<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page_css = "edit_inf_CLB.css";
require 'site.php';
load_top();
load_header();

require_once(__DIR__ . "/assets/database/connect.php");

$user_id = $_SESSION['user_id'];
$club_id = intval($_GET['id'] ?? 0);

if ($club_id <= 0) {
    echo "<script>alert('ID CLB không hợp lệ!'); window.location.href='myclub.php';</script>";
    exit();
}

// Kiểm tra xem các cột email_CLB và sodt_CLB có tồn tại không
$check_columns = $conn->query("SHOW COLUMNS FROM clubs LIKE 'email_CLB'");
$has_contact_columns = ($check_columns && $check_columns->num_rows > 0);

// Lấy thông tin CLB + check quyền
if ($has_contact_columns) {
    $sql = "SELECT id, ten_clb, mo_ta, logo_url, linh_vuc, so_thanh_vien, email_CLB, sodt_CLB 
            FROM clubs WHERE id=? AND chu_nhiem_id=?";
} else {
    $sql = "SELECT id, ten_clb, mo_ta, logo_url, linh_vuc, so_thanh_vien 
            FROM clubs WHERE id=? AND chu_nhiem_id=?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $club_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Không tìm thấy CLB hoặc bạn không có quyền!'); window.location.href='myclub.php';</script>";
    exit();
}

$club = $result->fetch_assoc();

// Set default values nếu không có cột contact
$club['email_CLB'] = $club['email_CLB'] ?? '';
$club['sodt_CLB'] = $club['sodt_CLB'] ?? '';
?>

<div class="edit-club-wrapper">
    <div class="edit-club-container">
        <!-- Header -->
        <div class="form-header">
            <div class="header-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
            </div>
            <h2>Hoàn thiện thông tin câu lạc bộ</h2>
            <p class="form-subtitle">Cập nhật thông tin chi tiết cho câu lạc bộ của bạn</p>
        </div>

        <form action="edit_inf_CLB_xuli.php" method="POST" enctype="multipart/form-data" class="edit-form">
            <input type="hidden" name="club_id" value="<?= $club['id'] ?>">

            <!-- Logo Section -->
            <div class="logo-section">
                <div class="current-logo">
                    <img src="<?= htmlspecialchars($club['logo_url']) ?>" alt="Logo" id="logoPreview" onerror="this.src='assets/img/default-club.png'">
                </div>
                <label class="upload-btn" for="logoUpload">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <span>Thay đổi logo</span>
                    <input type="file" id="logoUpload" name="logo" accept="image/*" style="display:none">
                </label>
            </div>

            <!-- Basic Info -->
            <div class="form-section">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Thông tin cơ bản
                </h3>

                <div class="form-group">
                    <label>Tên câu lạc bộ</label>
                    <input type="text" name="ten_clb" value="<?= htmlspecialchars($club['ten_clb']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Lĩnh vực hoạt động</label>
                        <select name="linh_vuc" required>
                            <option value="">Chọn lĩnh vực</option>
                            <option value="Học thuật" <?= ($club['linh_vuc']=="Học thuật") ? "selected":""; ?>>📚 Học thuật</option>
                            <option value="Thể thao" <?= ($club['linh_vuc']=="Thể thao") ? "selected":""; ?>>⚽ Thể thao</option>
                            <option value="Nghệ thuật" <?= ($club['linh_vuc']=="Nghệ thuật") ? "selected":""; ?>>🎨 Nghệ thuật</option>
                            <option value="Tình nguyện" <?= ($club['linh_vuc']=="Tình nguyện") ? "selected":""; ?>>❤️ Tình nguyện</option>
                            <option value="Văn nghệ" <?= ($club['linh_vuc']=="Văn nghệ") ? "selected":""; ?>>🎭 Văn nghệ</option>
                            <option value="Kỹ năng" <?= ($club['linh_vuc']=="Kỹ năng") ? "selected":""; ?>>💡 Kỹ năng</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Số lượng thành viên</label>
                        <input type="number" name="so_thanh_vien" value="<?= $club['so_thanh_vien'] ?>" min="1">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="form-section">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Giới thiệu câu lạc bộ
                </h3>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="mo_ta" rows="5" placeholder="Giới thiệu về mục đích, hoạt động của câu lạc bộ..."><?= htmlspecialchars($club['mo_ta'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="form-section">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    Thông tin liên hệ
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email liên hệ</label>
                        <input type="email" name="email_CLB" value="<?= htmlspecialchars($club['email_CLB'] ?? '') ?>" placeholder="club@example.com">
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="tel" name="sodt_CLB" value="<?= htmlspecialchars($club['sodt_CLB'] ?? '') ?>" placeholder="0123456789">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Lưu thay đổi
                </button>
                <a href="Dashboard.php?id=<?= $club_id ?>" class="btn-cancel">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Quay lại
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Preview logo khi chọn file
document.getElementById('logoUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php
load_footer();
?>