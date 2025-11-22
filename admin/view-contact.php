<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require '../assets/database/connect.php';

$id = intval($_GET['id'] ?? 0);
$contact = $conn->query("SELECT * FROM lienhe WHERE id = $id")->fetch_assoc();

if (!$contact) {
    header('Location: contacts.php');
    exit();
}

// Đánh dấu đã đọc
if ($contact['status'] == 'new') {
    $conn->query("UPDATE lienhe SET status = 'read' WHERE id = $id");
}

$page_title = "Chi tiết tin nhắn";
include 'includes/header.php';
?>

<div class="view-contact-container">
    <div class="contact-detail-card">
        <div class="contact-header">
            <h2>Chi tiết tin nhắn #<?php echo $contact['id']; ?></h2>
            <div class="contact-actions">
                <a href="contacts.php" class="btn-back">← Quay lại</a>
                <?php if ($contact['status'] != 'replied'): ?>
                <a href="contacts.php?action=replied&id=<?php echo $id; ?>" class="btn-replied">✓ Đánh dấu đã trả lời</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="contact-info-grid">
            <div class="info-item">
                <label>Họ và tên:</label>
                <p><?php echo htmlspecialchars($contact['name']); ?></p>
            </div>

            <div class="info-item">
                <label>Email:</label>
                <p><a href="mailto:<?php echo $contact['email']; ?>"><?php echo htmlspecialchars($contact['email']); ?></a></p>
            </div>

            <div class="info-item">
                <label>Tiêu đề:</label>
                <p><?php echo htmlspecialchars($contact['subject']); ?></p>
            </div>

            <div class="info-item">
                <label>Trạng thái:</label>
                <p>
                    <span class="status-badge status-<?php echo $contact['status']; ?>">
                        <?php 
                        echo $contact['status'] == 'new' ? 'Mới' : 
                            ($contact['status'] == 'read' ? 'Đã đọc' : 'Đã trả lời'); 
                        ?>
                    </span>
                </p>
            </div>

            <div class="info-item">
                <label>Thời gian gửi:</label>
                <p><?php echo date('d/m/Y H:i:s', strtotime($contact['created_at'])); ?></p>
            </div>
        </div>

        <div class="message-content">
            <label>Nội dung tin nhắn:</label>
            <div class="message-box">
                <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
            </div>
        </div>
    </div>
</div>

<style>
.view-contact-container {
    max-width: 900px;
}

.contact-detail-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.contact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e2e8f0;
}

.contact-header h2 {
    font-size: 24px;
    font-weight: 900;
    color: #2d3748;
}

.contact-actions {
    display: flex;
    gap: 10px;
}

.btn-back, .btn-replied {
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-back {
    background: #e2e8f0;
    color: #2d3748;
}

.btn-back:hover {
    background: #cbd5e0;
}

.btn-replied {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}

.btn-replied:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(72, 187, 120, 0.4);
}

.contact-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.info-item label {
    display: block;
    font-weight: 700;
    color: #718096;
    font-size: 13px;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item p {
    color: #2d3748;
    font-size: 16px;
    font-weight: 600;
}

.info-item a {
    color: #667eea;
    text-decoration: none;
}

.info-item a:hover {
    text-decoration: underline;
}

.message-content label {
    display: block;
    font-weight: 700;
    color: #718096;
    font-size: 13px;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.message-box {
    background: #f7fafc;
    padding: 25px;
    border-radius: 15px;
    border: 2px solid #e2e8f0;
    color: #2d3748;
    line-height: 1.8;
    font-size: 15px;
}
</style>

<?php include 'includes/footer.php'; ?>
