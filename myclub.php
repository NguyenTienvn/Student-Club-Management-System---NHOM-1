<?php
require 'site.php';
load_top();
load_header();
?> 

<!-- Link tới file CSS riêng -->
<link rel="stylesheet" href="css/myclub.css">

<div class="container-myclub">
    <h2>Câu lạc bộ của tôi</h2>
    <div class="title">Quản lý danh sách các CLB mà bạn đã tạo hoặc tham gia</div>

    <div class="myCLB">
        <!-- <?php if ($result->num_rows > 0): ?>
            <div class="club-list">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="club-item">
                        <img src="<?= htmlspecialchars($row['logo']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="club-logo">
                        <span class="club-name"><?= htmlspecialchars($row['name']) ?></span>
                    </div>
                <?php endwhile; ?>
            </div>
            <button type="button" class="btnCreateCLB" onclick="window.location.href='createCLB.php'">Tạo câu lạc bộ mới</button>
        <?php else: ?> -->
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
