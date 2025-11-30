<?php
$page_css = "Danhsachsukien.css";
require 'site.php';
load_top();
load_header();

// Lấy danh sách sự kiện từ database
require('assets/database/connect.php');

// Lấy tổng số sự kiện
$sql_count = "SELECT COUNT(*) as total FROM events";
$result_count = $conn->query($sql_count);
$total_events = $result_count->fetch_assoc()['total'];

// Lấy danh sách sự kiện với thông tin câu lạc bộ
$sql = "SELECT e.*, c.ten_clb, c.logo_url, c.linh_vuc
        FROM events e 
        LEFT JOIN clubs c ON e.club_id = c.id 
        ORDER BY e.thoi_gian_bat_dau DESC";
$result = $conn->query($sql);
$events = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>

<div class="container">
    <!-- TIÊU ĐỀ -->
    <h1 class="title">
        🧡 Hãy cùng khám phá<br>
        <span class="highlight">Những sự kiện hấp dẫn</span> 🧡
    </h1>

    <!-- DANH MỤC ICON -->
    <div class="categories">
        <div class="cat-item" data-category="Học thuật">
            <img src="https://cdn-icons-png.flaticon.com/512/2995/2995541.png" alt="Học thuật">
            <p>Học thuật</p>
        </div>

        <div class="cat-item" data-category="Nghệ thuật">
            <img src="https://cdn-icons-png.flaticon.com/512/4339/4339685.png" alt="Nghệ thuật">
            <p>Nghệ thuật</p>
        </div>

        <div class="cat-item" data-category="Truyền thông">
            <img src="https://cdn-icons-png.flaticon.com/512/1048/1048945.png" alt="Truyền thông">
            <p>Truyền thông</p>
        </div>

        <div class="cat-item" data-category="Thể thao">
            <img src="https://cdn-icons-png.flaticon.com/512/2964/2964514.png" alt="Thể thao">
            <p>Thể thao</p>
        </div>

        <div class="cat-item" data-category="Sở thích">
            <img src="https://cdn-icons-png.flaticon.com/512/1946/1946488.png" alt="Sở thích">
            <p>Sở thích</p>
        </div>

        <div class="cat-item" data-category="Tình nguyện">
            <img src="https://cdn-icons-png.flaticon.com/512/2950/2950736.png" alt="Tình nguyện">
            <p>Tình nguyện</p>
        </div>

        <div class="cat-item" data-category="Công nghệ">
            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828884.png" alt="Công nghệ">
            <p>Công nghệ</p>
        </div>
    </div>

    <!-- TÌM KIẾM + BỘ LỌC -->
    <div class="filters">
        <input type="text" id="searchInput" placeholder="🔍 Tìm kiếm Câu lạc bộ theo tên...">

        <select id="categoryFilter">
            <option value="">Thuộc khoa</option>
            <option value="Khoa Lý luận chính trị - Luật & Quản lý nhà nước">Khoa Lý luận chính trị - Luật & Quản lý nhà nước</option>
            <option value="Khoa Kinh tế - Tài chính">Khoa Kinh tế - Tài chính</option>
            <option value="Khoa Khoa học Xã hội & Nhân văn">Khoa Khoa học Xã hội & Nhân văn</option>
            <option value="Khoa Ngoại ngữ">Khoa Ngoại ngữ</option>
            <option value="Khoa Giáo dục Tiểu học & Mầm non">Khoa Giáo dục Tiểu học & Mầm non</option>
            <option value="Khoa Công nghệ Thông tin">Khoa Công nghệ Thông tin</option>
            <option value="Khoa Giáo dục Thể chất - Quốc phòng">Khoa Giáo dục Thể chất - Quốc phòng</option>
            <option value="Khoa Sư phạm">Khoa Sư phạm</option>
            <option value="Khoa Nghệ thuật & Công nghệ">Khoa Nghệ thuật & Công nghệ</option>
            <option value="Khoa Toán & Thống kê">Khoa Toán & Thống kê</option>
            <option value="Khoa Kinh tế & Kế toán">Khoa Kinh tế & Kế toán</option>
            <option value="Khoa Tài chính - Ngân hàng & Quản trị kinh doanh">Khoa Tài chính - Ngân hàng & Quản trị kinh doanh</option>
        </select>

        <select id="sortFilter">
            <option value="">Sắp xếp theo</option>
            <option value="date-asc">Số lượng thành viên tham gia nhiều nhất</option>
            <option value="date-desc">Số lượng thành viên tham gia ít nhất</option>
            <option value="participants-desc">Thời gian diễn ra gần nhất</option>
        </select>

        <button class="btn-filter" id="resetBtn">Bỏ lọc</button>
    </div>
