<?php
require('assets/database/connect.php');

echo "<h2>Cập nhật Database</h2>";

// Kiểm tra xem cột avatar đã tồn tại chưa
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'avatar'");

if ($check->num_rows == 0) {
    // Thêm cột avatar
    $sql = "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER email";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ Đã thêm cột 'avatar' vào bảng users thành công!</p>";
    } else {
        echo "<p style='color: red;'>❌ Lỗi: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ️ Cột 'avatar' đã tồn tại trong bảng users.</p>";
}

echo "<br><a href='profile.php'>← Quay lại Profile</a>";

$conn->close();
?>
