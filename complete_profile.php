<?php 
session_start();
require('assets/database/connect.php');
require('xulylogin.php');
$page_type = 'login';
require('site.php'); 
load_top();

if (!isset($_SESSION['temp_username'])) {
    header("Location: register.php"); exit();
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $so_dien_thoai = $_POST['so_dien_thoai'] ?? '';
    $student_id = $_POST['student_id'] ?? '';
    $class = $_POST['class'] ?? '';
    $faculty = $_POST['faculty'] ?? '';
    $gender = $_POST['gender'] ?? 'khac';

    if (empty($ho_ten)) {
        $error_ho_ten = "Họ tên không được để trống!";
    }
    if (empty($email)) {
        $error_email = "Email không được để trống!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_email = "Email không hợp lệ!";
    }

    if (empty($error_ho_ten) && empty($error_email)) {
        $result = completeUserProfile(
            $_SESSION['temp_username'], $ho_ten, $email, $so_dien_thoai,
            $student_id, $class, $faculty, $gender
        );

        if ($result === true) {
            unset($_SESSION['temp_username'], $_SESSION['temp_password']);
            $success = "Hoàn thiện hồ sơ thành công! Chào mừng bạn đến với LeaderClub";
            echo '<script>setTimeout(() => { window.location.href = "trangchu.php"; }, 2500);</script>';
        } else {
            $error_ho_ten = $result;
        }
}
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoàn thiện hồ sơ - LeaderClub</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #F6EDE2, #fbe2b7); margin:0; padding:50px 20px; min-height:100vh; }
        .card { max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .header { background: linear-gradient(135deg, #fbd8adff, #ffc35cff); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { opacity: 0.9; margin-top: 10px; }
        .form-body { padding: 40px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 8px; color: #333; }
        input, select { padding: 14px; border: 1px solid #ddd; border-radius: 10px; font-size: 16px; }
        .gender-group { display: flex; gap: 25px; margin-top: 10px; }
        .gender-option { display: flex; align-items: center; gap: 8px; font-size: 15px; }
        .btn { background: linear-gradient(to right, #f8dcb9, #f8bd55); color: white; padding: 16px; border: none; border-radius: 10px; font-size: 18px; cursor: pointer; width: 100%; margin-top: 20px; }
        .btn:hover { background:linear-gradient(to right, #f8dcb9, #f8bd55); }
        .error-text { background: #feb2b2; color: #9b2c2c; padding: 15px; border-radius: 8px; margin-top: 10px;; margin-bottom: 20px; }
        .success { background: #9ae6b4; color: #22543d; padding: 20px; border-radius: 10px; text-align: center; font-size: 18px; font-weight: bold; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>LeaderClub</h1>
            <p>Vui lòng hoàn thiện hồ sơ để bắt đầu trải nghiệm</p>
        </div>
        
        <div class="form-body">
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?><br><small>Đang chuyển về trang chủ...</small></div>
            <?php else: ?>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên *</label>
                            <input type="text" name="ho_ten" value="<?php echo $_POST['ho_ten'] ?? ''; ?>">
                            <?php if (!empty($error_ho_ten)): ?>
                                <span class="error-text"><?php echo $error_ho_ten; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
                            <?php if (!empty($error_email)): ?>
                                <span class="error-text"><?php echo $error_email; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Mã sinh viên</label>
                            <input type="text" name="student_id" value="<?php echo $_POST['student_id'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Lớp</label>
                            <input type="text" name="class" value="<?php echo $_POST['class'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Khoa</label>
                            <input type="text" name="faculty" value="<?php echo $_POST['faculty'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="so_dien_thoai" value="<?php echo $_POST['so_dien_thoai'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Giới tính</label>
                        <div class="gender-group">
                            <div class="gender-option">
                                <input type="radio" name="gender" value="nam" id="nam" checked>
                                <label for="nam">Nam</label>
                            </div>
                            <div class="gender-option">
                                <input type="radio" name="gender" value="nu" id="nu">
                                <label for="nu">Nữ</label>
                            </div>
                            <div class="gender-option">
                                <input type="radio" name="gender" value="khac" id="khac">
                                <label for="khac">Khác</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn">Hoàn tất đăng ký</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>