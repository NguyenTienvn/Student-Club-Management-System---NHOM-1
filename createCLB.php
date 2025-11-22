<?php
require 'site.php';
load_top();
load_header();
?>

<div class="container-center">
    <div class="taoCLB">
        <h2>TẠO CÂU LẠC BỘ</h2>

        <form method="POST" action="createCLB_xuli.php" enctype="multipart/form-data" onsubmit="return validateForm()">

            <img src="image/Ketnoitre.png" class="imgCLB" alt="CLB">
 
    <label>Tên câu lạc bộ</label>
    <input type="text" name="ten_clb" placeholder="Nhập tên..." maxlength="150" required>

    <label>Mô tả</label>
    <textarea name="mo_ta" rows="4" placeholder="Giới thiệu về câu lạc bộ..." required></textarea>

    
    <label class="upload-box" for="fileUpload">
    <span id="uploadText">+ Chọn ảnh</span>
    <input type="file" id="fileUpload" name="logo_url" accept="image/*" required style="display: none;">
    </label>
    <label>Lĩnh vực hoạt động</label>
    <select name="linh_vuc" required>
        <option value="">Chọn lĩnh vực</option>
        <option value="Học thuật">Học thuật</option>
        <option value="Thể thao">Thể thao</option>
        <option value="Nghệ thuật">Nghệ thuật</option>
        <option value="Tình nguyện">Tình nguyện</option>
        <option value="Kỹ năng">Kỹ năng</option>
        <option value="Khác">Khác</option>
    </select>


    <label>Số thành viên</label>
    <input type="number" name="so_thanh_vien" min="1" required>
 
 
    <label>ID Chủ nhiệm</label>
    <input type="number" name="chu_nhiem_id" min="1" required>
            <div class="btnRow">
                <input type="submit" class="btnCreateCLB" value="Tạo">
                <a href="myclub.php" class="btnback">Quay lại</a>
            </div>
        </form>
    </div>
</div>

<script>
// JavaScript để xử lý upload box
document.getElementById('uploadText').addEventListener('click', function() {
    document.getElementById('fileUpload').click();
});

document.getElementById('fileUpload').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        document.getElementById('uploadText').textContent = file.name;
    } else {
        document.getElementById('uploadText').textContent = '+ Chọn ảnh';
    }
});

// Hàm validation cơ bản
function validateForm() {
    const fileInput = document.getElementById('fileUpload');
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (!allowedTypes.includes(file.type)) {
            alert('Chỉ chấp nhận file ảnh (JPEG, PNG, GIF).');
            return false;
        }
        if (file.size > maxSize) {
            alert('Kích thước file không được vượt quá 2MB.');
            return false;
        }
    }
    return true;
}
</script>

<?php
load_footer();
?> 
