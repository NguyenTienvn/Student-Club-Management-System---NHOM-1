<?php
require 'site.php';
load_top();
load_header();

// Kết nối database
include(__DIR__ . "/assets/database/dbleaderclub.php");

// Giả sử user_id có trong session
$user_id = $_SESSION['user_id'] ?? 0;

// Lấy danh sách CLB mà user là chủ nhiệm
$sql = "SELECT id, ten_clb, logo_url 
        FROM clubs
        WHERE chu_nhiem_id = $user_id";

$result = $conn->query($sql);
?>

<div class="container-myclub">
    <h2>Câu lạc bộ của tôi</h2>
    <div class="title">Quản lý danh sách các CLB mà bạn đã tạo hoặc tham gia</div>

    <div class="myCLB">

        <?php if ($result && $result->num_rows > 0): ?>

            <div class="club-list">

                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="club-item">

                        <img src="<?= htmlspecialchars($row['logo_url']) ?>" 
                             alt="<?= htmlspecialchars($row['ten_clb']) ?>" 
                             class="club-logo">

                        <div class="club-name"><?= htmlspecialchars($row['ten_clb']) ?></div>

                        <a href="manageCLB.php?id=<?= $row['id'] ?>" 
                           class="btnCreateCLB" 
                           style="margin-top:10px; width:100%; text-align:center;">
                            Quản lý
                        </a>

                    </div>
                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="create">  
                <img src="image/Ketnoitre.png" alt="Image">
                <p class="lb1">Có vẻ như bạn chưa có hoặc chưa tham gia CLB nào.</p>
                <p class="lb2">Tạo CLB mới hoặc tham gia một CLB bất kỳ tại đây.</p>
                <a href="createCLB.php" class="btnCreateCLB">Tạo câu lạc bộ</a>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php
load_footer();
?>
