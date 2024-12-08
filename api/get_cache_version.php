<?php
// Sertakan file db.php untuk menggunakan koneksi
require_once 'db.php';

try {
    // Query untuk mendapatkan `versi_p` berdasarkan `kode_p`
    $kode_p = 'APP001';
    $stmt = $pdo->prepare("SELECT versi_p FROM tb_profile WHERE kode_p = :kode_p");
    $stmt->bindParam(':kode_p', $kode_p, PDO::PARAM_STR);
    $stmt->execute();

    // Ambil hasil query
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(["cache_name" => $result["versi_p"]]);
    } else {
        echo json_encode(["cache_name" => "default-cache"]); // Fallback jika data tidak ditemukan
    }
} catch (PDOException $e) {
    // Tangani kesalahan query
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
