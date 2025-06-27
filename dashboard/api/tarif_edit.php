<?php
session_start();
// Include the database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_tarif = $_POST['kode_tarif'];
    $nama_tarif = $_POST['nama_tarif'];
    $tarif = $_POST['tarif'];
    $metode = $_POST['metode'];

    // Validasi input
    if (empty($kode_tarif) || empty($nama_tarif) || empty($tarif)) {
        session_start();
        $_SESSION['swal'] = ['msg' => 'Input tidak boleh kosong!', 'icon' => 'error'];
        header('Location: ../tarif.php');
        exit();
    }

    // Update user data in the database
    $sql = "UPDATE tb_tarif SET nama_tarif = ?, tarif = ?, metode = ? WHERE kode_tarif = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama_tarif, $tarif, $metode, $kode_tarif]);
    session_start();
    $_SESSION['swal'] = ['msg' => 'Data berhasil diperbarui!', 'icon' => 'success'];
    header('Location: ../tarif.php');
    exit();
}
?>