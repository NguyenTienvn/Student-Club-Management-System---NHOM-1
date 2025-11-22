<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require '../assets/database/connect.php';

$id = intval($_GET['id'] ?? 0);
$club = $conn->query("SELECT * FROM clubs WHERE id = $id")->fetch_assoc();

if (!$club) {
    header('Location: clubs.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_clb = trim($_POST['ten_clb'] ?? '');
    $mo_ta = trim($_POST['mo_ta'] ?? '');
    $linh_vuc = trim($_POST['linh_vuc'] ?? '');
    $color = trim($_POST['color'] ?? '#667eea');
    $ngay_thanh_lap = $_POST['ngay_thanh_lap'] ?? date('Y-m-d');
    
    if (empty($ten_clb)) {
        $error = "Vui lòng nhập tên CLB!";
    } else {
        $stmt = $conn->prepare("UPDATE clubs SET ten_clb=?, mo_ta=?, linh_vuc=?, color=?, ngay_thanh_lap=? WHERE id=?");
        $stmt->bind_param("sssssi", $ten_clb, $mo_ta, $linh_vuc, $color, $ngay_thanh_lap, $id);
        
        if ($stmt->execute()) {
            $success = "Cập nhật CLB thành công!";
            header('Location: clubs.php');
            exit();
        } else {
            $error = "Có lỗi xảy ra: " . $conn->error;
        }
    }
}

$page_title = "Chỉnh sửa CLB";
include 'includes/header.php';
?>

<div class="form-container">
    <div class="form-card">
        <h2>Chỉnh sửa câu lạc bộ</h2>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="ten_clb">Tên CLB *</label>
                <input type="text" id="ten_clb" name="ten_clb" required value="<?php echo htmlspecialchars($club['ten_clb']); ?>">
            </div>
            
            <div class="form-group">
                <label for="linh_vuc">Lĩnh vực</label>
                <select id="linh_vuc" name="linh_vuc">
                    <option value="Học thuật" <?php echo ($club['linh_vuc'] ?? '') == 'Học thuật' ? 'selected' : ''; ?>>Học thuật</option>
                    <option value="Thể thao" <?php echo ($club['linh_vuc'] ?? '') == 'Thể thao' ? 'selected' : ''; ?>>Thể thao</option>
                    <option value="Nghệ thuật" <?php echo ($club['linh_vuc'] ?? '') == 'Nghệ thuật' ? 'selected' : ''; ?>>Nghệ thuật</option>
                    <option value="Công nghệ" <?php echo ($club['linh_vuc'] ?? '') == 'Công nghệ' ? 'selected' : ''; ?>>Công nghệ</option>
                    <option value="Tình nguyện" <?php echo ($club['linh_vuc'] ?? '') == 'Tình nguyện' ? 'selected' : ''; ?>>Tình nguyện</option>
                    <option value="Khác" <?php echo ($club['linh_vuc'] ?? '') == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="color">Màu đại diện</label>
                <input type="color" id="color" name="color" value="<?php echo htmlspecialchars($club['color'] ?? '#667eea'); ?>">
            </div>
            
            <div class="form-group">
                <label for="ngay_thanh_lap">Ngày thành lập</label>
                <input type="date" id="ngay_thanh_lap" name="ngay_thanh_lap" value="<?php echo $club['ngay_thanh_lap'] ?? date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <textarea id="mo_ta" name="mo_ta" rows="6"><?php echo htmlspecialchars($club['mo_ta'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-actions">
                <a href="clubs.php" class="btn-secondary">Hủy</a>
                <button type="submit" class="btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<style>
.form-container {
    max-width: 800px;
}

.form-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.form-card h2 {
    font-size: 28px;
    font-weight: 900;
    color: #2d3748;
    margin-bottom: 30px;
}

.admin-form .form-group {
    margin-bottom: 25px;
}

.admin-form label {
    display: block;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 10px;
    font-size: 14px;
}

.admin-form input,
.admin-form select,
.admin-form textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    font-family: 'Inter', sans-serif;
    transition: all 0.3s ease;
}

.admin-form input:focus,
.admin-form select:focus,
.admin-form textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.admin-form textarea {
    resize: vertical;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-primary,
.btn-secondary {
    padding: 14px 28px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    flex: 1;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #e2e8f0;
    color: #2d3748;
    display: inline-block;
    text-align: center;
}

.btn-secondary:hover {
    background: #cbd5e0;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    font-weight: 600;
}

.alert-error {
    background: #fed7d7;
    color: #c53030;
    border: 2px solid #fc8181;
}
</style>

<?php include 'includes/footer.php'; ?>
