<?php
session_start();
require_once __DIR__ . "/assets/database/connect.php";

$conn->set_charset("utf8mb4");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// === 1. Kiểm tra đăng nhập ===
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập để tạo sự kiện!";
    header("Location: dangnhap.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// === 2. Kiểm tra club_id ===
$club_id = (int)$_POST['club_id'];
if ($club_id <= 0) {
    $_SESSION['error'] = "Câu lạc bộ không hợp lệ.";
    header("Location: Dashboard.php");
    exit;
}

// === 3. Nhận dữ liệu ===
$ten_su_kien       = trim($_POST['ten_su_kien'] ?? '');
$mo_ta             = trim($_POST['mo_ta'] ?? '');
$noi_dung_chi_tiet = trim($_POST['noi_dung_chi_tiet'] ?? '');
$dia_diem          = trim($_POST['dia_diem'] ?? '');
$tg_bat_dau        = $_POST['tg_bat_dau'] ?? '';
$tg_ket_thuc       = $_POST['tg_ket_thuc'] ?? '';
$so_luong          = (int)($_POST['so_luong'] ?? 0);
$han_dang_ky       = $_POST['han_dang_ky'] ?? '';

// GÁN TRẠNG THÁI MẶC ĐỊNH SỚM NHẤT (tránh undefined)
$trang_thai = 'dang_dien_ra';  // ĐÚNG CHUẨN, CÓ DẤU CÁCH, TIẾNG VIỆT ĐẦY ĐỦ

function format_datetime_local($dt) {
    return str_replace("T", " ", $dt) . ":00"; // thêm giây
}

$tg_bat_dau  = format_datetime_local($tg_bat_dau);
$tg_ket_thuc = format_datetime_local($tg_ket_thuc);
$han_dang_ky = format_datetime_local($han_dang_ky);

// === 4. Validate dữ liệu ===
$errors = [];

if (empty($ten_su_kien))                     $errors[] = "Tên sự kiện không được để trống.";
if (empty($mo_ta))                           $errors[] = "Mô tả không được để trống.";
if (empty($noi_dung_chi_tiet))               $errors[] = "Nội dung chi tiết không được để trống.";
if (empty($dia_diem))                        $errors[] = "Địa điểm không được để trống.";
if (empty($tg_bat_dau) || empty($tg_ket_thuc))$errors[] = "Vui lòng chọn đầy đủ thời gian.";
if (empty($han_dang_ky))                     $errors[] = "Vui lòng chọn hạn đăng ký.";
if ($so_luong < 1)                           $errors[] = "Số lượng tối đa phải ≥ 1.";
if (strtotime($tg_bat_dau) >= strtotime($tg_ket_thuc)) {
    $errors[] = "Thời gian kết thúc phải sau thời gian bắt đầu.";
}
if (strtotime($han_dang_ky) >= strtotime($tg_bat_dau)) {
    $errors[] = "Hạn đăng ký phải trước ngày diễn ra sự kiện.";
}

if (!empty($errors)) {
    $_SESSION['error'] = implode("<br>• ", $errors);
    header("Location: add_Sukien.php");
    exit;
}

// === 5. Upload ảnh bìa (giữ nguyên, tốt rồi) ===
$anh_bia = '';
if (!isset($_FILES['anhbia']) || $_FILES['anhbia']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "Vui lòng tải lên ảnh bìa!";
    header("Location: add_Sukien.php");
    exit;
}

$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$ext     = strtolower(pathinfo($_FILES['anhbia']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    $_SESSION['error'] = "Chỉ chấp nhận file: jpg, jpeg, png, gif, webp";
    header("Location: add_Sukien.php");
    exit;
}
if ($_FILES['anhbia']['size'] > 5 * 1024 * 1024) {
    $_SESSION['error'] = "Ảnh bìa không được quá 5MB!";
    header("Location: add_Sukien.php");
    exit;
}

$upload_dir = __DIR__ . "/anh_bia_sk/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

$file_name   = "anhbia_" . time() . "_" . rand(1000, 9999) . "." . $ext;
$target_path = "anh_bia_sk/" . $file_name;
$full_path   = $upload_dir . $file_name;

if (!move_uploaded_file($_FILES['anhbia']['tmp_name'], $full_path)) {
    $_SESSION['error'] = "Lỗi upload ảnh!";
    header("Location: add_Sukien.php");
    exit;
}
$anh_bia = $target_path;

// === 6. INSERT vào DB – ĐÃ SỬA HOÀN HẢO ===
$sql = "INSERT INTO events (
            club_id, ten_su_kien, mo_ta, noi_dung_chi_tiet, anh_bia,
            dia_diem, thoi_gian_bat_dau, thoi_gian_ket_thuc,
            so_luong_toi_da, han_dang_ky, trang_thai, created_by, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    unlink($full_path);
    die("Lỗi prepare: " . $conn->error);
}

$stmt->bind_param(
    "isssssssissi",
    $club_id,
    $ten_su_kien,
    $mo_ta,
    $noi_dung_chi_tiet,
    $anh_bia,
    $dia_diem,
    $tg_bat_dau,
    $tg_ket_thuc,
    $so_luong,
    $han_dang_ky,
    $trang_thai,
    $user_id
);


if ($stmt->execute()) {
    $_SESSION['success'] = "Tạo sự kiện thành công!";
    header("Location: Dashboard.php?id=$club_id");
} else {
    unlink($full_path);
    $_SESSION['error'] = "Lỗi tạo sự kiện: " . $stmt->error;
    header("Location: add_Sukien.php");
}
$stmt->close();
$conn->close();
exit;
?>