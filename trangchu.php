<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeaderClub</title>
    <link rel="stylesheet" href="assets/css/trangchu.css">
</head>
<body>

    <!-- HEADER -->
    <header class="header">
        <div class="logo">LeaderClub</div>
        <nav>
            <a href="#">Trang chủ</a>
            <a href="#">CLB</a>
            <a href="#">Sự kiện</a>
            <a href="#">Liên hệ</a>
            <button class="btn" onclick="window.location.href = 'register.php'">Đăng Ký</button>
            <button class="btn outline" onclick="window.location.href = 'login.php'">Đăng Nhập</button>
        </nav>
    </header>

    <!-- HERO FULL SCREEN -->
    <section class="hero">
        <div class="hero-content">
            <h1>Nền tảng quản lý Câu Lạc Bộ toàn diện</h1>
            <p>Khánh phần mềm giúp các CLB học đường dễ dàng quảng bá hình ảnh, quản lý thành viên, vận hành sự kiện.</p>
            <button class="btn black">Bắt đầu ngay</button>
        </div>

        <div class="hero-img">
            <div class="img-box"></div>
            <div class="img-box"></div>
            <div class="img-box"></div>
            <div class="img-box"></div>
        </div>
    </section>

    <!-- 3 ICON TÍNH NĂNG -->
    <section class="features">
        <div class="item">
            <div class="icon yellow"></div>
            <h3>Quảng bá hình ảnh</h3>
        </div>

        <div class="item">
            <div class="icon red"></div>
            <h3>Quản lý nội bộ</h3>
        </div>

        <div class="item">
            <div class="icon green"></div>
            <h3>Vận hành sự kiện</h3>
        </div>
    </section>

    <!-- BANNER LỚN -->
    <section class="banner">
        <div class="banner-left">
            <h2>Trang đại diện riêng</h2>
            <p>Xây dựng một trang dành riêng cho CLB của bạn. Dễ dàng đăng bài, chia sẻ tài liệu, thông báo và sự kiện.</p>
        </div>

        <div class="banner-img"></div>
    </section>

    <!-- LOGO DANH SÁCH CLB -->
    <section class="logos">
        <div class="logo-item"></div>
        <div class="logo-item"></div>
        <div class="logo-item"></div>
        <div class="logo-item"></div>
        <div class="logo-item"></div>
        <div class="logo-item"></div>
        <div class="logo-item"></div>
    </section>

    <!-- QUẢN LÝ THÀNH VIÊN -->
    <section class="members">

        <div class="members-img"></div>

        <div class="members-text">
            <h2>Quản lý thành viên</h2>
            <p>Dễ dàng theo dõi thông tin thành viên, phân chia phòng ban, thêm mới, chỉnh sửa và thống kê hoạt động.</p>
        </div>
    </section>

</body>
</html>
