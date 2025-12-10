<?php 
session_start();
require('assets/database/connect.php');
require('xulylogin.php');

if (!isset($_SESSION['temp_username'])) {
    header("Location: register.php"); exit();
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once __DIR__ . '/includes/functions.php';
    $ho_ten = sanitize_input(trim($_POST['ho_ten'] ?? ''));
    $email = sanitize_input(trim($_POST['email'] ?? ''));
    $so_dien_thoai = sanitize_input($_POST['so_dien_thoai'] ?? '');
    $student_id = sanitize_input($_POST['student_id'] ?? '');
    $class = sanitize_input($_POST['class'] ?? '');
    $faculty = sanitize_input($_POST['faculty'] ?? '');
    $gender = sanitize_input($_POST['gender'] ?? 'khac');

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/complete_profile.css">
</head>
<body>

<div class="container">
    <div class="profile-box">
        <div class="header">
            <h1>LeaderClub</h1>
            <p>Vui lòng hoàn thiện hồ sơ để bắt đầu trải nghiệm</p>
        </div>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?><small>Đang chuyển về trang chủ...</small></div>
        <?php else: ?>
            <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên *</label>
                            <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($_POST['ho_ten'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (!empty($error_ho_ten)): ?>
                                <span class="error-text"><?php echo htmlspecialchars($error_ho_ten, ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (!empty($error_email)): ?>
                                <span class="error-text"><?php echo htmlspecialchars($error_email, ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Mã sinh viên</label>
                            <input type="text" name="student_id" value="<?php echo htmlspecialchars($_POST['student_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Lớp</label>
                            <input type="text" name="class" value="<?php echo htmlspecialchars($_POST['class'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Khoa</label>
                            <input type="text" name="faculty" value="<?php echo htmlspecialchars($_POST['faculty'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="so_dien_thoai" value="<?php echo htmlspecialchars($_POST['so_dien_thoai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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

                <button type="submit" class="submit-btn">Hoàn tất đăng ký</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>