<?php
require 'db.php';
header('Content-Type: application/json');

$coa_code = isset($_POST['coa_code']) ? $_POST['coa_code'] : '100-002';
$date_trx = isset($_POST['date_trx']) && $_POST['date_trx'] ? $_POST['date_trx'] : date('Y-m-d');
$desc_trx = isset($_POST['desc_trx']) ? trim($_POST['desc_trx']) : '';
$reff = isset($_POST['reff']) ? $_POST['reff'] : '';
$debet = isset($_POST['debet']) ? floatval($_POST['debet']) : 0;
$kredit = isset($_POST['kredit']) ? floatval($_POST['kredit']) : 0;

if ($desc_trx === '' || ($debet <= 0 && $kredit <= 0)) {
    echo json_encode(['success' => false, 'message' => 'Keterangan harus diisi dan minimal salah satu debet/kredit > 0.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO kas_sub (coa_code, date_trx, desc_trx, reff, debet, kredit) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$coa_code, $date_trx, $desc_trx, $reff, $debet, $kredit]);
    echo json_encode(['success' => true, 'message' => 'Jurnal berhasil disimpan.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan jurnal: ' . $e->getMessage()]);
} 