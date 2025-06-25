<?php
include 'db.php';
header('Content-Type: application/json');
$stmt = $pdo->prepare("SELECT nikk, nama as kk_name, MIN(nokk) as nokk FROM tb_warga WHERE hubungan='Kepala Keluarga' GROUP BY nikk");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data); 