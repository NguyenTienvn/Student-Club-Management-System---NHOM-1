<?php
$page_css = "trangchu.css";
require 'site.php';
load_top();
load_header();
?>

<section class="hero-qnu">
    <div class="hero-left">
        <h1>Hệ thống quản lý Câu Lạc Bộ QNU</h1>
        <p>Nền tảng chính thức dành cho các CLB đã được Trường Đại học Quy Nhơn phê duyệt. 
        Quản lý thành viên – sự kiện – tài liệu một cách khoa học và hiệu quả.</p>

        <button class="btn primary" onclick="location.href='DanhsachCLB.php'">
            Xem danh sách CLB
        </button>
    </div>

    <div class="hero-right">
        <img src="assets/img/svqnu.png" class="hero-illustration">
    </div>
</section>



<!-- TÍNH NĂNG CHÍNH -->
<section class="feature-qnu">
    <div class="feature-item">
        <i class="ri-group-fill feature-icon"></i>
        <h3>Quản lý thành viên</h3>
        <p>Theo dõi hồ sơ, phòng ban, phân quyền và hoạt động của các thành viên.</p>
    </div>

    <div class="feature-item">
        <i class="ri-calendar-event-fill feature-icon"></i>
        <h3>Quản lý sự kiện</h3>
        <p>Tạo – điểm danh – thống kê sự kiện nhanh chóng, chính xác.</p>
    </div>

    <div class="feature-item">
        <i class="ri-folder-2-fill feature-icon"></i>
        <h3>Tài liệu & truyền thông</h3>
        <p>Lưu trữ tài liệu nội bộ và đăng tin tức cho CLB.</p>
    </div>
</section>


<!-- DANH SÁCH CLB NỔI BẬT -->
<section class="highlight-club">
    <h2>CLB nổi bật tại QNU</h2>
    <p class="section-subtitle">Khám phá các câu lạc bộ năng động và sáng tạo nhất</p>

    <div class="club-grid">
        <div class="club-card-small club-bg-1">
            <h4>Đội Thanh Niên Xung Kích QNU</h4>
            <p class="member-count">👥 140 thành viên</p>
            <p class="club-category">Sự kiện</p>
        </div>

        <div class="club-card-small club-bg-2">
            <h4>Đội Thanh Niên Tình Nguyện</h4>
            <p class="member-count">👥 55 thành viên</p>
            <p class="club-category">Tình nguyện</p>
        </div>

        <div class="club-card-small club-bg-3">
            <h4>CLB Kết Nối Trẻ</h4>
            <p class="member-count">👥 120 thành viên</p>
            <p class="club-category">Truyền thông</p>
        </div>

        <div class="club-card-small club-bg-4">
            <h4>CLB Tiếng Anh LET's Go</h4>
            <p class="member-count">👥 95 thành viên</p>
            <p class="club-category">Ngôn ngữ</p>
        </div>
    </div>

    <button class="btn outline view-all" onclick="location.href='DanhsachCLB.php'">
        Xem tất cả CLB →
    </button>
</section>


<!-- SỰ KIỆN SẮP DIỄN RA -->
<section class="upcoming-events">
    <h2>Sự kiện sắp diễn ra</h2>
    <p class="section-subtitle">Đừng bỏ lỡ những hoạt động thú vị</p>

    <div class="event-grid">
        <div class="event-card">
            <div class="event-date">
                <span class="date-day">25</span>
                <span class="date-month">Th11</span>
            </div>
            <div class="event-info">
                <h4>Ngày hội Câu Lạc Bộ 2024</h4>
                <p class="event-location">📍 Sân vận động QNU</p>
                <p class="event-time">⏰ 8:00 - 17:00</p>
                <span class="event-badge">Miễn phí</span>
            </div>
        </div>

        <div class="event-card">
            <div class="event-date">
                <span class="date-day">28</span>
                <span class="date-month">Th11</span>
            </div>
            <div class="event-info">
                <h4>Workshop: Kỹ năng lãnh đạo CLB</h4>
                <p class="event-location">📍 Hội trường A</p>
                <p class="event-time">⏰ 14:00 - 16:30</p>
                <span class="event-badge">Đăng ký</span>
            </div>
        </div>

        <div class="event-card">
            <div class="event-date">
                <span class="date-day">02</span>
                <span class="date-month">Th12</span>
            </div>
            <div class="event-info">
                <h4>Chương trình Tình nguyện mùa đông</h4>
                <p class="event-location">📍 Vùng cao Bình Định</p>
                <p class="event-time">⏰ 3 ngày 2 đêm</p>
                <span class="event-badge hot">Hot</span>
            </div>
        </div>
    </div>
