<?php
session_start();
include '../db.php';

header('Content-Type: application/json');

$id_iuran = $_POST['id_iuran'] ?? '';

// Debug log
error_log("hapus_pembayaran.php - Data received: " . json_encode($_POST));

if (empty($id_iuran)) {
    echo json_encode(['success' => false, 'message' => 'ID iuran tidak ditemukan', 'received' => $_POST]);
    exit;
}

try {
    // Debug: Tampilkan data yang akan dihapus
    $debug_info = [
        'id_iuran' => $id_iuran
    ];
    
    // Cek apakah data ada sebelum dihapus
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tb_iuran WHERE id_iuran = ?");
    $stmt_check->execute([$id_iuran]);
    $count_before = $stmt_check->fetchColumn();
    
    // Ambil data yang akan dihapus untuk debugging
    $stmt_debug = $pdo->prepare("SELECT * FROM tb_iuran WHERE id_iuran = ?");
    $stmt_debug->execute([$id_iuran]);
    $data_to_delete = $stmt_debug->fetch(PDO::FETCH_ASSOC);
    
    $debug_info['count_before'] = $count_before;
    $debug_info['data_to_delete'] = $data_to_delete;
    
    if ($count_before > 0) {
        // Validasi: Cek apakah bulan pembayaran sama dengan bulan sekarang
        $bulan_sekarang = date('n'); // 1-12
        $tahun_sekarang = date('Y');
        $bulan_pembayaran = date('n', strtotime($data_to_delete['tgl_bayar']));
        $tahun_pembayaran = date('Y', strtotime($data_to_delete['tgl_bayar']));
        
        $debug_info['bulan_sekarang'] = $bulan_sekarang;
        $debug_info['tahun_sekarang'] = $tahun_sekarang;
        $debug_info['bulan_pembayaran'] = $bulan_pembayaran;
        $debug_info['tahun_pembayaran'] = $tahun_pembayaran;
        
        // Jika bulan atau tahun tidak sama, tidak boleh dihapus
        if ($bulan_pembayaran != $bulan_sekarang || $tahun_pembayaran != $tahun_sekarang) {
            $nama_bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            
            $pesan_error = "Pembayaran tidak dapat dihapus! Pembayaran ini dilakukan pada " . 
                          $nama_bulan[$bulan_pembayaran] . " " . $tahun_pembayaran . 
                          ". Hanya pembayaran bulan " . $nama_bulan[$bulan_sekarang] . " " . $tahun_sekarang . 
                          " yang dapat dihapus.";
            
            echo json_encode([
                'success' => false, 
                'message' => $pesan_error,
                'error_type' => 'month_mismatch',
                'debug' => $debug_info
            ]);
            exit;
        }
        
        // Hapus data berdasarkan id_iuran
        $stmt = $pdo->prepare("DELETE FROM tb_iuran WHERE id_iuran = ?");
        $stmt->execute([$id_iuran]);
        
        $rows_deleted = $stmt->rowCount();
        $debug_info['rows_deleted'] = $rows_deleted;
        
        // Cek lagi setelah delete untuk memastikan data benar-benar terhapus
        $stmt_check_after = $pdo->prepare("SELECT COUNT(*) FROM tb_iuran WHERE id_iuran = ?");
        $stmt_check_after->execute([$id_iuran]);
        $count_after = $stmt_check_after->fetchColumn();
        
        $debug_info['count_after'] = $count_after;
        
        if ($rows_deleted > 0 && $count_after == 0) {
            echo json_encode(['success' => true, 'message' => 'Pembayaran berhasil dihapus', 'rows_deleted' => $rows_deleted, 'debug' => $debug_info]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Gagal menghapus pembayaran', 
                'count_before' => $count_before,
                'count_after' => $count_after,
                'rows_deleted' => $rows_deleted,
                'debug' => $debug_info
            ]);
        }
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