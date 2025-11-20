<?php 
$page_type = 'login';
require('widget/top.php'); 
?>
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
                        <!-- SỬA ĐƯỜNG DẪN ẢNH Ở ĐÂY -->
                        <img src="image/eye-off.svg.png" class="eye-icon" id="eyeIcon" onclick="togglePassword()" alt="Hiển thị mật khẩu">
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
                eyeIcon.src = 'image/eye.svg.png'; // SỬA ĐƯỜNG DẪN Ở ĐÂY
            } else {
                passwordInput.type = 'password';
                eyeIcon.src = 'image/eye-off.svg.png'; // SỬA ĐƯỜNG DẪN Ở ĐÂY
            }
        }
        
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

        // THÊM: Kiểm tra xem ảnh có tồn tại không
        window.addEventListener('load', function() {
            const eyeIcon = document.getElementById('eyeIcon');
            const img = new Image();
            img.onload = function() {
                console.log('Ảnh icon mắt tải thành công');
            };
            img.onerror = function() {
                console.error('Không thể tải ảnh icon mắt. Đường dẫn: ' + eyeIcon.src);
                // Có thể thay thế bằng icon font-awesome nếu ảnh không tồn tại
            };
            img.src = eyeIcon.src;
        });
    </script>
</body>
</html>