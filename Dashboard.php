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
<<<<<<< Updated upstream
            <p>Tạo phòng ban để quản lí thông tin thành viên</p>
            <button onclick="location.href='duong_dan.php'" class="btn_addTV">Bắt đầu</button>
=======
            <p>Thêm thành viên cho câu lạc bộ của bạn</p>
            <button onclick="location.href='taopb.php?id=<?= $club_id ?>'" class="btn_addPage">Bắt đầu</button>

>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
            <div class="member-item">
            </div>
=======
    $sql = "
        SELECT u.ho_ten, u.avatar, u.email
        FROM club_members cm
        JOIN users u ON cm.user_id = u.id
        WHERE cm.club_id = ? 
          AND cm.trang_thai = 'dang_hoat_dong'
        ORDER BY cm.joined_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0):
    ?>
        <div class="ds_tv">
            <?php while ($member = $result->fetch_assoc()): 
                // Xử lý avatar mặc định nếu không có
                $avatar = !empty($member['avatar']) ? 'uploads/avatars/' . $member['avatar'] : 'assets/images/default-avatar.png';
            ?>
                <div class="member-item">
                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="member-avatar">
                    <div class="member-info">
                        <h4><?= htmlspecialchars($member['ho_ten']) ?></h4>
                        <p class="member-email"><?= htmlspecialchars($member['email']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    
    <div style="text-align:center; margin-top:15px;">
        <button onclick="location.href='add_TV_CLB.php?id=<?= $club_id ?>'" class="taosk">
            +
        </button>
    </div>
    
    <?php else: ?>
        <div class="empty-txt" style="text-align: center; padding: 30px; color: #888;">
            <p>Chưa có thành viên nào đang hoạt động</p>
            <button onclick="location.href='add_TV_CLB.php?id=<?= $club_id ?>'" class="taosk" style="margin-top: 10px;">
                + 
            </button>
        </div>
    <?php endif; ?>

    <?php $stmt->close(); ?>
</div>


    </div>

    <div class="task-group" style="margin-top: 30px;">
        <div class="box info-add">
            <h3>📸 Thư viện ảnh</h3>
            <p>Quản lý và upload ảnh cho CLB</p>
            <button onclick="location.href='club-gallery.php?id=<?= $club_id ?>&mode=manage'" class="btn_addInfor">Quản lý</button>
>>>>>>> Stashed changes
        </div>
    </div>
</div>

<?php
load_footer();
?>
