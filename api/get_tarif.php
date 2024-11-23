<?php
header('Content-Type: application/json');

// Koneksi ke database
$host = 'localhost'; // Ganti dengan host database Anda
$dbname = 'appsbeem_jimpitan'; // Ganti dengan nama database Anda
$username = 'appsbeem_admin'; // Ganti dengan username database Anda
$password = 'A7by777__'; // Ganti dengan password database Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil kode_tarif dari query string
    $kode_tarif = isset($_GET['kode_tarif']) ? $_GET['kode_tarif'] : '';

    // Query untuk mengambil tarif
    $stmt = $pdo->prepare("SELECT tarif FROM tb_tarif WHERE kode_tarif = :kode_tarif");
    $stmt->bindParam(':kode_tarif', $kode_tarif);
    $stmt->execute();

    // Ambil hasil
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['success' => true, 'tarif' => $result['tarif']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tarif tidak ditemukan']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Kesalahan koneksi: ' . $e->getMessage()]);
}
?>