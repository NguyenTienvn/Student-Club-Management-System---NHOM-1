<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin'; ?> - LeaderClub Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🎯 LeaderClub</h2>
                <p>Admin Panel</p>
            </div>

            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="users.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">👥</span>
                    <span>Người dùng</span>
                </a>
                <a href="clubs.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'clubs.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">🎯</span>
                    <span>Câu lạc bộ</span>
                </a>
                <a href="contacts.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">✉️</span>
                    <span>Tin nhắn</span>
                </a>
                <a href="statistics.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'statistics.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">📊</span>
                    <span>Thống kê</span>
                </a>
                <a href="settings.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">⚙️</span>
                    <span>Cài đặt</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="nav-item logout">
                    <span class="nav-icon">🚪</span>
                    <span>Đăng xuất</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1><?php echo $page_title ?? 'Dashboard'; ?></h1>
                <div class="admin-info">
                    <span>👤 <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?></span>
                </div>
            </header>

            <div class="content-area">
