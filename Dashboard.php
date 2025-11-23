<?php
require 'site.php'; 
load_top();
load_header();
?>

<div class="dash-contain">
    <div class="dash-head">
        <h1>Dashboard</h1> 
    </div>
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
            <button onclick="location.href='duong_dan.php'" class="btn_addInfor">Bắt đầu</button>
        </div>

        <div class="box page-add">
            <h3>Tạo trang đại diện</h3>
            <p>Trang đại diện của CLB và công khai trang</p>
            <button onclick="location.href='duong_dan.php'" class="btn_addPage">Bắt đầu</button>
        </div>

        <div class="box member-add">
            <h3>Thêm thành viên</h3>
            <p>Tạo phòng ban để quản lí thông tin thành viên</p>
            <button onclick="location.href='duong_dan.php'" class="btn_addTV">Bắt đầu</button>
        </div>
    </div>

    <div class="dash-main">
        <div class="event-sect">
            <div class="event-empty">
                <h2>Sự kiện</h2>
                <div class="empty-txt">
                    <h3>Chưa có sự kiện nào</h3>
                    <p>Tạo sự kiện để thu hút các nhà tài trợ</p>
                </div> 
                <button onclick="location.href='tao_su_kien.php'" class="taosk">+Tạo sự kiện</button>
            </div>
        </div>
      
        <div class="member-list">
            <h2>Thành viên 
                <button onclick="location.href='themTV.php'" class="addTV">+</button>
            </h2>

            <div class="member-item">
            </div>
        </div>
    </div>
</div>

<?php
load_footer();
?>
