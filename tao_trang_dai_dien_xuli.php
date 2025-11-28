<?php
session_start();
require 'assets/database/connect.php';

// Kiểm tra đăng nhập
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

// Kiểm tra quyền - Tạm thời bỏ qua để test
// TODO: Bật lại sau khi có bảng club_members đầy đủ
/*
$sql = "SELECT * FROM club_members WHERE club_id = ? AND user_id = ? AND vai_tro IN ('admin', 'owner')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $club_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Bạn không có quyền chỉnh sửa trang này!";
    header("Location: Dashboard.php?id=" . $club_id);
    exit;
}
*/

// Lấy dữ liệu từ form
$slogan = $_POST['slogan'] ?? '';
$description = $_POST['description'] ?? '';
$primary_color = $_POST['primary_color'] ?? '#667eea';
$facebook = $_POST['facebook'] ?? '';
$instagram = $_POST['instagram'] ?? '';
$twitter = $_POST['twitter'] ?? '';
$website = $_POST['website'] ?? '';
$is_public = isset($_POST['is_public']) ? 1 : 0;

// Xử lý upload ảnh bìa
$banner_path = null;
if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['banner']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array($ext, $allowed)) {
        $new_filename = 'banner_' . $club_id . '_' . time() . '.' . $ext;
        $upload_path = 'assets/img/' . $new_filename;
        
        if (move_uploaded_file($_FILES['banner']['tmp_name'], $upload_path)) {
            $banner_path = $upload_path;
        }
    }
}

// Xử lý upload logo/avatar
$avatar_path = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['avatar']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array($ext, $allowed)) {
        $new_filename = 'logo_' . $club_id . '_' . time() . '.' . $ext;
        $upload_path = 'assets/img/' . $new_filename;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
            $avatar_path = $upload_path;
        }
    }
}

// Cập nhật thông tin vào bảng clubs
$update_fields = [];
$params = [];
$types = '';

// Logo/Avatar được lưu vào cả logo và logo_url để đồng bộ
if ($avatar_path) {
    $update_fields[] = "logo = ?";
    $params[] = $avatar_path;
    $types .= 's';
    
    $update_fields[] = "logo_url = ?";
    $params[] = $avatar_path;
    $types .= 's';
}

// Banner được lưu riêng, không update vào bảng clubs
// (sẽ lưu vào club_pages ở dưới)

if ($description) {
    $update_fields[] = "mo_ta = ?";
    $params[] = $description;
    $types .= 's';
}

if ($primary_color) {
    $update_fields[] = "color = ?";
    $params[] = $primary_color;
    $types .= 's';
}

if ($website) {
    $update_fields[] = "website = ?";
    $params[] = $website;
    $types .= 's';
}

// Cập nhật bảng clubs
if (!empty($update_fields)) {
    $sql = "UPDATE clubs SET " . implode(", ", $update_fields) . " WHERE id = ?";
    $params[] = $club_id;
    $types .= 'i';
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
}

// Lưu thông tin trang đại diện vào bảng riêng (club_pages)
// Kiểm tra xem đã có bảng club_pages chưa
$table_check = $conn->query("SHOW TABLES LIKE 'club_pages'");
if ($table_check->num_rows == 0) {
    // Tạo bảng mới nếu chưa có
    $create_table = "CREATE TABLE club_pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        club_id INT NOT NULL,
        slogan VARCHAR(255),
        description TEXT,
        banner_url VARCHAR(255),
        logo_url VARCHAR(255),
        primary_color VARCHAR(20),
        facebook VARCHAR(255),
        instagram VARCHAR(255),
        twitter VARCHAR(255),
        website VARCHAR(255),
        is_public TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
        UNIQUE KEY unique_club (club_id)
    )";
    $conn->query($create_table);
}

// Insert hoặc Update thông tin trang đại diện
// Kiểm tra xem đã có record chưa
$check_sql = "SELECT * FROM club_pages WHERE club_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $club_id);
$check_stmt->execute();
$existing = $check_stmt->get_result()->fetch_assoc();

if ($existing) {
    // UPDATE - chỉ update các field có giá trị mới
    $update_parts = [];
    $update_params = [];
    $update_types = '';
    
    $update_parts[] = "slogan = ?";
    $update_params[] = $slogan;
    $update_types .= 's';
    
    $update_parts[] = "description = ?";
    $update_params[] = $description;
    $update_types .= 's';
    
    if ($banner_path) {
        $update_parts[] = "banner_url = ?";
        $update_params[] = $banner_path;
        $update_types .= 's';
    }
    
    if ($avatar_path) {
        $update_parts[] = "logo_url = ?";
        $update_params[] = $avatar_path;
        $update_types .= 's';
    }
    
    $update_parts[] = "primary_color = ?";
    $update_params[] = $primary_color;
    $update_types .= 's';
    
    $update_parts[] = "facebook = ?";
    $update_params[] = $facebook;
    $update_types .= 's';
    
    $update_parts[] = "instagram = ?";
    $update_params[] = $instagram;
    $update_types .= 's';
    
    $update_parts[] = "twitter = ?";
    $update_params[] = $twitter;
    $update_types .= 's';
    
    $update_parts[] = "website = ?";
    $update_params[] = $website;
    $update_types .= 's';
    
    $update_parts[] = "is_public = ?";
    $update_params[] = $is_public;
    $update_types .= 'i';
    
    $update_params[] = $club_id;
    $update_types .= 'i';
    
    $sql = "UPDATE club_pages SET " . implode(", ", $update_parts) . " WHERE club_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($update_types, ...$update_params);
} else {
    // INSERT mới
    $sql = "INSERT INTO club_pages (club_id, slogan, description, banner_url, logo_url, primary_color, facebook, instagram, twitter, website, is_public)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssi", 
        $club_id, 
        $slogan, 
        $description, 
        $banner_path, 
        $avatar_path, 
        $primary_color, 
        $facebook, 
        $instagram, 
        $twitter, 
        $website, 
        $is_public
    );
}

if ($stmt->execute()) {
    $_SESSION['success'] = "Đã lưu trang đại diện thành công!";
    header("Location: club-detail.php?id=" . $club_id);
} else {
    $_SESSION['error'] = "Có lỗi xảy ra: " . $stmt->error;
    header("Location: tao_trang_dai_dien.php");
}

$stmt->close();
$conn->close();
?>