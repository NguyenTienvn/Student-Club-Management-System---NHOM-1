<div id="club-list">
    <?php 
    $badge_colors = ['green', 'yellow', 'blue', 'red', 'purple'];
    foreach ($clubs as $index => $club): 
        $hidden_class = ($index >= 5) ? 'hidden-club' : '';
        $badge_color = $badge_colors[$index % count($badge_colors)];
        $short_desc = mb_substr($club['mo_ta'], 0, 80) . '...';
    ?>
    <div class="club-card <?php echo $hidden_class; ?>">
        <div class="club-info">
            <span class="badge <?php echo $badge_color; ?>"><?php echo htmlspecialchars($club['linh_vuc']); ?></span>
            <h2>
                <a href="club-detail.php?id=<?php echo $club['id']; ?>" class="club-title-link">
                    <?php echo htmlspecialchars($club['ten_clb']); ?>
                </a>
            </h2>
            <p><?php echo htmlspecialchars($short_desc); ?></p>
            <p class="member-count">👥 <?php echo $club['so_thanh_vien']; ?> thành viên</p>
            <a href="club-detail.php?id=<?php echo $club['id']; ?>" class="btn-detail">Chi tiết</a>
        </div>
        <img class="club-img" src="<?php echo htmlspecialchars($club['logo_url'] ?? 'https://i.imgur.com/1Qd7UXJ.jpeg'); ?>" 
             onerror="this.src='https://i.imgur.com/1Qd7UXJ.jpeg'">
    </div>
    <?php endforeach; ?>
</div>
