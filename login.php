<?php 
session_start();
$page_css = "login.css";
include(__DIR__ . '/assets/database/dbleaderclub.php');  
// require('xulylogin.php');
$page_type = 'login';
require('site.php'); 
load_top();

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (loginUser($username, $password)) {
        // Đăng nhập thành công
        $_SESSION['user_id'] = getUserId($username); // hoặc hàm trả về ID user
        $_SESSION['username'] = $username;
        if (isset($_POST['remember'])) {
            setcookie('remember_user', $username, time() + (30 * 24 * 60 * 60), "/");
        }
        
        header("Location: trangchu.php");
        exit();
    } else {
        $error_message = "Tên đăng nhập hoặc mật khẩu không đúng!";
        
        // KIỂM TRA NẾU USERNAME KHÔNG TỒN TẠI
        if (!usernameExists($username)) {
            $error_message = "Tài khoản không tồn tại. Vui lòng đăng ký tài khoản mới!";
        }
    }
}
?>
    <div class="container">
        <button class="back-btn" onclick="window.location.href = 'trangchu.php'">← Quay lại</button>
        
        <div class="login-box">
            <h1>LeaderClub</h1>
            <p class="subtitle">Đăng nhập vào tài khoản của bạn</p>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form class="login-form" method="POST" action="">
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="username" placeholder="Nhập tên đăng nhập" 
                           value="<?php echo isset($_COOKIE['remember_user']) ? $_COOKIE['remember_user'] : ''; ?>" required>
                </div>
                
                <div class="input-group">
                    <label>Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        <img src="image/eye-off.svg.png" class="eye-icon" id="eyeIcon" onclick="togglePassword()" alt="Hiển thị mật khẩu">
                    </div>
                </div>
                
                <div class="options">
                    <label class="remember">
                        <input type="checkbox" name="remember" <?php echo isset($_COOKIE['remember_user']) ? 'checked' : ''; ?>> Ghi nhớ tôi
                    </label>
                    <a href="#" class="forgot-link">Quên mật khẩu?</a>
                </div>
                
                <button type="submit" name="login" class="login-btn">Đăng nhập</button>
                
                <div class="register">
                    Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.src = 'image/eye.svg.png'; 
            } else {
                passwordInput.type = 'password';
                eyeIcon.src = 'image/eye-off.svg.png';
            }
        }
    
    </script>
</body>
</html>