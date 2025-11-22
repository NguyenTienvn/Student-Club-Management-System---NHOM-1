<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require '../assets/database/connect.php';

// Thống kê users theo tháng
$users_by_month = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
    FROM users 
    GROUP BY month 
    ORDER BY month DESC 
    LIMIT 12
");

// Thống kê CLB theo lĩnh vực
$clubs_by_field = $conn->query("
    SELECT linh_vuc, COUNT(*) as count 
    FROM clubs 
    GROUP BY linh_vuc 
    ORDER BY count DESC
");

// Thống kê tin nhắn theo trạng thái
$messages_stats = [
    'new' => $conn->query("SELECT COUNT(*) as c FROM lienhe WHERE status='new'")->fetch_assoc()['c'],
    'read' => $conn->query("SELECT COUNT(*) as c FROM lienhe WHERE status='read'")->fetch_assoc()['c'],
    'replied' => $conn->query("SELECT COUNT(*) as c FROM lienhe WHERE status='replied'")->fetch_assoc()['c']
];

// Top 5 CLB có nhiều thành viên nhất
$top_clubs = $conn->query("
    SELECT ten_clb, so_thanh_vien 
    FROM clubs 
    ORDER BY so_thanh_vien DESC 
    LIMIT 5
");

$page_title = "Thống kê";
include 'includes/header.php';
?>

<div class="statistics-container">
    <!-- Charts Grid -->
    <div class="charts-grid">
        <!-- Users Growth -->
        <div class="chart-card">
            <h3>📈 Tăng trưởng người dùng</h3>
            <div class="chart-content">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $users_by_month->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('m/Y', strtotime($row['month'] . '-01')); ?></td>
                            <td><strong><?php echo $row['count']; ?></strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Clubs by Field -->
        <div class="chart-card">
            <h3>🎯 CLB theo lĩnh vực</h3>
            <div class="chart-content">
                <?php while ($row = $clubs_by_field->fetch_assoc()): ?>
                <div class="progress-item">
                    <div class="progress-label">
                        <span><?php echo $row['linh_vuc'] ?? 'Chưa phân loại'; ?></span>
                        <strong><?php echo $row['count']; ?></strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo ($row['count'] * 20); ?>%"></div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Messages Status -->
        <div class="chart-card">
            <h3>✉️ Trạng thái tin nhắn</h3>
            <div class="chart-content">
                <div class="pie-stats">
                    <div class="pie-item">
                        <div class="pie-circle new"><?php echo $messages_stats['new']; ?></div>
                        <p>Tin mới</p>
                    </div>
                    <div class="pie-item">
                        <div class="pie-circle read"><?php echo $messages_stats['read']; ?></div>
                        <p>Đã đọc</p>
                    </div>
                    <div class="pie-item">
                        <div class="pie-circle replied"><?php echo $messages_stats['replied']; ?></div>
                        <p>Đã trả lời</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Clubs -->
        <div class="chart-card">
            <h3>🏆 Top 5 CLB nhiều thành viên</h3>
            <div class="chart-content">
                <div class="ranking-list">
                    <?php 
                    $rank = 1;
                    while ($row = $top_clubs->fetch_assoc()): 
                    ?>
                    <div class="ranking-item">
                        <div class="rank-badge">#<?php echo $rank++; ?></div>
                        <div class="rank-info">
                            <h4><?php echo htmlspecialchars($row['ten_clb']); ?></h4>
                            <p><?php echo $row['so_thanh_vien'] ?? 0; ?> thành viên</p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
}

.chart-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.chart-card h3 {
    font-size: 20px;
    font-weight: 800;
    color: #2d3748;
    margin-bottom: 25px;
}

.stats-table {
    width: 100%;
    border-collapse: collapse;
}

.stats-table th {
    text-align: left;
    padding: 12px;
    background: #f7fafc;
    font-weight: 700;
    color: #2d3748;
    font-size: 13px;
}

.stats-table td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
    color: #4a5568;
}

.progress-item {
    margin-bottom: 20px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 14px;
}

.progress-bar {
    height: 10px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 10px;
    transition: width 0.5s ease;
}

.pie-stats {
    display: flex;
    justify-content: space-around;
    gap: 20px;
}

.pie-item {
    text-align: center;
}

.pie-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 900;
    color: white;
    margin: 0 auto 10px;
}

.pie-circle.new {
    background: linear-gradient(135deg, #fc8181, #f56565);
}

.pie-circle.read {
    background: linear-gradient(135deg, #63b3ed, #4299e1);
}

.pie-circle.replied {
    background: linear-gradient(135deg, #68d391, #48bb78);
}

.pie-item p {
    font-size: 14px;
    color: #4a5568;
    font-weight: 600;
}

.ranking-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.ranking-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f7fafc;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.ranking-item:hover {
    background: #edf2f7;
    transform: translateX(5px);
}

.rank-badge {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 16px;
}

.rank-info h4 {
    font-size: 15px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 5px;
}

.rank-info p {
    font-size: 13px;
    color: #718096;
}

@media (max-width: 1024px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
