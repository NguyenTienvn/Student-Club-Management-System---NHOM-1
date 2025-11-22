<?php
session_start();  

require 'site.php';
load_top();
load_header();

// Kết nối database
include(__DIR__ . "/assets/database/dbleaderclub.php");

if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập!");
}
// Giả sử user_id có trong session
$user_id = $_SESSION['user_id'];

// Lấy danh sách CLB mà user là chủ nhiệm
$sql = "SELECT id, ten_clb, logo_url, linh_vuc 
        FROM clubs
        WHERE chu_nhiem_id = $user_id";

$result = $conn->query($sql);
?>
 <div class="container-myclub">
    <h2>Câu lạc bộ của tôi</h2>
    <div class="title">Quản lý danh sách các CLB mà bạn đã tạo hoặc tham gia</div>

    <?php if ($result && $result->num_rows > 0): ?>

        <!-- KHỐI TẠO CLB (Nằm riêng) -->
        <div class="create1">
            <img src="image/Ketnoitre.png" alt="Image" class="create-img"> 
            <p class="lb2">Tạo CLB mới hoặc tham gia một CLB bất kỳ tại đây.</p>
            <a href="createCLB.php" class="btnCreateCLB">Tạo câu lạc bộ</a>
        </div>

        <!-- KHỐI DANH SÁCH CLB (Nằm riêng) -->
        <div class="myCLB">
            <div class="club-list">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="club-item">
                        <img src="<?= htmlspecialchars($row['logo_url']) ?>" 
                             alt="<?= htmlspecialchars($row['ten_clb']) ?>" 
                             class="club-logo">

                        <div class="club-info">
                            <span class="club-tag"><?= htmlspecialchars($row['linh_vuc']) ?></span>
                            <h3 class="club-name"><?= htmlspecialchars($row['ten_clb']) ?></h3>
                            <a href="manageCLB.php?id=<?= $row['id'] ?>" class="btn-manage">Quản lý</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

    <?php else: ?>

        <!-- TRƯỜNG HỢP CHƯA CÓ CLB -->
        <div class="create2">  
            <img src="image/Ketnoitre.png" alt="Image">
            <p class="lb1">Có vẻ như bạn chưa có hoặc chưa tham gia CLB nào.</p>
            <p class="lb2">Tạo CLB mới hoặc tham gia một CLB bất kỳ tại đây.</p>
            <a href="createCLB.php" class="btnCreateCLB">Tạo câu lạc bộ</a>
        </div>

    <?php endif; ?>
</div>


<?php
load_footer();
?>
