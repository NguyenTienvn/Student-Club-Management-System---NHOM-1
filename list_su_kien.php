<?php 

require_once(__DIR__ . "/assets/database/connect.php");
$club_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($club_id <= 0) {
    die("ID câu lạc bộ không hợp lệ");
}

// Lấy tên câu lạc bộ
$sql_club = "SELECT ten_clb FROM clubs WHERE id = ?";
$stmt = $conn->prepare($sql_club);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$club = $stmt->get_result()->fetch_assoc();
$ten_clb = $club['ten_clb'] ?? 'Câu lạc bộ';

// Truy vấn danh sách sự kiện - ĐÃ CẬP NHẬT ĐẦY ĐỦ CÁC TRƯỜNG
$sql = "SELECT 
            id,
            ten_su_kien,
            mo_ta,
            anh_bia,
            dia_diem,
            thoi_gian_bat_dau,
            thoi_gian_ket_thuc,
            so_luong_toi_da,
            han_dang_ky,
            trang_thai,
            created_at
        FROM events 
        WHERE club_id = ? 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();
?>  
    <link rel="stylesheet" href="assets/css/list_sukien.css?v=<?= time() ?>">

<div class="container">
    <div class="header-title">
        <h1>Danh sách sự kiện của</h1>
        <h2><?= htmlspecialchars($ten_clb) ?></h2> 
    </div>

    <div class="events-grid">
        <?php while ($event = $result->fetch_assoc()): ?>
            <div class="event-card <?= $event['trang_thai'] == 'da_ket_thuc' ? 'ended' : '' ?>">
                <?php if (!empty($event['anh_bia'])): ?>
                    <div class="event-img">
                        <img src="uploads/events/<?= htmlspecialchars($event['anh_bia']) ?>" 
                             alt="<?= htmlspecialchars($event['ten_su_kien']) ?>">
                    </div>
                <?php else: ?>
                    <div class="event-img placeholder">
                        <span>Chưa có ảnh bìa</span>
                    </div>
                <?php endif; ?>

                <div class="event-info">
                    <h3><?= htmlspecialchars($event['ten_su_kien']) ?></h3>

                    <!-- Hiển thị trạng thái -->
                    <?php if ($event['trang_thai'] == 'dang_dien_ra'): ?>
                        <span class="status ongoing">Đang diễn ra</span>
                    <?php elseif ($event['trang_thai'] == 'sap_dien_ra'): ?>
                        <span class="status upcoming">Sắp diễn ra</span>
                    <?php elseif ($event['trang_thai'] == 'da_ket_thuc'): ?>
                        <span class="status ended">Đã kết thúc</span>
                    <?php elseif ($event['trang_thai'] == 'da_huy'): ?>
                        <span class="status cancelled">Đã hủy</span>
                    <?php endif; ?>

                    <div class="event-meta">
                        <p><strong>Thời gian:</strong> 
                            <?= date('d/m/Y H:i', strtotime($event['thoi_gian_bat_dau'])) ?> 
                            <?= $event['thoi_gian_ket_thuc'] ? ' → ' . date('d/m/Y H:i', strtotime($event['thoi_gian_ket_thuc'])) : '' ?>
                        </p>
                        <p><strong>Địa điểm:</strong> <?= htmlspecialchars($event['dia_diem'] ?: 'Chưa cập nhật') ?></p>
                        <p><strong>Số lượng:</strong> <?= $event['so_luong_toi_da'] ? $event['so_luong_toi_da'] . ' người' : 'Không giới hạn' ?></p>
                        <?php if ($event['han_dang_ky']): ?>
                            <p><strong>Hạn đăng ký:</strong> <?= date('d/m/Y H:i', strtotime($event['han_dang_ky'])) ?></p>
                        <?php endif; ?>
                    </div>

                    <p class="description">
                        <?= mb_substr(htmlspecialchars($event['mo_ta']), 0, 120) ?>...
                    </p>

                    <div class="event-actions"> 
                        <a href="edit_su_kien.php?id=<?= $event['id'] ?>" class="btn-edit">Chỉnh sửa</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if ($result->num_rows == 0): ?>
        <div class="empty">
            <p>Chưa có sự kiện nào được tạo.</p>
            <a href="add_Su_kien.php?id=<?= $club_id ?>" class="btn-add-large">+ Tạo sự kiện</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
<?php $conn->close(); ?>