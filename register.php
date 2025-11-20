<?php 
$page_type = 'login';
require('widget/top.php'); 
?>
    <div class="container">
       
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
                eyeIcon.src = 'image/eye.svg.png';
            } else {
                passwordInput.type = 'password';
                eyeIcon.src = 'image/eye-off.svg.png';
            }
        }
        
        
        document.querySelector('.register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            
            if (!username || !password || !confirmPassword) {
                alert('Vui lòng điền đầy đủ thông tin!');
                return;
            }
            
           
            const usernameRegex = /^[a-zA-Z0-9]+$/;
            if (!usernameRegex.test(username)) {
                alert('Tên đăng nhập chỉ được dùng chữ và số!');
                return;
            }
            
            
            if (password.length < 8) {
                alert('Mật khẩu phải có ít nhất 8 ký tự!');
                return;
            }
            
            
            if (password !== confirmPassword) {
                alert('Mật khẩu nhập lại không khớp!');
                return;
            }
            
            
            alert('Tạo tài khoản thành công!');
            
            window.location.href = 'login.php';
        });

        
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