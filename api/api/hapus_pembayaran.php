<?php
session_start();
include 'db.php';

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