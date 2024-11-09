<?php
header('Content-Type: application/json');

// Koneksi ke database
require '../helper/connection.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil data dari permintaan POST
    $data = json_decode(file_get_contents("php://input"), true);
    $kode_tarif = $data['kode_tarif'] ?? '';

    // Query untuk mengambil tarif berdasarkan kode_tarif
    $stmt = $pdo->prepare("SELECT nominal FROM tb_tarif WHERE kode_tarif = :kode_tarif");
    $stmt->bindParam(':kode_tarif', $kode_tarif);
    $stmt->execute();

    // Ambil hasil
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['nominal' => $result['nominal']]);
    } else {
        echo json_encode(['nominal' => null, 'message' => 'Tarif tidak ditemukan.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Kesalahan koneksi: ' . $e->getMessage()]);
}
?>