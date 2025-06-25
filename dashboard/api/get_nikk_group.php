<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
header('Content-Type: application/json');
$stmt = $pdo->prepare("SELECT nikk, MIN(nama) as kk_name FROM tb_warga WHERE nikk IS NOT NULL AND nikk != '' GROUP BY nikk");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data); 