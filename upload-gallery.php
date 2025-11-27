<?php
session_start();
require 'assets/database/connect.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Vui lòng đăng nhập!";
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$club_id = $_POST['club_id'] ?? 0;

if ($club_id <= 0) {
    $_SESSION['error'] = "Không tìm thấy câu lạc bộ!";
    header("Location: myclub.php");
    exit;
}

// Kiểm tra user có phải ban quản lý không (chỉ chủ nhiệm hoặc admin)
$sql = "SELECT chu_nhiem_id FROM clubs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$club = $stmt->get_result()->fetch_assoc();

$is_admin = false;
if ($club && $club['chu_nhiem_id'] == $user_id) {
    $is_admin = true;
} else {
    // Kiểm tra admin trong club_members
    $sql = "SELECT * FROM club_members WHERE club_id = ? AND user_id = ? AND vai_tro IN ('admin', 'owner')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $club_id, $user_id);
    $stmt->execute();
    $is_admin = $stmt->get_result()->num_rows > 0;
}

if (!$is_admin) {
    $_SESSION['error'] = "Chỉ ban quản lý CLB mới có quyền upload ảnh!";
    header("Location: club-detail.php?id=" . $club_id);
    exit;
}

// Kiểm tra và tạo/sửa bảng club_gallery
$check_table = $conn->query("SHOW TABLES LIKE 'club_gallery'");
if ($check_table && $check_table->num_rows > 0) {
    // Bảng đã tồn tại, kiểm tra cột uploaded_by
    $check_column = $conn->query("SHOW COLUMNS FROM club_gallery LIKE 'uploaded_by'");
    if ($check_column && $check_column->num_rows == 0) {
        // Thêm cột uploaded_by nếu chưa có
        $conn->query("ALTER TABLE club_gallery ADD COLUMN uploaded_by INT NULL");
    }
} else {
    // Tạo bảng mới
    $create_table = "CREATE TABLE club_gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        club_id INT NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        title VARCHAR(255),
        description TEXT,
        uploaded_by INT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_club_id (club_id)
    )";
    $conn->query($create_table);
}

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$uploaded_count = 0;
$errors = [];

// Debug: Kiểm tra xem có file không
if (!isset($_FILES['images'])) {
    $_SESSION['error'] = "Không có file nào được chọn!";
    header("Location: club-gallery.php?id=" . $club_id . "&mode=manage");
    exit;
}

// Xử lý upload nhiều ảnh
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $total_files = count($_FILES['images']['name']);
    
    for ($i = 0; $i < $total_files; $i++) {
        if ($_FILES['images']['error'][$i] == 0) {
            $filename = $_FILES['images']['name'][$i];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = 'gallery_' . $club_id . '_' . time() . '_' . $i . '.' . $ext;
                $upload_path = 'assets/img/gallery/' . $new_filename;
                
                // Tạo thư mục nếu chưa có
                if (!file_exists('assets/img/gallery')) {
                    mkdir('assets/img/gallery', 0777, true);
                }
                
                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $upload_path)) {
                    // Lưu vào database
                    $sql = "INSERT INTO club_gallery (club_id, image_url, title, description, uploaded_by) 
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssi", $club_id, $upload_path, $title, $description, $user_id);
                    
                    if ($stmt->execute()) {
                        $uploaded_count++;
                    } else {
                        $errors[] = "Lỗi database: " . $stmt->error;
                    }
                } else {
                    $errors[] = "Không thể di chuyển file: " . $filename;
                }
            } else {
                $errors[] = "File không hợp lệ: " . $filename . " (chỉ chấp nhận JPG, PNG, GIF)";
            }
        } else {
            $errors[] = "Lỗi upload file " . ($i+1) . ": " . $_FILES['images']['error'][$i];
        }
    }
} else {
    $_SESSION['error'] = "Vui lòng chọn ít nhất một ảnh!";
    header("Location: club-gallery.php?id=" . $club_id . "&mode=manage");
    exit;
}

if ($uploaded_count > 0) {
    $msg = "Đã tải lên $uploaded_count ảnh thành công!";
    if (!empty($errors)) {
        $msg .= " (Có " . count($errors) . " lỗi)";
    }
    $_SESSION['success'] = $msg;
} else {
    if (!empty($errors)) {
        $_SESSION['error'] = "Không thể tải ảnh lên: " . implode(", ", $errors);
    } else {
        $_SESSION['error'] = "Không thể tải ảnh lên. Vui lòng thử lại!";
    }
}

header("Location: club-gallery.php?id=" . $club_id . "&mode=manage");
exit;
?>
