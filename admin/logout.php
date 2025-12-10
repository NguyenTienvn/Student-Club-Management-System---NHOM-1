<?php
session_start();

// Xóa tất cả session admin
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_avatar']);

// Xóa session user thường (nếu có)
unset($_SESSION['logged_in']);
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['ho_ten']);
unset($_SESSION['email']);
unset($_SESSION['vai_tro']);
unset($_SESSION['so_dien_thoai']);
unset($_SESSION['avatar']);

// Destroy session
session_unset();
session_destroy();

// Redirect về trang đăng nhập chung
header('Location: ../login.php');
exit;
?>

