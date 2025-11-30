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

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) {
    $_SESSION['error'] = "ID sự kiện không hợp lệ";
    header("Location: myclub.php");
    exit;
}

$page_css = "add_sk.css";
load_top();
load_header();

global $conn;

// Lấy thông tin sự kiện
$sql = "SELECT e.*, c.ten_clb 
        FROM events e 
        JOIN clubs c ON e.club_id = c.id 
        WHERE e.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    $_SESSION['error'] = "Sự kiện không tồn tại";
    header("Location: myclub.php");
    exit;
}

// Kiểm tra quyền chỉnh sửa
$user_id = $_SESSION['user_id'];
$check_role_sql = "SELECT vai_tro FROM club_members WHERE club_id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_role_sql);
$check_stmt->bind_param("ii", $event['club_id'], $user_id);
$check_stmt->execute();
$role_result = $check_stmt->get_result();
$user_role = $role_result->num_rows > 0 ? $role_result->fetch_assoc()['vai_tro'] : '';
$check_stmt->close();

$can_edit = ($event['created_by'] == $user_id) || in_array($user_role, ['Chủ nhiệm', 'Phó chủ nhiệm']);
if (!$can_edit) {
    $_SESSION['error'] = "Bạn không có quyền chỉnh sửa sự kiện này";
    header("Location: list_su_kien.php?id=" . $event['club_id']);
    exit;
}

// Format datetime cho input
function format_for_input($datetime) {
    return date('Y-m-d\TH:i', strtotime($datetime));
}
?>

