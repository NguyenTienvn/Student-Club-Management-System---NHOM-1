<?php
require 'site.php';
load_top();
load_header();
?>

<div class="container-center">
    <div class="taoCLB">
        <h2>TẠO CÂU LẠC BỘ</h2>

        <form method="POST" action="taoCLB.php" enctype="multipart/form-data" onsubmit="return validateForm()">

            <img src="image/Ketnoitre.png" class="imgCLB" alt="CLB">

            <label>Logo Câu Lạc Bộ</label>
            <label class="upload-box" for="fileUpload">
                <span id="uploadText">+ Chọn ảnh</span>
                <input type="file" id="fileUpload" name="logo" accept="image/*" required style="display: none;">
            </label>


            <label>Tên câu lạc bộ</label>
            <input type="text" name="ten_clb" placeholder="Nhập tên..." required>

            <label>Email liên hệ</label>
            <input type="email" name="email" placeholder="Nhập email..." required>

            <label>Lĩnh vực hoạt động</label>
            <select name="linh_vuc" required>
                <option value="">Chọn lĩnh vực</option>
                <option value="hoc_thuat_chuyen_mon">Học thuật & Chuyên môn</option>
                <option value="nghe_thuat_sang_tao">Nghệ thuật & Sáng tạo</option>
                <option value="truyen_thong_dich_vu">Truyền thông & Dịch vụ</option>
                <option value="the_thao_suc_khoe">Thể thao & Sức khỏe</option>
                <option value="esports_cong_nghe">Thể thao điện tử & Công nghệ</option>
                <option value="so_thich_giai_tri">Sở thích & Giải trí</option>
                <option value="ngon_ngu_van_hoa">Ngôn ngữ & Văn hóa</option>
                <option value="tinh_nguyen_cong_dong">Tình nguyện & Cộng đồng</option>
            </select>
            <div class="btnRow">
                <input type="submit" class="btnCreateCLB" value="Tạo Câu Lạc Bộ">
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
// Xử lý form khi submit (nếu action là trang hiện tại; nếu không, chuyển sang taoCLB.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu
    $ten_clb = htmlspecialchars($_POST['ten_clb']);
    $email = htmlspecialchars($_POST['email']);
    $linh_vuc = htmlspecialchars($_POST['linh_vuc']);
    
    // Xử lý upload file
    $logo_path = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/'; // Thư mục lưu file (tạo thư mục này nếu chưa có)
        $file_name = basename($_FILES['logo']['name']);
        $target_path = $upload_dir . $file_name;
        
        // Kiểm tra loại file và kích thước (thêm bảo mật)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        if (in_array($_FILES['logo']['type'], $allowed_types) && $_FILES['logo']['size'] <= $max_size) {
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_path)) {
                $logo_path = $target_path;
            } else {
                echo '<p>Lỗi khi upload file.</p>';
            }
        } else {
            echo '<p>File không hợp lệ hoặc quá lớn.</p>';
        }
    }
    
    // Lưu vào database hoặc xử lý khác (giả sử bạn có kết nối DB)
    // Ví dụ: $pdo->prepare("INSERT INTO clb (ten, email, linh_vuc, logo) VALUES (?, ?, ?, ?)")->execute([$ten_clb, $email, $linh_vuc, $logo_path]);
    
    echo '<p>Câu lạc bộ đã được tạo thành công!</p>';
}
load_footer();
?> 
