<?php
$page_css = "my-clubs.css";
require 'site.php';
load_top();
load_header();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="my-clubs-container">
    <div class="page-header">
        <h1>CLB của tôi</h1>
        <p>Quản lý các Câu Lạc Bộ bạn đã tham gia</p>
    </div>

    <div class="clubs-tabs">
        <button class="tab-btn active">Đã tham gia (0)</button>
        <button class="tab-btn">Đang chờ duyệt (0)</button>
        <button class="tab-btn">Đã tạo (0)</button>
    </div>

    <div class="empty-state">
        <div class="empty-icon">🏆</div>
        <h2>Chưa tham gia CLB nào</h2>
        <p>Hãy khám phá và tham gia các Câu Lạc Bộ phù hợp với bạn!</p>
        <button class="btn-explore" onclick="location.href='DanhsachCLB.php'">
            Khám phá CLB
        </button>
    </div>
</div>

<?php
load_footer();
?>
