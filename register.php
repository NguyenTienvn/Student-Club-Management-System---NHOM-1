<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo tài khoản - LeaderClub</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="container">
        <!-- Nút quay lại -->
        <button class="back-btn" onclick="window.location.href = 'trangchu.php'">← Quay lại</button>
        
        <div class="register-box">
            <h1>Tạo tài khoản LeaderClub</h1>
            
            <form class="register-form">
                <div class="input-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" placeholder="Nhập tên đăng nhập" required>
                    <div class="input-note">Chỉ dùng chữ và số</div>
                </div>
                
                <div class="input-group">
                    <label for="password">Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" placeholder="Nhập mật khẩu" required>
                        <img src="eye-closed.png" class="eye-icon" id="eyeIcon1" onclick="togglePassword('password', 'eyeIcon1')">
                    </div>
                    <div class="input-note">Ít nhất 8 ký tự</div>
                </div>
                
                <div class="input-group">
                    <label for="confirmPassword">Nhập lại mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirmPassword" placeholder="Nhập lại mật khẩu" required>
                        <img src="eye-closed.png" class="eye-icon" id="eyeIcon2" onclick="togglePassword('confirmPassword', 'eyeIcon2')">
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <button type="submit" class="submit-btn">Tạo tài khoản</button>
                
                <div class="login-link">
                    Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, eyeIconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeIconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.src = 'eye.svg.png';
            } else {
                passwordInput.type = 'password';
                eyeIcon.src = 'eye-off.svg.png';
            }
        }
        
        // Xử lý form đăng ký
        document.querySelector('.register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Kiểm tra validation
            if (!username || !password || !confirmPassword) {
                alert('Vui lòng điền đầy đủ thông tin!');
                return;
            }
            
            // Kiểm tra username chỉ chứa chữ và số
            const usernameRegex = /^[a-zA-Z0-9]+$/;
            if (!usernameRegex.test(username)) {
                alert('Tên đăng nhập chỉ được dùng chữ và số!');
                return;
            }
            
            // Kiểm tra độ dài mật khẩu
            if (password.length < 8) {
                alert('Mật khẩu phải có ít nhất 8 ký tự!');
                return;
            }
            
            // Kiểm tra mật khẩu khớp
            if (password !== confirmPassword) {
                alert('Mật khẩu nhập lại không khớp!');
                return;
            }
            
            // Nếu tất cả validation passed
            alert('Tạo tài khoản thành công!');
            // Chuyển hướng đến trang đăng nhập
            window.location.href = 'login.php';
        });
    </script>
</body>
</html>