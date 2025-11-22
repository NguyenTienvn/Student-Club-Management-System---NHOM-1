<?php
session_start();

// Xóa tất cả session
session_unset();
session_destroy();

// Xóa cookie remember nếu có
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Chuyển về trang chủ
header("Location: trangchu.php");
exit();
?>
