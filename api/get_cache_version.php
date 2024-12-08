<?php
include 'db.php';

try {
    $kode_p = 'APP001';
    echo 'Kode p: ' . $kode_p; // debuging
    $stmt = $pdo->prepare("SELECT versi_p FROM tb_profile WHERE kode_p = :kode_p");
    $stmt->bindParam(':kode_p', $kode_p, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode(["cache_name" => $result["versi_p"]]);
    } else {
        echo json_encode(["cache_name" => "default-cache"]);
    }
} catch (PDOException $e) {
    echo 'PDOException: ' . $e->getMessage(); // Tambahkan ini untuk melihat pesan error
    http_response_code(500);
    echo json_encode(["error" => "Database error"]);
}
?>
