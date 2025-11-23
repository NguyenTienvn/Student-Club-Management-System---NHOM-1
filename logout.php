<?php
session_start();

// Xóa tất cả session
session_unset();
session_destroy();

// Xóa cookie remember me
setcookie('remember_user', '', time() - 3600, "/");

// Chuyển hướng về trang chủ
header("Location: trangchu.php");
exit();
?>