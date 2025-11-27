<?php
// File này chỉ chạy 1 lần để tạo bảng club_pages
require 'assets/database/connect.php';

// Tạo bảng club_pages
$sql = "CREATE TABLE IF NOT EXISTS `club_pages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `club_id` INT NOT NULL,
    `slogan` VARCHAR(255) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `banner_url` VARCHAR(255) DEFAULT NULL,
    `logo_url` VARCHAR(255) DEFAULT NULL,
    `primary_color` VARCHAR(20) DEFAULT '#667eea',
    `facebook` VARCHAR(255) DEFAULT NULL,
    `instagram` VARCHAR(255) DEFAULT NULL,
    `twitter` VARCHAR(255) DEFAULT NULL,
    `website` VARCHAR(255) DEFAULT NULL,
    `is_public` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_club` (`club_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql)) {
    echo "✅ Đã tạo bảng club_pages thành công!<br><br>";
    echo "✅ Setup hoàn tất! Bạn có thể xóa file này.<br><br>";
    echo "<a href='Dashboard.php' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 8px;'>← Quay lại Dashboard</a>";
} else {
    echo "❌ Lỗi: " . $conn->error;
}

$conn->close();
?>
