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

// Debug log
error_log("hapus_pembayaran.php - Data received: " . json_encode($_POST));

if (empty($nikk) || empty($kode_tarif) || empty($tahun) || empty($jml_bayar) || empty($tgl_bayar)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap', 'received' => $_POST]);
    exit;
}

try {
    // Debug: Tampilkan query yang akan dijalankan
    $debug_info = [
        'nikk' => $nikk,
        'kode_tarif' => $kode_tarif,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'jml_bayar' => $jml_bayar,
        'tgl_bayar' => $tgl_bayar
    ];
    
    // Cek apakah data ada sebelum dihapus
    if (!empty($bulan) && $bulan !== 'Tahunan') {
        // Untuk tarif bulanan
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND bulan = ? AND tahun = ? AND jml_bayar = ? AND tgl_bayar = ?");
        $stmt_check->execute([$nikk, $kode_tarif, $bulan, $tahun, $jml_bayar, $tgl_bayar]);
        $count_before = $stmt_check->fetchColumn();
        
        $stmt = $pdo->prepare("DELETE FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND bulan = ? AND tahun = ? AND jml_bayar = ? AND tgl_bayar = ? LIMIT 1");
        $stmt->execute([$nikk, $kode_tarif, $bulan, $tahun, $jml_bayar, $tgl_bayar]);
        
        $debug_info['query_type'] = 'bulanan';
        $debug_info['sql'] = "DELETE FROM tb_iuran WHERE nikk = '$nikk' AND kode_tarif = '$kode_tarif' AND bulan = '$bulan' AND tahun = '$tahun' AND jml_bayar = '$jml_bayar' AND tgl_bayar = '$tgl_bayar'";
    } else {
        // Untuk tarif tahunan (bulan = 'Tahunan' atau kosong)
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND bulan = 'Tahunan' AND jml_bayar = ? AND tgl_bayar = ?");
        $stmt_check->execute([$nikk, $kode_tarif, $tahun, $jml_bayar, $tgl_bayar]);
        $count_before = $stmt_check->fetchColumn();
        
        $stmt = $pdo->prepare("DELETE FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND bulan = 'Tahunan' AND jml_bayar = ? AND tgl_bayar = ? LIMIT 1");
        $stmt->execute([$nikk, $kode_tarif, $tahun, $jml_bayar, $tgl_bayar]);
        
        $debug_info['query_type'] = 'tahunan';
        $debug_info['sql'] = "DELETE FROM tb_iuran WHERE nikk = '$nikk' AND kode_tarif = '$kode_tarif' AND tahun = '$tahun' AND bulan = 'Tahunan' AND jml_bayar = '$jml_bayar' AND tgl_bayar = '$tgl_bayar'";
    }
    
    $rows_deleted = $stmt->rowCount();
    $debug_info['count_before'] = $count_before;
    $debug_info['rows_deleted'] = $rows_deleted;
    
    if ($rows_deleted > 0) {
        echo json_encode(['success' => true, 'message' => 'Pembayaran berhasil dihapus', 'rows_deleted' => $rows_deleted, 'debug' => $debug_info]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Pembayaran tidak ditemukan', 
            'count_before' => $count_before,
            'debug' => $debug_info
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'debug' => $debug_info ?? []]);
}
?> 