</section>


<!-- TIN TỨC & HOẠT ĐỘNG -->
<section class="news-section">
    <h2>Tin tức & Hoạt động</h2>
    <p class="section-subtitle">Cập nhật những thông tin mới nhất từ các CLB</p>

    <div class="news-grid">
        <div class="news-card">
            <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?w=400" alt="News">
            <div class="news-content">
                <span class="news-category">Thành tích</span>
                <h4>CLB Bóng Rổ QNU giành giải Nhất giải Sinh viên toàn quốc</h4>
                <p class="news-date">20/11/2024</p>
            </div>
        </div>

        <div class="news-card">
            <img src="https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=400" alt="News">
            <div class="news-content">
                <span class="news-category">Tình nguyện</span>
                <h4>Chương trình "Mùa đông ấm" trao 500 phần quà cho học sinh vùng cao</h4>
                <p class="news-date">18/11/2024</p>
            </div>
        </div>

        <div class="news-card">
            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=400" alt="News">
            <div class="news-content">
                <span class="news-category">Sự kiện</span>
                <h4>Đêm nhạc "Sóng 20" thu hút hơn 2000 sinh viên tham dự</h4>
                <p class="news-date">15/11/2024</p>
            </div>
        </div>
    </div>
</section>


<!-- LỢI ÍCH KHI THAM GIA CLB -->
<section class="benefits-section">
    <h2>Tại sao nên tham gia CLB?</h2>
    <p class="section-subtitle">Những giá trị bạn nhận được khi là thành viên CLB</p>

    <div class="benefits-grid">
        <div class="benefit-item">
            <div class="benefit-icon">🎯</div>
            <h4>Phát triển kỹ năng</h4>
            <p>Rèn luyện kỹ năng mềm, làm việc nhóm, lãnh đạo và giao tiếp</p>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon">🤝</div>
            <h4>Mở rộng mạng lưới</h4>
            <p>Kết nối với sinh viên cùng đam mê, xây dựng mối quan hệ bền vững</p>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon">🏆</div>
            <h4>Cơ hội thăng tiến</h4>
            <p>Tham gia các vị trí quản lý, tổ chức sự kiện lớn</p>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon">📜</div>
            <h4>Chứng nhận & Điểm rèn luyện</h4>
            <p>Nhận chứng nhận hoạt động và cộng điểm rèn luyện</p>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon">🎨</div>
            <h4>Sáng tạo & Đam mê</h4>
            <p>Thỏa sức sáng tạo, theo đuổi đam mê của bản thân</p>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon">💼</div>
            <h4>Kinh nghiệm thực tế</h4>
            <p>Tích lũy kinh nghiệm quý báu cho CV và tương lai</p>
        </div>
    </div>
</section>


<!-- CALL TO ACTION -->
<section class="cta-section">
    <div class="cta-content">
        <h2>Sẵn sàng tham gia cộng đồng CLB QNU?</h2>
        <p>Khám phá hơn 80 câu lạc bộ và tìm nơi phù hợp với đam mê của bạn</p>
        <div class="cta-buttons">
            <button class="btn primary large" onclick="location.href='DanhsachCLB.php'">
                Khám phá CLB ngay
            </button>
            <button class="btn outline large">
                Tạo CLB mới
            </button>
        </div>
    </div>
</section>



<?php
load_footer();
?>