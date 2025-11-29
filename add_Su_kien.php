<?php
session_start();  
require 'site.php';
load_top();
load_header();
?>

<?php
// Lấy ID CLB từ query string
$club_id = $_SESSION['club_id']  ?? 0;

// Lấy tên người tạo từ session
$user_id = $_SESSION['user_id'] ?? '';
?>

<link rel="stylesheet" href="assets/css/add_sk.css">
<div class = "contain">
<div class="contain-add-sk">
    <h1>Tạo sự kiện cho Câu Lạc Bộ của bạn</h1>
  
<form action="add_Sukien_xuli.php" method="POST" enctype="multipart/form-data" class="form-sk">
    
    <div class="form-group">
        <label class="name-event">Tên sự kiện</label>
        <input type="text" name="ten_su_kien" required>
    </div>

    <div class="form-group mo-ta-sk">
        <h3 class="section-title">📝 Giới thiệu / mô tả</h3>
        <label>Mô tả sự kiện</label>
        <textarea name="mo_ta" rows="5" required></textarea>
    </div>

    <div class="form-group nd-chitiet">
        <h3 class="section-title">📌 Nội dung chi tiết</h3>
        <label>Mô tả nội dung chi tiết của sự kiện</label>
        <textarea name="noi_dung_chi_tiet" rows="6" required></textarea>
    </div>

    <div class="form-group upload-container">
        <label>Ảnh bìa tạo sự thu hút cho sự kiện</label>
        <img src="" class="anh_bia" id="preview" alt="Preview ảnh bìa">

        <label class="upload-anhbia">
            Chọn ảnh mới
            <input type="file" name="anhbia" id="anhbia" accept="image/*" style="display:none" required>
        </label>
    </div>

    <div class="form-group">
        <label>Địa điểm tổ chức</label>
        <input type="text" name="dia_diem" required>
    </div>

    <div class="two-col">
        <div class="form-group">
            <label>Thời gian bắt đầu</label>
            <input type="datetime-local" name="tg_bat_dau" required>
        </div>

        <div class="form-group">
            <label>Thời gian kết thúc</label>
            <input type="datetime-local" name="tg_ket_thuc" required>
        </div>
    </div>

    <div class="two-col">
        <div class="form-group">
            <label>Số lượng tối đa</label>
            <input type="number" name="so_luong" min="1" required>
        </div>

        <div class="form-group">
            <label>Hạn đăng ký</label>
            <input type="datetime-local" name="han_dang_ky" required>

        </div>
    </div>

      <div class="form-group"> 
        <input type="hidden" name="user_id"   value="<?= htmlspecialchars($user_id) ?>">
    </div>  

    <input type="hidden" name="club_id" value="<?= htmlspecialchars($club_id) ?>">
    <div class="button-group">
    <button type="submit" class="btn-submit"  value="<?= $club_id ?>">Tạo sự kiện</button>
    <button type="button" class="btn btn-back" 
            onclick="window.location.href='Dashboard.php?id=<?= $club_id ?>'">
        Quay lại
    </button>
    </div>
</form>
</div>
</div>

<script>
// JS preview ảnh bìa
document.getElementById('anhbia').addEventListener('change', function(e) {
    const preview = document.getElementById('preview');
    if(e.target.files && e.target.files[0]) {
        preview.src = URL.createObjectURL(e.target.files[0]);
    }
});
</script>

</body>
</html>
 