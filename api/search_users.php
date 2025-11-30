<?php 
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');  

require_once(__DIR__ . "/../assets/database/connect.php");

$keyword = trim($_GET['keyword'] ?? '');
$club_id = (int)($_GET['club_id'] ?? 0);

if (strlen($keyword) < 2 || $club_id < 1) {
    echo json_encode([]);
    exit;
}

// Bảo mật: dùng real_escape_string + prepared statement
$like = '%' . $conn->real_escape_string($keyword) . '%';

$stmt = $conn->prepare("
    SELECT id, ho_ten, email 
    FROM users 
    WHERE (ho_ten LIKE ? OR email LIKE ?)
      AND id NOT IN (
          SELECT user_id FROM club_members WHERE club_id = ?
      )
    ORDER BY 
          CASE WHEN email LIKE ? THEN 1 ELSE 2 END,
          ho_ten
    LIMIT 20
");
$exact = $conn->real_escape_string($keyword) . '%'; // ưu tiên email khớp đầu
$stmt->bind_param("ssis", $like, $like, $club_id, $exact);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        'id'      => (int)$row['id'],
        'ho_ten'  => $row['ho_ten'] ?: 'Chưa đặt tên',
        'email'   => $row['email'] ?? ''
    ];
}

echo json_encode($users, JSON_UNESCAPED_UNICODE);
$stmt->close();
?>