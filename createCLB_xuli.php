<?php
session_start(); // Bắt đầu session để lấy user_id đăng nhập

// Kết nối database từ file đã có
include(__DIR__ . '/assets/database/dbleaderclub.php');  

if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}

// Kiểm tra user tồn tại
function userExists($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập!");
}

$chu_nhiem_id = $_SESSION['user_id']; // user đang đăng nhập sẽ là chủ nhiệm

 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ten_clb = $_POST['ten_clb'];
    $mo_ta = $_POST['mo_ta'];
    $linh_vuc = $_POST['linh_vuc'];
    $so_thanh_vien = intval($_POST['so_thanh_vien']);
  
    // Upload ảnh
    if (isset($_FILES['logo_url']) && $_FILES['logo_url']['error'] === 0) {
        $fileName = $_FILES['logo_url']['name'];
        $fileTmp = $_FILES['logo_url']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExt, $allowedExt)) {
            die("Chỉ chấp nhận file ảnh: jpg, jpeg, png, gif");
        }

        $newFileName = uniqid('clb_') . "." . $fileExt;
        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filePath = "uploads/" . $newFileName;

        if (!move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
            die("Lỗi upload ảnh!");
        }
    } else {
        die("Chưa chọn ảnh hoặc lỗi upload!");
    }

    // INSERT có đủ 6 cột, bao gồm chu_nhiem_id
    $stmt = $conn->prepare("
        INSERT INTO clubs (ten_clb, mo_ta, logo_url, linh_vuc, so_thanh_vien, chu_nhiem_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssssii", $ten_clb, $mo_ta, $filePath, $linh_vuc, $so_thanh_vien, $chu_nhiem_id);

    if ($stmt->execute()) {
        echo "<script>alert('Tạo câu lạc bộ thành công!'); window.location.href='myclub.php';</script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
