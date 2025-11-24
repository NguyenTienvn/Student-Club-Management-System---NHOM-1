<?php
$page_css = "DanhsachCLB.css";
require 'site.php';
load_top();
load_header();

// Lấy danh sách CLB từ database
require('assets/database/connect.php');
$sql = "SELECT c.*, COUNT(cm.id) as so_thanh_vien 
        FROM clubs c 
        LEFT JOIN club_members cm ON c.id = cm.club_id 
        GROUP BY c.id 
        ORDER BY c.id ASC";
$result = $conn->query($sql);
$clubs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clubs[] = $row;
    }
}
$total_clubs = count($clubs);
?>


<div class="container">

    <!-- TIÊU ĐỀ -->
    <h1 class="title">
        Khám phá <span class="highlight"><?php echo $total_clubs; ?> Câu Lạc Bộ</span> phù hợp với bạn!
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
    <?php 
    $badge_colors = ['green', 'yellow', 'blue', 'red', 'purple'];
    foreach ($clubs as $index => $club): 
        $hidden_class = ($index >= 5) ? 'hidden-club' : '';
        $badge_color = $badge_colors[$index % count($badge_colors)];
        $short_desc = mb_substr($club['mo_ta'], 0, 80) . '...';
    ?>
    <div class="club-card <?php echo $hidden_class; ?>">
        <div class="club-info">
            <span class="badge <?php echo $badge_color; ?>"><?php echo htmlspecialchars($club['linh_vuc']); ?></span>
            <h2>
                <a href="club-detail.php?id=<?php echo $club['id']; ?>" class="club-title-link">
                    <?php echo htmlspecialchars($club['ten_clb']); ?>
                </a>
            </h2>
            <p><?php echo htmlspecialchars($short_desc); ?></p>
            <p class="member-count">👥 <?php echo $club['so_thanh_vien']; ?> thành viên</p>
            <a href="club-detail.php?id=<?php echo $club['id']; ?>" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="<?php echo htmlspecialchars($club['logo_url'] ?? 'https://i.imgur.com/1Qd7UXJ.jpeg'); ?>" 
             onerror="this.src='https://i.imgur.com/1Qd7UXJ.jpeg'">
    </div>
    <?php endforeach; ?>
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
