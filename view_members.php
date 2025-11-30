<?php
session_start();
require 'site.php'; 
load_top();
load_header();
require_once(__DIR__ . "/assets/database/connect.php");

// Lấy ID từ GET hoặc SESSION (ưu tiên GET)
if (isset($_GET['id']) && is_numeric($_GET['id']) && (int)$_GET['id'] > 0) {
    $club_id = (int)$_GET['id'];
} elseif (isset($_SESSION['club_id']) && is_numeric($_SESSION['club_id']) && (int)$_SESSION['club_id'] > 0) {
    $club_id = (int)$_SESSION['club_id'];
} else {
    die("ID câu lạc bộ không hợp lệ");
}

$sql = "
    SELECT 
        cm.id AS club_member_id,
        u.id AS user_id,
        u.ho_ten,
        u.avatar,
        u.email,
        cm.vai_tro AS vai_tro_clb,
        cm.trang_thai
    FROM club_members cm
    INNER JOIN users u ON cm.user_id = u.id
    WHERE cm.club_id = ?
    ORDER BY cm.vai_tro = 'chu_nhiem' DESC, cm.trang_thai, u.ho_ten
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="assets/css/view_member.css">

<h1>Danh sách thành viên câu lạc bộ của bạn</h1>
<div class="members-grid">

<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($member = $result->fetch_assoc()): 
        $club_member_id = $member['club_member_id'];
        $ho_ten         = htmlspecialchars($member['ho_ten']);
        $avatar         = $member['avatar']; 
        $email          = htmlspecialchars($member['email'] ?? 'Chưa cung cấp');

        // Vai trò
        $vai_tro = $member['vai_tro_clb'];
        $vai_tro_hien_thi = match($vai_tro) {
            'chu_nhiem'     => 'Chủ nhiệm CLB',
            'pho_chu_nhiem' => 'Phó chủ nhiệm',
            default         => 'Thành viên'
        };

        // Trạng thái
        $trang_thai = $member['trang_thai'];
        $trang_thai_text = match($trang_thai) {
            'da_duyet'  => '<span class="status approved">Đã duyệt</span>',
            'cho_duyet' => '<span class="status pending">Chờ duyệt</span>',
            'tu_choi'   => '<span class="status rejected">Bị từ chối</span>',
             default     => '<span class="status pending">Chờ duyệt</span>'
        };

        // Xử lý avatar
        $avatar_path_disk = $_SERVER['DOCUMENT_ROOT'] . "/Student-Club-Management-System---NHOM-1/" . $avatar;
        $avatar_path_web  = "/Student-Club-Management-System---NHOM-1/" . $avatar;

        if (!empty($avatar) && file_exists($avatar_path_disk)) {
            $anh_avatar = $avatar_path_web;
        } else {
            $anh_avatar = "/Student-Club-Management-System---NHOM-1/assets/img/avatars/user.svg";
        }
?>

<div class="member-card">
    <img src="<?= $anh_avatar ?>" class="member-avatar">

    <div class="member-info">
        <h4><?= $ho_ten ?></h4>
        <span class="member-email"><?= $email ?></span>              
        <span class="member-role"><?= $vai_tro_hien_thi ?></span>
        <span class="member-status"><?= $trang_thai_text ?></span>
    </div>

    <button class="delete-btn" onclick="deleteMember(<?= $club_member_id ?>)">Xóa</button>
</div>

<?php endwhile; ?>
<?php else: ?>
<p style="grid-column:1/-1; text-align:center; padding:40px; color:#666;">
    Chưa có thành viên nào trong câu lạc bộ này
</p>
<?php endif; ?>

</div>

<?php $stmt->close(); ?>


<script>
function deleteMember(club_member_id) {
    if (!confirm('Bạn có chắc chắn muốn xóa thành viên này?')) return;

    fetch('api/delete_member.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'club_member_id=' + club_member_id
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi kết nối server!');
    });
}
</script>