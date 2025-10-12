<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $kode_tarif = $_POST['kode_tarif'];
    $nama_tarif = $_POST['nama_tarif'];
    $tarif = $_POST['tarif'];
    $metode = $_POST['metode'];
    $icon = $_POST['icon'];

    // Validasi input
    if (empty($kode_tarif) || empty($nama_tarif) || empty($tarif) || empty($icon)) {
        $_SESSION['swal'] = ['msg' => 'Input tidak boleh kosong!', 'icon' => 'error'];
        header('Location: ../tarif.php');
        exit();
    }

    // SQL untuk memasukkan data
    $sql = "INSERT INTO tb_tarif (kode_tarif, nama_tarif, tarif, metode, icon) VALUES (:kode_tarif, :nama_tarif, :tarif, :metode, :icon)";

    $stmt = $pdo->prepare($sql);

    // Eksekusi dan bind data
    $stmt->bindParam(':kode_tarif', $kode_tarif);
    $stmt->bindParam(':nama_tarif', $nama_tarif);
    $stmt->bindParam(':tarif', $tarif);
    $stmt->bindParam(':metode', $metode);
    $stmt->bindParam(':icon', $icon);

    if ($stmt->execute()) {
        $_SESSION['swal'] = ['msg' => 'Data berhasil disimpan!', 'icon' => 'success'];
        header("Location: ../tarif.php"); // Mengarahkan ke tarif.php setelah berhasil
        exit(); // Menghentikan eksekusi script setelah pengalihan
    } else {
        $_SESSION['swal'] = ['msg' => 'Gagal menyimpan data!', 'icon' => 'error'];
        header("Location: ../tarif.php");
        exit();
    }
}
?>
