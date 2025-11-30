<?php
require_once('assets/database/connect.php');

$keyword = $_GET['keyword'] ?? '';

$stmt = $conn->prepare("SELECT id, ho_ten, email FROM users WHERE ho_ten LIKE ? OR email LIKE ? LIMIT 10");
$kw = "%$keyword%";
$stmt->bind_param("ss", $kw, $kw);
$stmt->execute();
$result = $stmt->get_result();

$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}

echo json_encode($list);
?>