<div class="contain">
    <div class="contain-add-sk">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <h1>✏️ Chỉnh sửa sự kiện</h1>
        <p style="text-align: center; color: #718096; margin: -30px 0 30px 0; font-size: 16px;">
            <?= htmlspecialchars($event['ten_clb']) ?>
        </p>

        <form action="update_sk.php" method="POST" enctype="multipart/form-data" class="form-sk">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            
            <!-- Ảnh bìa -->
            <div class="section-title">📸 Ảnh bìa sự kiện</div>
            
            <div class="current-image">
                <?php if (!empty($event['anh_bia'])): ?>
                    <img src="<?= htmlspecialchars($event['anh_bia']) ?>" alt="Ảnh bìa hiện tại" id="preview-image" class="anh_bia">
                    <p class="image-note">Ảnh bìa hiện tại</p>
                <?php else: ?>
                    <div class="no-image">Chưa có ảnh bìa</div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="anh_bia_moi">Thay đổi ảnh bìa (tùy chọn)</label>
                <input type="file" id="anh_bia_moi" name="anh_bia_moi" accept="image/*" onchange="previewNewImage(this)" style="padding: 10px;">
                <small style="color: #718096; font-size: 13px;">Chấp nhận: JPG, PNG, GIF, WEBP. Tối đa 5MB</small>
            </div>

            <!-- Thông tin cơ bản -->
            <div class="section-title">📝 Thông tin cơ bản</div>
            
            <div class="form-group">
                <label for="ten_su_kien" class="name-event">Tên sự kiện <span style="color: #e53e3e;">*</span></label>
                <input type="text" id="ten_su_kien" name="ten_su_kien" 
                       value="<?= htmlspecialchars($event['ten_su_kien']) ?>" required>
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả ngắn <span style="color: #e53e3e;">*</span></label>
                <textarea id="mo_ta" name="mo_ta" rows="3" required><?= htmlspecialchars($event['mo_ta']) ?></textarea>
                <small style="color: #718096; font-size: 13px;">Mô tả ngắn gọn về sự kiện (hiển thị trong danh sách)</small>
            </div>

            <div class="form-group">
                <label for="noi_dung_chi_tiet">Nội dung chi tiết <span style="color: #e53e3e;">*</span></label>
                <textarea id="noi_dung_chi_tiet" name="noi_dung_chi_tiet" rows="6" required><?= htmlspecialchars($event['noi_dung_chi_tiet']) ?></textarea>
                <small style="color: #718096; font-size: 13px;">Mô tả chi tiết về sự kiện, chương trình, lịch trình...</small>
            </div>

            <!-- Thời gian & Địa điểm -->
            <div class="section-title">📅 Thời gian & Địa điểm</div>
            
            <div class="two-col">
                <div class="form-group">
                    <label for="tg_bat_dau">Thời gian bắt đầu <span style="color: #e53e3e;">*</span></label>
                    <input type="datetime-local" id="tg_bat_dau" name="tg_bat_dau" 
                           value="<?= format_for_input($event['thoi_gian_bat_dau']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="tg_ket_thuc">Thời gian kết thúc <span style="color: #e53e3e;">*</span></label>
                    <input type="datetime-local" id="tg_ket_thuc" name="tg_ket_thuc" 
                           value="<?= format_for_input($event['thoi_gian_ket_thuc']) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="dia_diem">Địa điểm <span style="color: #e53e3e;">*</span></label>
                <input type="text" id="dia_diem" name="dia_diem" 
                       value="<?= htmlspecialchars($event['dia_diem']) ?>" 
                       placeholder="VD: Hội trường A, Tòa nhà B..." required>
            </div>

            <!-- Đăng ký & Trạng thái -->
            <div class="section-title">👥 Đăng ký & Trạng thái</div>
            
            <div class="two-col">
                <div class="form-group">
                    <label for="so_luong">Số lượng tối đa <span style="color: #e53e3e;">*</span></label>
                    <input type="number" id="so_luong" name="so_luong" min="1" 
                           value="<?= $event['so_luong_toi_da'] ?>" required>
                    <small style="color: #718096; font-size: 13px;">Số người tối đa có thể tham gia</small>
                </div>

                <div class="form-group">
                    <label for="han_dang_ky">Hạn đăng ký</label>
                    <input type="datetime-local" id="han_dang_ky" name="han_dang_ky" 
                           value="<?= $event['han_dang_ky'] ? format_for_input($event['han_dang_ky']) : '' ?>">
                    <small style="color: #718096; font-size: 13px;">Thời hạn cuối để đăng ký tham gia</small>
                </div>
            </div>

            <div class="form-group">
                <label for="trang_thai">Trạng thái sự kiện <span style="color: #e53e3e;">*</span></label>
                <select id="trang_thai" name="trang_thai" required style="padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; background: #f7fafc; width: 100%;">
                    <option value="sap_dien_ra" <?= $event['trang_thai'] == 'sap_dien_ra' ? 'selected' : '' ?>>Sắp diễn ra</option>
                    <option value="dang_dien_ra" <?= $event['trang_thai'] == 'dang_dien_ra' ? 'selected' : '' ?>>Đang diễn ra</option>
                    <option value="da_ket_thuc" <?= $event['trang_thai'] == 'da_ket_thuc' ? 'selected' : '' ?>>Đã kết thúc</option>
                    <option value="da_huy" <?= $event['trang_thai'] == 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button type="button" class="btn-back" onclick="window.location.href='list_su_kien.php?id=<?= $event['club_id'] ?>'">
                    ← Quay lại
                </button>
                <button type="submit" class="btn-submit">
                    💾 Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewNewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview-image');
            if (preview) {
                preview.src = e.target.result;
                const note = document.querySelector('.image-note');
                if (note) note.textContent = 'Ảnh mới (chưa lưu)';
            } else {
                const currentImage = document.querySelector('.current-image');
                currentImage.innerHTML = `
                    <img src="${e.target.result}" alt="Ảnh mới" id="preview-image" class="anh_bia">
                    <p class="image-note">Ảnh mới (chưa lưu)</p>
                `;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Validate form trước khi submit
document.querySelector('.form-sk').addEventListener('submit', function(e) {
    const tgBatDau = new Date(document.getElementById('tg_bat_dau').value);
    const tgKetThuc = new Date(document.getElementById('tg_ket_thuc').value);
    const hanDangKy = document.getElementById('han_dang_ky').value;
    
    if (tgKetThuc <= tgBatDau) {
        e.preventDefault();
        alert('⚠️ Thời gian kết thúc phải sau thời gian bắt đầu!');
        return false;
    }
    
    if (hanDangKy) {
        const hanDK = new Date(hanDangKy);
        if (hanDK >= tgBatDau) {
            e.preventDefault();
            alert('⚠️ Hạn đăng ký phải trước thời gian bắt đầu sự kiện!');
            return false;
        }
    }
    
    return true;
});
</script>

<?php 
$conn->close();
load_footer();
?>
