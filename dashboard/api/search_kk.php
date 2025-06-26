<?php
include '../api/db.php';
header('Content-Type: application/json');
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT DISTINCT nikk FROM tb_warga WHERE nikk LIKE ? ORDER BY nikk LIMIT 20";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$q%"]);
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = ["id" => $row['nikk'], "text" => $row['nikk']];
}
echo json_encode(["results" => $data]); 