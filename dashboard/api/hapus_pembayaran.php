<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

$nikk = $_POST['nikk'] ?? '';
$kode_tarif = $_POST['kode_tarif'] ?? '';
$bulan = $_POST['bulan'] ?? '';
$tahun = $_POST['tahun'] ?? '';
$jml_bayar = $_POST['jml_bayar'] ?? '';
$tgl_bayar = $_POST['tgl_bayar'] ?? '';

if (empty($nikk) || empty($kode_tarif) || empty($tahun) || empty($jml_bayar) || empty($tgl_bayar)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    // Hapus pembayaran berdasarkan kombinasi field yang unik
    if (!empty($bulan)) {
        $stmt = $pdo->prepare("DELETE FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND bulan = ? AND tahun = ? AND jml_bayar = ? AND tgl_bayar = ? LIMIT 1");
        $stmt->execute([$nikk, $kode_tarif, $bulan, $tahun, $jml_bayar, $tgl_bayar]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND (bulan IS NULL OR bulan = '') AND jml_bayar = ? AND tgl_bayar = ? LIMIT 1");
        $stmt->execute([$nikk, $kode_tarif, $tahun, $jml_bayar, $tgl_bayar]);
    }
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Pembayaran berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pembayaran tidak ditemukan']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 