<?php
$conn = new mysqli('localhost', 'root', '', 'leaderclub');

if ($conn->connect_error) { //một thuộc tính cho biết lỗi nếu kết nối thất bại.
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>