</div>

<!-- DANH SÁCH SỰ KIỆN -->
<div id="event-list">
    <?php 
    $badge_colors = ['green', 'yellow', 'blue', 'red', 'purple'];
    foreach ($events as $index => $event): 
        $hidden_class = ($index >= 6) ? 'hidden-event' : '';
        $badge_color = $badge_colors[$index % count($badge_colors)];
        
        // Xử lý ảnh bìa - đường dẫn đúng từ add_Sukien_xuli.php
        $event_image = !empty($event['anh_bia']) 
            ? htmlspecialchars($event['anh_bia'])
            : 'https://via.placeholder.com/400x300?text=Event+Image';
        
        // Xử lý mô tả ngắn
        $short_desc = mb_substr($event['mo_ta'], 0, 100) . '...';
        
        // Xử lý trạng thái
        $status_class = '';
        $status_text = '';
        switch($event['trang_thai']) {
            case 'sap_dien_ra':
                $status_class = 'upcoming';
                $status_text = 'Sắp diễn ra';
                break;
            case 'dang_dien_ra':
                $status_class = 'ongoing';
                $status_text = 'Đang diễn ra';
                break;
            case 'da_ket_thuc':
                $status_class = 'ended';
                $status_text = 'Đã kết thúc';
                break;
            default:
                $status_class = 'upcoming';
                $status_text = 'Sắp diễn ra';
        }
        
        // Format ngày tháng
        $event_date = date('d', strtotime($event['thoi_gian_bat_dau']));
        $event_month = 'Tháng ' . date('m', strtotime($event['thoi_gian_bat_dau']));
    ?>
    <div class="event-card <?php echo $hidden_class; ?>" 
         data-category="<?php echo htmlspecialchars($event['ten_clb'] ?? ''); ?>"
         data-name="<?php echo htmlspecialchars($event['ten_su_kien']); ?>">
        
        <div class="event-image-wrapper">
            <img class="event-img" src="<?php echo $event_image; ?>" 
                 alt="<?php echo htmlspecialchars($event['ten_su_kien']); ?>"
                 onerror="this.src='https://via.placeholder.com/400x300?text=Event+Image'">
            <div class="event-date-badge">
                <div class="date-day"><?php echo $event_date; ?></div>
                <div class="date-month"><?php echo $event_month; ?></div>
            </div>
        </div>

        <div class="event-info">
            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
            
            <h2>
                <a href="chi_tiet_su_kien.php?id=<?php echo $event['id']; ?>" class="event-title-link">
                    <?php echo htmlspecialchars($event['ten_su_kien']); ?>
                </a>
            </h2>
            
            <div class="event-meta">
                <p class="event-club">
                    <i class="icon">🏛️</i>
                    <?php echo htmlspecialchars($event['ten_clb'] ?? 'Chưa có CLB'); ?>
                </p>
                <p class="event-location">
                    <i class="icon">📍</i>
                    <?php echo htmlspecialchars($event['dia_diem'] ?: 'Chưa cập nhật'); ?>
                </p>
            </div>

            <div class="event-footer">
                <p class="participant-count">
                    👥 Tối đa <?php echo $event['so_luong_toi_da']; ?> người
                </p>
                <a href="chi_tiet_su_kien.php?id=<?php echo $event['id']; ?>" class="btn-join">Tham gia</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- XEM THÊM -->
<div class="xem-them-wrap">
    <button class="btn-xem-them" id="loadMoreBtn">
        Xem thêm
        <span class="arrow">▾</span>
    </button>
</div>

<!-- CTA SECTION -->
<div class="cta-full">
    <h2>Tạo sự kiện của riêng bạn<br>và kết nối với cộng đồng</h2>
    <button class="cta-btn" onclick="window.location.href='add_Su_kien.php'">
        Bắt đầu ngay →
    </button>
</div>

<script src="assets/js/Danhsachsukien.js"></script>

<?php
load_footer();
?>
