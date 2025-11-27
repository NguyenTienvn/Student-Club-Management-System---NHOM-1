<?php
// xulylogin.php - ĐÃ SỬA ĐÚNG CÚ PHÁP MYSQLI 100%
require_once('assets/database/connect.php'); // $conn đã có sẵn

// ================== ĐĂNG NHẬP ==================
function loginUser($username, $password, $remember = false) {
    global $conn;
    
    // Kiểm tra admin trước
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = 'admin';
        return 'admin'; // Trả về 'admin' để redirect đến admin panel
    }
    
    $stmt = $conn->prepare("SELECT id, ho_ten, username, email, password, vai_tro, so_dien_thoai FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra mật khẩu: hỗ trợ cả plain text và hash
        $password_match = false;
        
        // Kiểm tra nếu là password hash (bắt đầu với $2y$)
        if (substr($user['password'], 0, 4) === '$2y$') {
            $password_match = password_verify($password, $user['password']);
        } else {
            // Plain text password
            $password_match = ($password === $user['password']);
        }
        
        if ($password_match) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['ho_ten']    = $user['ho_ten'] ?? '';
            $_SESSION['email']     = $user['email'] ?? '';
            $_SESSION['vai_tro']   = $user['vai_tro'] ?? 'thanh_vien';
            $_SESSION['so_dien_thoai'] = $user['so_dien_thoai'] ?? '';
            
            // Xử lý "Ghi nhớ tôi"
            if ($remember) {
                // Kiểm tra xem cột remember_token có tồn tại không
                $check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
                if ($check_column && $check_column->num_rows > 0) {
                    $token = bin2hex(random_bytes(32)); // Tạo token ngẫu nhiên
                    $expiry = time() + (30 * 24 * 60 * 60); // 30 ngày
                    
                    // Lưu token vào database
                    $stmt = $conn->prepare("UPDATE users SET remember_token = ?, remember_token_expiry = ? WHERE id = ?");
                    $expiry_date = date('Y-m-d H:i:s', $expiry);
                    $stmt->bind_param("ssi", $token, $expiry_date, $user['id']);
                    $stmt->execute();
                    
                    // Lưu token vào cookie
                    setcookie('remember_token', $token, $expiry, "/", "", false, true); // httponly = true
                    setcookie('remember_user', $user['id'], $expiry, "/", "", false, true);
                }
            }
            
            return true;
        }
    }
    return false;
}

// ================== TỰ ĐỘNG ĐĂNG NHẬP TỪ COOKIE ==================
function autoLoginFromCookie() {
    global $conn;
    
    // Kiểm tra xem cột remember_token có tồn tại không
    $check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
    if (!$check_column || $check_column->num_rows == 0) {
        return false; // Cột chưa tồn tại, bỏ qua chức năng này
    }
    
    if (isset($_COOKIE['remember_token']) && isset($_COOKIE['remember_user'])) {
        $token = $_COOKIE['remember_token'];
        $user_id = $_COOKIE['remember_user'];
        
        $stmt = $conn->prepare("SELECT id, ho_ten, username, email, vai_tro, so_dien_thoai, remember_token_expiry FROM users WHERE id = ? AND remember_token = ?");
        $stmt->bind_param("is", $user_id, $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Kiểm tra token còn hạn không
            if (strtotime($user['remember_token_expiry']) > time()) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['ho_ten']    = $user['ho_ten'] ?? '';
                $_SESSION['email']     = $user['email'] ?? '';
                $_SESSION['vai_tro']   = $user['vai_tro'] ?? 'thanh_vien';
                $_SESSION['so_dien_thoai'] = $user['so_dien_thoai'] ?? '';
                return true;
            } else {
                // Token hết hạn, xóa cookie
                clearRememberCookie();
            }
        }
    }
    return false;
}

// ================== XÓA COOKIE GHI NHỚ ==================
function clearRememberCookie() {
    setcookie('remember_token', '', time() - 3600, "/");
    setcookie('remember_user', '', time() - 3600, "/");
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
    global $conn;
    
    // Xóa remember token trong database nếu có
    if (isset($_SESSION['user_id'])) {
        $check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
        if ($check_column && $check_column->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE users SET remember_token = NULL, remember_token_expiry = NULL WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
        }
    }
    
    // Xóa cookie
    clearRememberCookie();
    
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