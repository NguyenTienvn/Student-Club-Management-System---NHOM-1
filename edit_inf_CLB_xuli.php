<?php
session_start();
require_once(__DIR__ . "/assets/database/connect.php");

if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập!");
}

$user_id = $_SESSION['user_id'];
$club_id = intval($_POST['club_id'] ?? 0);

if ($club_id <= 0) {
    die("Dữ liệu không hợp lệ!");
}

// Lấy dữ liệu form
$ten_clb       = trim($_POST['ten_clb'] ?? '');
$linh_vuc      = trim($_POST['linh_vuc'] ?? '');
$so_tv         = intval($_POST['so_thanh_vien'] ?? 0);
$mo_ta         = trim($_POST['mo_ta'] ?? '');
$email         = trim($_POST['email_CLB'] ?? '');
$so_dien_thoai = trim($_POST['sodt_CLB'] ?? '');

// Check quyền
$sqlCheck = $conn->prepare("SELECT id, logo_url FROM clubs WHERE id=? AND chu_nhiem_id=?");
$sqlCheck->bind_param("ii", $club_id, $user_id);
$sqlCheck->execute();
$resultCheck = $sqlCheck->get_result();

if ($resultCheck->num_rows == 0) {
    echo "<script>alert('Bạn không có quyền chỉnh sửa CLB này!'); window.location.href='myclub.php';</script>";
    exit();
}

$oldData = $resultCheck->fetch_assoc();
$oldLogo = $oldData['logo_url'] ?? '';

// Xử lý upload logo mới
$logo_sql = "";
$logo_param = null;

if (!empty($_FILES['logo']['name'])) {
    $targetDir = "uploads/clubs/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES['logo']['name']);
    $targetPath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
        // Xóa logo cũ
        if (!empty($oldLogo) && file_exists($oldLogo)) unlink($oldLogo);
        $logo_sql = ", logo_url=?";
        $logo_param = $targetPath;
    }
}

// Kiểm tra xem các cột email_CLB và sodt_CLB có tồn tại không
$check_columns = $conn->query("SHOW COLUMNS FROM clubs LIKE 'email_CLB'");
$has_contact_columns = ($check_columns && $check_columns->num_rows > 0);

// Câu SQL update
if ($has_contact_columns) {
    // Có cột contact
    if ($logo_sql) {
        $sql = "UPDATE clubs SET ten_clb=?, mo_ta=?, linh_vuc=?, so_thanh_vien=?, email_CLB=?, sodt_CLB=? $logo_sql WHERE id=? AND chu_nhiem_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssii", $ten_clb, $mo_ta, $linh_vuc, $so_tv, $email, $so_dien_thoai, $logo_param, $club_id, $user_id);
    } else {
        $sql = "UPDATE clubs SET ten_clb=?, mo_ta=?, linh_vuc=?, so_thanh_vien=?, email_CLB=?, sodt_CLB=? WHERE id=? AND chu_nhiem_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssii", $ten_clb, $mo_ta, $linh_vuc, $so_tv, $email, $so_dien_thoai, $club_id, $user_id);
    }
} else {
    // Không có cột contact
    if ($logo_sql) {
        $sql = "UPDATE clubs SET ten_clb=?, mo_ta=?, linh_vuc=?, so_thanh_vien=? $logo_sql WHERE id=? AND chu_nhiem_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissii", $ten_clb, $mo_ta, $linh_vuc, $so_tv, $logo_param, $club_id, $user_id);
    } else {
        $sql = "UPDATE clubs SET ten_clb=?, mo_ta=?, linh_vuc=?, so_thanh_vien=? WHERE id=? AND chu_nhiem_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisii", $ten_clb, $mo_ta, $linh_vuc, $so_tv, $club_id, $user_id);
    }
}

if ($stmt->execute()) {
    echo "<script>alert('Cập nhật CLB thành công!'); window.location.href='Dashboard.php?id=$club_id';</script>";
} else {
    echo "<script>alert('Lỗi cập nhật: " . $conn->error . "'); window.history.back();</script>";
}

echo "<script>
    alert('Cập nhật CLB thành công!');
    window.location.href='edit_inf_CLB.php?id=$club_id';
</script>";
?>