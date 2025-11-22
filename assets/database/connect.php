<?php
global $conn;

if (!isset($conn)) {
    $conn = new mysqli('localhost', 'root', '', 'leaderclub');
    
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
}
?>
