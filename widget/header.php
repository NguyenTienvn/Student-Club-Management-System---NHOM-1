<header class="header">
    
    <!-- LEFT -->
    <div class="left">
        <a href="trangchu.php" class="logo">
            <img src="assets/img/logoleaderclub.png" alt="LeaderClub Logo" class="logo-img">
            <span>LeaderClub</span>
        </a>
    </div>

    <!-- CENTER MENU -->
    <nav class="center-menu">
        <a href="trangchu.php">Trang chủ</a>

        <div class="nav-item">
            <span class="nav-link">Câu lạc bộ ▾</span>

            <div class="dropdown-menu">
                <a href="DanhsachCLB.php" class="dropdown-item">
                    <span class="icon orange">📘</span>
                    <div>
                        <h4>Danh sách CLB</h4>
                        <p>Khám phá các Câu Lạc Bộ phù hợp với bạn</p>
                    </div>
                </a>

                <a href="QuanLyCLB.php" class="dropdown-item">
                    <span class="icon blue">⚙️</span>
                    <div>
                        <h4>Quản lý CLB</h4>
                        <p>Tạo & Quản lý Câu Lạc Bộ của riêng bạn</p>
                    </div>
                </a>
            </div>
        </div>

        <a href="#">Sự kiện</a>
        <a href="#">Liên hệ</a>
    </nav>

    <!-- RIGHT BUTTONS -->
    <div class="right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- User đã đăng nhập -->
            <?php
            // Lấy avatar từ database
            require_once('assets/database/connect.php');
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT avatar FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            $avatar = $user_data['avatar'] ?? '';
            ?>
            <div class="nav-item user-profile">
                <div class="user-avatar">
                    <?php if (!empty($avatar) && file_exists($avatar)): ?>
                        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar">
                    <?php else: ?>
                        <span><?php echo strtoupper(substr($_SESSION['ho_ten'] ?? 'U', 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <span class="user-name"><?php echo $_SESSION['ho_ten'] ?? 'User'; ?></span>
                
                <div class="dropdown-menu profile-dropdown">
                    <a href="profile.php" class="dropdown-item">
                        <span class="icon">👤</span>
                        <div>
                            <h4>Hồ sơ của tôi</h4>
                            <p>Xem và chỉnh sửa thông tin</p>
                        </div>
                    </a>
                    
                    <a href="my-clubs.php" class="dropdown-item">
                        <span class="icon">🏆</span>
                        <div>
                            <h4>CLB của tôi</h4>
                            <p>Quản lý các CLB đã tham gia</p>
                        </div>
                    </a>
                    
                    <a href="settings.php" class="dropdown-item">
                        <span class="icon">⚙️</span>
                        <div>
                            <h4>Cài đặt</h4>
                            <p>Tùy chỉnh tài khoản</p>
                        </div>
                    </a>
                    
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid #eee;">
                    
                    <a href="logout.php" class="dropdown-item">
                        <span class="icon">🚪</span>
                        <div>
                            <h4>Đăng xuất</h4>
                            <p>Thoát khỏi tài khoản</p>
                        </div>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- User chưa đăng nhập -->
            <button class="btn" onclick="location.href='register.php'">Đăng Ký</button>
            <button class="btn outline" onclick="location.href='login.php'">Đăng Nhập</button>
        <?php endif; ?>
    </div>
</header>



