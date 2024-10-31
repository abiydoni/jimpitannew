<?php
include 'db.php';

try {
    // Query untuk menghitung jumlah data
    $sql = "SELECT COUNT(*) AS total_rows FROM master_kk";
    $stmt = $pdo->query($sql);
    
    // Mengambil hasil
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(["total" => $row["total_rows"]]);
    
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

// PDO akan menutup koneksi secara otomatis
?>