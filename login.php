<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập LeaderClub</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <button class="back-btn" onclick="window.location.href = 'trangchu.php'">← Quay lại</button>
        
        <div class="login-box">
            <h1>LeaderClub</h1>
            <p class="subtitle">Đăng nhập vào tài khoản của bạn</p>
            
            <form class="login-form">
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" placeholder="Nhập tên đăng nhập" required>
                </div>
                
                <div class="input-group">
                    <label>Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" placeholder="Nhập mật khẩu" required>
                        <img src="eye-closed.png" class="eye-icon" id="eyeIcon" onclick="togglePassword()">
                    </div>
                </div>
                
                <div class="options">
                    <label class="remember">
                        <input type="checkbox"> Ghi nhớ tôi
                    </label>
                    <a href="#" class="forgot-link">Quên mật khẩu?</a>
                </div>
                
                <button type="submit" class="login-btn">Đăng nhập</button>
                
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
                eyeIcon.src = 'eye.svg.png';
            } else {
                passwordInput.type = 'password';
                eyeIcon.src = 'eye-off.svg.png';
            }
        }
        
        // Xử lý form đăng nhập
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const username = this.querySelector('input[type="text"]').value;
            const password = document.getElementById('password').value;
            
            if (username && password) {
                alert('Đăng nhập thành công!');
            } else {
                alert('Vui lòng điền đầy đủ thông tin!');
            }
        });
    </script>
</body>
</html>