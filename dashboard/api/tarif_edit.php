<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit;
}

// Check if user is admin
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php'); // Redirect to unauthorized page
    exit;
}

// Include the database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_tarif = $_POST['kode_tarif'];
    $nama_tarif = $_POST['nama_tarif'];
    $tarif = $_POST['tarif'];

    // Validasi input
    if (empty($kode_tarif) || empty($nama_tarif) || empty($tarif)) {
        // Tangani kesalahan input
        echo "<script>alert('Input tidak boleh kosong!'); window.location.href='../setting.php';</script>"; // Ganti dengan messagebox
        exit();
    }

    // Update user data in the database
    $sql = "UPDATE tb_tarif SET nama_tarif = ?, tarif = ? WHERE kode_tarif = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama_tarif, $tarif, $kode_tarif]); // Menambahkan id_code ke parameter
    // Menambahkan pengalihan setelah pesan berhasil
    echo "<script>alert('Data berhasil diperbarui!'); window.location.href='../setting.php';</script>";
    exit();
}
?>