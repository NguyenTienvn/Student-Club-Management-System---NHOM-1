<?php 
session_start();
$page_css = "register.css";
require('assets/database/connect.php');
require('xulylogin.php');
$page_type = 'login';
require('site.php'); 
load_top();

// Chỉ xóa session tạm khi người dùng vào lại trang đăng ký mà không qua form POST
if (isset($_SESSION['temp_username']) && basename($_SERVER['PHP_SELF']) == 'register.php' && empty($_POST)) {
    unset($_SESSION['temp_username']);
    unset($_SESSION['temp_password']);
    unset($_SESSION['registration_time']);
}

$success_message = '';
$error_message = '';

// Xử lý đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validate
    $usernameRegex = '/^[a-zA-Z0-9]+$/';
    if (!preg_match($usernameRegex, $username)) {
        $error_message = 'Tên đăng nhập chỉ được dùng chữ và số!';
    } elseif (strlen($password) < 8) {
        $error_message = 'Mật khẩu phải có ít nhất 8 ký tự!';
    } elseif ($password !== $confirmPassword) {
        $error_message = 'Mật khẩu nhập lại không khớp!';
    } else {
        // Đăng ký user
        $result = registerUser($username, $password);

        if ($result === true) {
            $_SESSION['temp_username'] = $username;
            $_SESSION['temp_password'] = $password;
            $_SESSION['registration_time'] = time();
            
            // HIỆN THÔNG BÁO + TỰ ĐỘNG CHUYỂN SAU 2 GIÂY
            $success_message = 'Đăng ký thành công! Đang chuyển đến hoàn thiện hồ sơ...';
        } else {
            $error_message = $result;
        }
    }
}
?>

<div class="container">
    <button class="back-btn" onclick="window.location.href = 'trangchu.php'">Quay lại</button>
        
    <div class="register-box">
        <h1>Tạo tài khoản LeaderClub</h1>
            
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
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
                let seconds = 2;
                const countdown = document.getElementById('countdown');
                const timer = setInterval(() => {
                    seconds--;
                    countdown.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(timer);
                        window.location.href = "complete_profile.php";
                    }
                }, 1000);
            </script>
        <?php endif; ?>

        <?php if (!$success_message): ?>
            <form class="register-form" method="POST" action="">
                <div class="input-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    <div class="input-note">Chỉ dùng chữ và số</div>
                </div>
                
                <div class="input-group">
                    <label for="password">Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        <img src="image/eye-off.svg.png" class="eye-icon" id="eyeIcon1" onclick="togglePassword('password', 'eyeIcon1')">
                    </div>
                    <div class="input-note">Ít nhất 8 ký tự</div>
                </div>
                
                <div class="input-group">
                    <label for="confirmPassword">Nhập lại mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Nhập lại mật khẩu" required>
                        <img src="image/eye-off.svg.png" class="eye-icon" id="eyeIcon2" onclick="togglePassword('confirmPassword', 'eyeIcon2')">
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <button type="submit" class="submit-btn">Tạo tài khoản</button>
                
                <div class="login-link">
                    Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a>
                </div>
            </form>
        <?php endif;  ?>
    </div>
</div>

<script>
    function togglePassword(inputId, eyeIconId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeIconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.src = 'image/eye.svg.png';
        } else {
            passwordInput.type = 'password';
            eyeIcon.src = 'image/eye-off.svg.png';
        }
    }
        
    document.addEventListener('DOMContentLoaded', function() {
        const eyeIcons = document.querySelectorAll('.eye-icon');
        
        eyeIcons.forEach(icon => {
            const testImage = new Image();
            testImage.onerror = function() {
                const originalSrc = icon.src;
                if (!originalSrc.includes('image/')) {
                    if (originalSrc.includes('eye-closed.png')) {
                        icon.src = 'image/eye-off.svg.png';
                    } else if (originalSrc.includes('eye.svg.png')) {
                        icon.src = 'image/eye.svg.png';
                    }
                }
            };
            testImage.src = icon.src;
        });
    });
</script>

</body>
</html>