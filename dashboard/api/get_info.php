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

try {
    // Query untuk mendapatkan total saldo
    $sql = "SELECT (SUM(debet) - SUM(kredit)) AS total_saldo FROM kas_umum";
    $result = $pdo->query($sql);


    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(["saldo" => $row["total_saldo"]]);

} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
// PDO akan menutup koneksi secara otomatis
?>