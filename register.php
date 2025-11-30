<?php
session_start();
$page_css = "register.css";
require('assets/database/connect.php');
require('xulylogin.php');
$page_type = 'login';
require('site.php');
load_top();

// Xóa session tạm
if (isset($_SESSION['temp_username']) && basename($_SERVER['PHP_SELF']) == 'register.php' && empty($_POST)) {
    unset($_SESSION['temp_username'], $_SESSION['temp_password'], $_SESSION['registration_time']);
}

$success_message = '';
$errors = ['username' => '', 'password' => '', 'confirm' => '', 'general' => ''];

// Xử lý đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // === KIỂM TRA TÊN ĐĂNG NHẬP ===
    if ($username === '') {
        $errors['username'] = 'Vui lòng nhập tên đăng nhập!';
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $errors['username'] = 'Tên đăng nhập chỉ được dùng chữ cái và số!';
    }

    // === KIỂM TRA MẬT KHẨU ===
    if ($password === '') {
        $errors['password'] = 'Vui lòng nhập mật khẩu!';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Mật khẩu phải có ít nhất 8 ký tự!';
    }

    // === KIỂM TRA NHẬP LẠI MẬT KHẨU ===
    if ($confirmPassword === '') {
        $errors['confirm'] = 'Vui lòng nhập lại mật khẩu!';
    } elseif ($password !== $confirmPassword) {
        $errors['confirm'] = 'Mật khẩu nhập lại không khớp!';
    }

    // Nếu không có lỗi ở các field → gọi hàm đăng ký
    if ($errors['username'] === '' && $errors['password'] === '' && $errors['confirm'] === '') {
        $result = registerUser($username, $password);

        if ($result === true) {
            // Đăng ký thành công
            $_SESSION['temp_username'] = $username;
            $_SESSION['temp_password'] = $password;
            $_SESSION['registration_time'] = time();
            
            // HIỆN THÔNG BÁO + TỰ ĐỘNG CHUYỂN SAU 2 GIÂY
            $success_message = 'Đăng ký thành công! Đang chuyển đến hoàn thiện hồ sơ...';
        
        } else {
            // Lỗi từ hàm registerUser (tên trùng, lỗi DB,...)
            $errors['general'] = $result;
        }
    }
}
?>

<div class="container">
    <button class="back-btn" onclick="window.location.href='trangchu.php'">Quay lại</button>

    <div class="register-box">
        <h1>Tạo tài khoản LeaderClub</h1>

        <?php if (!empty($errors['general'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($errors['general']); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success-message">
                <strong><?php echo $success_message; ?></strong>
                <div style="margin-top:15px; font-size:14px; color:#155724;">
                    Đang chuyển tự động trong <span id="countdown">2</span> giây...
                </div>
            </div>

            <!-- TỰ ĐỘNG CHUYỂN SAU 2 GIÂY -->
            <script>
                let s = 2;
                const cd = document.getElementById('countdown');
                const t = setInterval(() => { s--; cd.textContent = s; if (s <= 0) { clearInterval(t); location.href = "complete_profile.php"; } }, 1000);
            </script>
        <?php endif; ?>

        <?php if (!$success_message): ?>
            <form class="register-form" method="POST">
                <!-- TÊN ĐĂNG NHẬP -->
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="username" placeholder="Nhập tên đăng nhập"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <div class="input-note">Chỉ dùng chữ và số</div>
                    <?php if (!empty($errors['username'])): ?>
                        <div class="field-error"><?php echo $errors['username']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- MẬT KHẨU -->
                <div class="input-group">
                    <label>Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" placeholder="Nhập mật khẩu" autocomplete="new-password">
                        <i class="fa-solid fa-eye-slash eye-icon" onclick="togglePassword(this)"></i>
                    </div>
                    <div class="input-note">Ít nhất 8 ký tự</div>
                    <?php if (!empty($errors['password'])): ?>
                        <div class="field-error"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- NHẬP LẠI MẬT KHẨU -->
                <div class="input-group">
                    <label>Nhập lại mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirmPassword" placeholder="Nhập lại mật khẩu" autocomplete="new-password">
                        <i class="fa-solid fa-eye-slash eye-icon" onclick="togglePassword(this)"></i>
                    </div>
                    <?php if (!empty($errors['confirm'])): ?>
                        <div class="field-error"><?php echo $errors['confirm']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="divider"></div>
                
                <button type="submit" class="submit-btn">Tạo tài khoản</button>
                <div class="login-link">Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a></div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    function togglePassword(icon) {
        const input = icon.previousElementSibling;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.password-wrapper input').forEach(input => {
            const icon = input.nextElementSibling;
            input.addEventListener('input', () => {
                icon.style.opacity = input.value ? '0.7' : '0';
            });
            icon.style.opacity = '0';
        });
    });
</script>

</body>
</html>