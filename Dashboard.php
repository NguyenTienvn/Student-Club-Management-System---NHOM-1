 <?php
session_start();
require 'site.php'; 
// === 1. Kiểm tra đăng nhập ===
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Vui lòng đăng nhập để truy cập Dashboard!";
    header("Location: dangnhap.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// === 2. Lấy club_id an toàn (ưu tiên GET, sau đó session) ===
$club_id = 0;

if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
    $club_id = (int)$_GET['id'];
    $_SESSION['club_id'] = $club_id; // đồng bộ session
} 
elseif (isset($_SESSION['club_id']) && $_SESSION['club_id'] > 0) {
    $club_id = (int)$_SESSION['club_id'];
}

// === 3. Nếu vẫn không có club_id → chuyển hướng ===
if ($club_id <= 0) {
    $_SESSION['error'] = "Không tìm thấy câu lạc bộ. Vui lòng chọn CLB từ danh sách.";
    header("Location: myclub.php");
    exit;
}
load_top();
load_header();
?>

<link rel="stylesheet" href="assets/css/Dashboard.css">
<div class="dash-contain">
    <div class="dash-head">
    <h1 class="dashboard-title">
        <span id="back-to-myclub" class="back-arrow">←</span> Dashboard
    </h1>

<script>
    document.getElementById("back-to-myclub").addEventListener("click", function () {
        window.location.href = "myclub.php";
    });
</script>    </div>
    <div class="dash-intro">
        <h2 class="title-main">👋Chào mừng đến trang Quản lý Câu Lạc Bộ</h2>
        <p class="title-sub">Đây là nơi để bạn quản lý thông tin cho CLB của bạn hoặc các CLB mà bạn đã tham gia</p>
        <p class="title-sub">Đối với CLB mới, bạn cần hoàn thiện một số thông tin ở trang Dashboard để CLB có thể đi vào hoạt động</p>
    </div>

    <div class="warn-box"> 
        <div class="alert-txt"> 
            <p><span>⚠️</span>Hoàn thiện các bước dưới đây để Câu Lạc Bộ của bạn đi vào hoạt động</p>
        </div>
    </div>

    <div class="task-group">
        <div class="box info-add">
            <h3>Bổ sung thông tin</h3>
            <p>Thông tin cơ bản của Câu Lạc Bộ</p>
            <button onclick="location.href='edit_inf_CLB.php?id=<?= $club_id ?>'" class="btn_addInfor">Bắt đầu</button>
         </div>

        <div class="box page-add">
            <h3>Tạo trang đại diện</h3>
            <p>Trang đại diện của CLB và công khai trang</p>
            <button onclick="location.href='tao_trang_dai_dien.php?id=<?= $club_id ?>'" class="btn_addPage">Bắt đầu</button>
            <button onclick="location.href='club-detail.php?id=<?= $club_id ?>'" class="btn_addPage" style="margin-top: 10px; background: rgba(255,255,255,0.7);">Xem trang</button>
        </div>

        <div class="box member-add">
            <h3>Thêm thành viên</h3>
            <p>Tạo phòng ban để quản lí thông tin thành viên</p>
            <button onclick="location.href='add_TV_CLB.php?id=<?= $club_id ?>'" class="btn_addPage">Bắt đầu</button>

        </div>
    </div>

    <div class="dash-main">
        <div class="event-sect">
            <div class="event-empty">
                <h2>Sự kiện</h2>
                <div class="empty-txt"> 
                    <p>Tạo sự kiện để thu hút các nhà tài trợ</p>
                </div> 
                <button onclick="location.href='add_Su_kien.php?id=<?= $club_id ?>'" class="taosk">+Tạo sự kiện</button>
            </div>
        </div>
      
        <div class="member-list">
             <h2>Thành viên</h2>
             <div class = "ds_tv"></div>
                <!--<button onclick="location.href='themTV.php'" class="addTV">+</button> -->
            <div class="member-item">
            </div>
        </div>
    </div>

    <div class="task-group" style="margin-top: 30px;">
        <div class="box info-add">
            <h3>📸 Thư viện ảnh</h3>
            <p>Quản lý và upload ảnh cho CLB</p>
            <button onclick="location.href='club-gallery.php?id=<?= $club_id ?>&mode=manage'" class="btn_addInfor">Quản lý</button>
        </div>
    </div>
</div>
 
<?php
load_footer();
?>