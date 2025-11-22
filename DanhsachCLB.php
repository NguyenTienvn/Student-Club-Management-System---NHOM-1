<?php
$page_css = "DanhsachCLB.css";
require 'site.php';
load_top();
load_header();
?>


<div class="container">

    <!-- TIÊU ĐỀ -->
    <h1 class="title">
        Khám phá <span class="highlight">535 Câu Lạc Bộ</span> phù hợp với bạn!
    </h1>

    <!-- DANH MỤC ICON -->
    <div class="categories">
        <div class="cat-item">
            <img src="https://cdn-icons-png.flaticon.com/512/2995/2995541.png">
            <p>Học thuật</p>
        </div>

        <div class="cat-item">
            <img src="https://cdn-icons-png.flaticon.com/512/4339/4339685.png">
            <p>Nghệ thuật</p>
        </div>

        <div class="cat-item">
            <img src="https://cdn-icons-png.flaticon.com/512/1048/1048945.png">
            <p>Truyền thông</p>
        </div>

        <div class="cat-item">
            <img src="https://cdn-icons-png.flaticon.com/512/2964/2964514.png">
            <p>Thể thao</p>
        </div>

        <div class="cat-item">
            <img src="https://cdn-icons-png.flaticon.com/512/1946/1946488.png">
            <p>Sở thích</p>
        </div>

        <div class="cat-item">
            <img src="https://cdn-icons-png.flaticon.com/512/2950/2950736.png">
            <p>Tình nguyện</p>
        </div>

        <div class="cat-item">
            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828884.png">
            <p>Ngôn ngữ</p>
        </div>

        <div class="cat-item">
            <img src="https://cdn-icons-png.flaticon.com/512/3063/3063187.png">
            <p>Điện tử</p>
        </div>
    </div>

    <!-- TÌM KIẾM + BỘ LỌC -->
    <div class="filters">
        <input type="text" id="searchInput" placeholder="Tìm kiếm Câu Lạc Bộ theo tên...">

        <select id="categoryFilter">
            <option value="">Tất cả danh mục</option>
            <option value="Nghệ thuật">Nghệ thuật</option>
            <option value="Truyền thông">Truyền thông</option>
            <option value="Thể thao">Thể thao</option>
            <option value="Ngôn ngữ">Ngôn ngữ</option>
            <option value="Sở thích">Sở thích</option>
            <option value="Điện tử">Điện tử</option>
            <option value="Tình nguyện">Tình nguyện</option>
            <option value="Học thuật">Học thuật</option>
            <option value="Âm nhạc">Âm nhạc</option>
            <option value="Khởi nghiệp">Khởi nghiệp</option>
            <option value="Văn học">Văn học</option>
            <option value="Công nghệ">Công nghệ</option>
            <option value="Môi trường">Môi trường</option>
        </select>

        <select id="sortFilter">
            <option value="">Sắp xếp theo</option>
            <option value="name-asc">Tên A-Z</option>
            <option value="name-desc">Tên Z-A</option>
            <option value="members-desc">Nhiều thành viên nhất</option>
            <option value="members-asc">Ít thành viên nhất</option>
        </select>

        <button class="btn-filter" id="resetBtn">Bỏ lọc</button>
    </div>

</div>

