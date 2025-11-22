<?php
// xulylogin.php - ĐÃ SỬA ĐÚNG CÚ PHÁP MYSQLI 100%
require_once('assets/database/connect.php'); // $conn đã có sẵn

// ================== ĐĂNG NHẬP ==================
function loginUser($username, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, ho_ten, username, email, password, vai_tro, so_dien_thoai FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) { // Sau này nên dùng password_hash + password_verify
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['ho_ten']    = $user['ho_ten'] ?? '';
            $_SESSION['email']     = $user['email'] ?? '';
            $_SESSION['vai_tro']   = $user['vai_tro'] ?? 'thanh_vien';
            $_SESSION['so_dien_thoai'] = $user['so_dien_thoai'] ?? '';
            return true;
        }
    }
    return false;
}

// ================== KIỂM TRA USERNAME TỒN TẠI ==================
function usernameExists($username) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// ================== ĐĂNG KÝ USER ==================
function registerUser($username, $password) {
    global $conn;
    
    if (usernameExists($username)) {
        return "Tên đăng nhập đã tồn tại!";
    }
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, vai_tro) VALUES (?, ?, 'thanh_vien')");
    $stmt->bind_param("ss", $username, $password);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return "Lỗi đăng ký: " . $conn->error;
    }
}

// ================== HOÀN THIỆN HỒ SƠ ==================
function completeUserProfile($username, $ho_ten, $email, $so_dien_thoai = '', $student_id = '', $class = '', $faculty = '', $gender = 'khac') {
    global $conn;
    
    // Kiểm tra email trùng (trừ chính người dùng)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND username != ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return "Email này đã được sử dụng!";
    }
    
    $stmt = $conn->prepare("UPDATE users SET ho_ten = ?, email = ?, so_dien_thoai = ?, student_id = ?, class = ?, faculty = ?, gender = ? WHERE username = ?");
    $stmt->bind_param("ssssssss", $ho_ten, $email, $so_dien_thoai, $student_id, $class, $faculty, $gender, $username);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return "Lỗi cập nhật hồ sơ!";
    }
}

// ================== CÁC HÀM KHÁC ==================
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}

function getUserInfo($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 1 ? $result->fetch_assoc() : null;
}
?>