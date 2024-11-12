<?php
// Mulai sesi
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

// Cek apakah pengguna adalah admin
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Include koneksi database
include 'db.php';

// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $kode_id = $_POST['code_id']
    $kk_name = $_POST['kk_name'];
    $kk_alamat = $_POST['kk_alamat'];
    $kk_hp = $_POST['kk_hp'];
    $kk_foto = $_FILES['kk_foto'];

    // Proses upload foto jika ada
    $foto_path = '';
    if ($kk_foto['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $foto_path = $upload_dir . basename($kk_foto['name']);
        move_uploaded_file($kk_foto['tmp_name'], $foto_path);
    }

    // Siapkan dan eksekusi pernyataan SQL untuk menyimpan data
    $stmt = $pdo->prepare("INSERT INTO master_kk (code_id, kk_name, kk_alamat, kk_hp, kk_foto) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$code_id, $kk_name, $kk_alamat, $kk_hp, $foto_path]);

    // Redirect setelah berhasil
    header('Location: ../kk.php?success=Data berhasil ditambahkan');
    exit;
}
?>