<div id="club-list">
    <!-- 5 CLB ĐẦU TIÊN - HIỂN THỊ MẶC ĐỊNH -->
    <div class="club-card">
        <div class="club-info">
            <span class="badge green">Nghệ thuật, Sáng tạo</span>
            <h2>
                <a href="CLBchitiet.php?id=1" class="club-title-link">CLB Nghệ thuật Sóng 20</a>
            </h2>
            <p>Câu lạc bộ Nghệ thuật Sóng 20 là CLB nghệ thuật trực thuộc Hội Sinh viên...</p>
            <p class="member-count">👥 60 thành viên</p>
            <a href="CLBchitiet.php?id=1" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/1Qd7UXJ.jpeg">
    </div>

    <div class="club-card">
        <div class="club-info">
            <span class="badge yellow">Truyền thông, Dịch vụ</span>
            <h2>
                <a href="CLBchitiet.php?id=2" class="club-title-link">Arise Team</a>
            </h2>
            <p>Nhóm truyền thông trẻ năng động thuộc Khoa Truyền thông – Sự kiện...</p>
            <p class="member-count">👥 55 thành viên</p>
            <a href="CLBchitiet.php?id=2" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/h1K5o8Z.jpeg">
    </div>

    <div class="club-card">
        <div class="club-info">
            <span class="badge green">Thể thao</span>
            <h2>
                <a href="CLBchitiet.php?id=3" class="club-title-link">CLB Bóng Rổ QNU</a>
            </h2>
            <p>CLB dành cho sinh viên yêu thích bóng rổ, luyện tập hàng tuần tại sân trường...</p>
            <p class="member-count">👥 120 thành viên</p>
            <a href="CLBchitiet.php?id=3" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/7xbXgze.jpeg">
    </div>

    <div class="club-card">
        <div class="club-info">
            <span class="badge yellow">Ngôn ngữ</span>
            <h2>
                <a href="CLBchitiet.php?id=4" class="club-title-link">CLB Tiếng Anh LET's Go</a>
            </h2>
            <p>CLB sinh hoạt hằng tuần với hoạt động Speaking, Debate, Workshop...</p>
            <p class="member-count">👥 95 thành viên</p>
            <a href="CLBchitiet.php?id=4" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/0gZrMQM.jpeg">
    </div>

    <div class="club-card">
        <div class="club-info">
            <span class="badge green">Sở thích</span>
            <h2>
                <a href="CLBchitiet.php?id=5" class="club-title-link">CLB Nhiếp ảnh QNU</a>
            </h2>
            <p>Nơi dành cho những bạn đam mê chụp ảnh, chỉnh sửa ảnh và sáng tạo nội dung...</p>
            <p class="member-count">👥 70 thành viên</p>
            <a href="CLBchitiet.php?id=5" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/0MOpGXJ.jpeg">
    </div>

    <!-- 5 CLB TIẾP THEO - ẨN MẶC ĐỊNH -->
    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge yellow">Điện tử</span>
            <h2>
                <a href="CLBchitiet.php?id=6" class="club-title-link">CLB Robot & IoT</a>
            </h2>
            <p>CLB chuyên về Arduino, ESP32, lập trình robot và các dự án IoT trong trường học...</p>
            <p class="member-count">👥 40 thành viên</p>
            <a href="CLBchitiet.php?id=6" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/hRTu3xV.jpeg">
    </div>

    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge green">Tình nguyện</span>
            <h2>
                <a href="CLBchitiet.php?id=7" class="club-title-link">CLB Thanh niên tình nguyện</a>
            </h2>
            <p>Tham gia các hoạt động thiện nguyện, Mùa hè xanh, tiếp sức mùa thi...</p>
            <p class="member-count">👥 150 thành viên</p>
            <a href="CLBchitiet.php?id=7" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/5mWv1kb.jpeg">
    </div>

    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge yellow">Học thuật</span>
            <h2>
                <a href="CLBchitiet.php?id=8" class="club-title-link">CLB Toán học QNU</a>
            </h2>
            <p>CLB dành cho những bạn yêu thích toán học, giải toán và tham gia các cuộc thi...</p>
            <p class="member-count">👥 85 thành viên</p>
            <a href="CLBchitiet.php?id=8" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/Qd7UXJ1.jpeg">
    </div>

    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge green">Âm nhạc</span>
            <h2>
                <a href="CLBchitiet.php?id=9" class="club-title-link">CLB Guitar QNU</a>
            </h2>
            <p>Nơi giao lưu, học hỏi và biểu diễn guitar cho những người đam mê âm nhạc...</p>
            <p class="member-count">👥 75 thành viên</p>
            <a href="CLBchitiet.php?id=9" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/h1K5o8Z.jpeg">
    </div>

    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge yellow">Khởi nghiệp</span>
            <h2>
                <a href="CLBchitiet.php?id=10" class="club-title-link">CLB Khởi nghiệp QNU</a>
            </h2>
            <p>Hỗ trợ sinh viên phát triển ý tưởng kinh doanh, kết nối với doanh nghiệp...</p>
            <p class="member-count">👥 110 thành viên</p>
            <a href="CLBchitiet.php?id=10" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/7xbXgze.jpeg">
    </div>

    <!-- 5 CLB BỔ SUNG - NHÓM 3 -->
    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge green">Văn học</span>
            <h2>
                <a href="CLBchitiet.php?id=11" class="club-title-link">CLB Văn học QNU</a>
            </h2>
            <p>Nơi chia sẻ đam mê văn chương, thơ ca và sáng tác văn học...</p>
            <p class="member-count">👥 65 thành viên</p>
            <a href="CLBchitiet.php?id=11" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/0MOpGXJ.jpeg">
    </div>

    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge yellow">Công nghệ</span>
            <h2>
                <a href="CLBchitiet.php?id=12" class="club-title-link">CLB Lập trình QNU</a>
            </h2>
            <p>Học lập trình, chia sẻ kiến thức và tham gia các dự án công nghệ...</p>
            <p class="member-count">👥 130 thành viên</p>
            <a href="CLBchitiet.php?id=12" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/hRTu3xV.jpeg">
    </div>

    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge green">Thể thao</span>
            <h2>
                <a href="CLBchitiet.php?id=13" class="club-title-link">CLB Cầu lông QNU</a>
            </h2>
            <p>CLB dành cho những người yêu thích cầu lông, tập luyện và thi đấu...</p>
            <p class="member-count">👥 90 thành viên</p>
            <a href="CLBchitiet.php?id=13" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/7xbXgze.jpeg">
    </div>

    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge yellow">Nghệ thuật</span>
            <h2>
                <a href="CLBchitiet.php?id=14" class="club-title-link">CLB Vẽ & Thiết kế</a>
            </h2>
            <p>Nơi sáng tạo nghệ thuật, học vẽ và thiết kế đồ họa...</p>
            <p class="member-count">👥 80 thành viên</p>
            <a href="CLBchitiet.php?id=14" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/1Qd7UXJ.jpeg">
    </div>

    <div class="club-card hidden-club">
        <div class="club-info">
            <span class="badge green">Môi trường</span>
            <h2>
                <a href="CLBchitiet.php?id=15" class="club-title-link">CLB Xanh QNU</a>
            </h2>
            <p>Bảo vệ môi trường, tổ chức các chiến dịch xanh và phát triển bền vững...</p>
            <p class="member-count">👥 100 thành viên</p>
            <a href="CLBchitiet.php?id=15" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="https://i.imgur.com/5mWv1kb.jpeg">
    </div>
</div>

<div class="xem-them-wrap">
    <button class="btn-xem-them" id="loadMoreBtn">
        Xem thêm
        <span class="arrow">▾</span>
    </button>
</div>


<div class="cta-full">
    <h2>Dễ dàng Tạo & Quản lý Câu Lạc Bộ<br>ngay trên LeaderClub</h2>
    <button class="cta-btn">
        Bắt đầu ngay →
    </button>
</div>


<script src="assets/js/DanhsachCLB.js"></script>

<?php
load_footer();
?>
