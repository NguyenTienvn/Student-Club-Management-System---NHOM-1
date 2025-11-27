-- Tạo bảng club_pages để lưu thông tin trang đại diện
CREATE TABLE IF NOT EXISTS `club_pages` (
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
    UNIQUE KEY `unique_club` (`club_id`),
    FOREIGN KEY (`club_id`) REFERENCES `clubs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
