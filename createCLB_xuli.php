<?php
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
session_start(); // Bắt đầu session để lấy user_id đăng nhập

// Kết nối database từ file đã có
include(__DIR__ . '/assets/database/dbleaderclub.php');  

if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}
=======
session_start();
require_once(__DIR__ . '/assets/database/connect.php');
>>>>>>> Stashed changes

=======
session_start();
require_once(__DIR__ . '/assets/database/connect.php');

>>>>>>> Stashed changes
=======
session_start();
require_once(__DIR__ . '/assets/database/connect.php');

>>>>>>> Stashed changes
// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập!");
}

$chu_nhiem_id = $_SESSION['user_id']; 

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
            die("Chỉ chấp nhận ảnh jpg, jpeg, png, gif");
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

    // INSERT CLB
    $stmt = $conn->prepare("
        INSERT INTO clubs (ten_clb, mo_ta, logo_url, linh_vuc, so_thanh_vien, chu_nhiem_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssssii", 
        $ten_clb, 
        $mo_ta, 
        $filePath, 
        $linh_vuc, 
        $so_thanh_vien, 
        $chu_nhiem_id
    );

    if ($stmt->execute()) {

        // LẤY ID CLB VỪA TẠO
        $club_id = $conn->insert_id;

        // --- THÊM CHỦ NHIỆM VÀO club_members ---
        $stmt2 = $conn->prepare("
            INSERT INTO club_members (club_id, user_id, phong_ban_id, trang_thai)
            VALUES (?, ?, NULL, 'dang_hoat_dong')
        ");
        $stmt2->bind_param("ii", $club_id, $chu_nhiem_id);
        $stmt2->execute();
        $stmt2->close();

        echo "<script>
            alert('Tạo câu lạc bộ thành công!');
            window.location.href='myclub.php';
        </script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
