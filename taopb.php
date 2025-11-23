<?php
session_start();
include __DIR__ . '/assets/database/dbleaderclub.php'; 

// kiểm tra login: dùng session user_id hoặc id
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) {
    echo "<script>alert('Vui lòng đăng nhập'); window.location.href='login.php';</script>";
    exit;
}
$user_id = $_SESSION['user_id'] ?? $_SESSION['id'];

// lấy CLB mà user là chủ nhiệm (nếu có)
// ưu tiên lấy theo chu_nhiem_id (file createCLB_xuli nên lưu chu_nhiem_id = creator)
$stmt = $conn->prepare("SELECT id, ten_clb FROM clubs WHERE chu_nhiem_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$club = $res->fetch_assoc();
$club_id = $club['id'] ?? null;

// nếu user không có CLB, redirect tới createCLB để tạo (hoặc gán club_id tạm)
if (!$club_id) {
    // chuyển sang trang tạo CLB (nếu muốn test nhanh, có thể tạm gán club_id = 1 ở đây)
    echo "<script>alert('Bạn chưa có CLB — chuyển sang tạo CLB'); window.location.href='createCLB.php';</script>";
    exit;
}

// Lấy thông tin thành viên: ưu tiên hiển thị creator (chu_nhiem) giống mockup
$sql = "
    SELECT u.ho_ten, u.username, u.email, u.so_dien_thoai, cm.vai_tro, pb.ten_phong_ban
    FROM club_members cm
    JOIN users u ON cm.user_id = u.id
    LEFT JOIN phong_ban pb ON cm.phong_ban_id = pb.id
    WHERE cm.club_id = ?
    ORDER BY FIELD(cm.vai_tro, 'chu_nhiem','pho_chu_nhiem','truong_phong','thanh_vien'), u.ho_ten
";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $club_id);
$stmt2->execute();
$members = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// load css
?>
<link rel="stylesheet" href="assets/css/taopb.css">

<div class="header-strip">
  <div class="title">Thành viên</div>
  <div class="top-actions">
    <button class="btn-outline">Danh sách chờ</button>
    <button class="btn-outline active">Tạo phòng ban</button>
    <button class="btn-primary">+ Mời tham gia</button>
  </div>
</div>

<div class="container">
  <div class="hero-card">
    <div class="hero-left">
      <h3>Phòng ban</h3>
      <p>Quản lý danh sách thông tin thành viên theo từng phòng ban</p>
      <button class="create-btn" onclick="openModal()">Tạo phòng ban</button>
    </div>
    <div class="hero-right">
      <!-- dùng ảnh đã upload (hệ thống sẽ transform đường dẫn /mnt/data -> url) -->
      <img src="assets/images/hinh1.jpg" alt="illustration">
    </div>
  </div>

  <div class="table-card">
    <table class="members-table">
      <thead>
        <tr>
          <th>Thành viên CLB</th>
          <th>Số điện thoại</th>
          <th>Email</th>
          <th>Phòng ban</th>
          <th>Chức vụ</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($members)): ?>
          <tr><td colspan="5" class="empty-row">Chưa có thành viên nào trong CLB</td></tr>
        <?php else: ?>
          <?php foreach ($members as $m): ?>
            <tr>
              <td>
                <div class="member-info">
                  <div class="avatar"><?= !empty($m['ho_ten']) ? mb_substr($m['ho_ten'],0,1,'UTF-8') : '?' ?></div>
                  <div>
                    <div class="name"><?= htmlspecialchars($m['ho_ten']) ?></div>
                    <div class="subid"><?= htmlspecialchars($m['username']) ?></div>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($m['so_dien_thoai'] ?? '-') ?></td>
              <td><?= htmlspecialchars($m['email'] ?? '-') ?></td>
              <td><?= htmlspecialchars($m['ten_phong_ban'] ?? '-') ?></td>
              <td>
                <span class="role <?= ($m['vai_tro'] ?? '') === 'chu_nhiem' ? 'president' : '' ?>">
                  <?= ($m['vai_tro'] ?? '') === 'chu_nhiem' ? 'Chủ nhiệm' : 'Thành viên' ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal container (JS sẽ load popup_taopb.php hoặc hiển thị modal) -->
<div id="modalContainer"></div>

<script>
function openModal(){
  fetch('popup_taopb.php') // file popup nhận club_id từ session/DB
    .then(r => r.text())
    .then(html => {
      document.getElementById('modalContainer').innerHTML = html;
      const modal = document.getElementById('createDeptModal');
      if(modal) modal.classList.add('show');
    });
}
function closeModal(){
  const modal = document.getElementById('createDeptModal');
  if(modal){
    modal.classList.remove('show');
    setTimeout(()=> document.getElementById('modalContainer').innerHTML='', 220);
  }
}
window.addEventListener('click', e => {
  const modal = document.getElementById('createDeptModal');
  if(modal && e.target === modal) closeModal();
});
</script>

<script src="assets/js/taopb.js"></script>

