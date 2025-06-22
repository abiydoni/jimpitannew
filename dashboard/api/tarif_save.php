<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $kode_tarif = $_POST['kode_tarif'];
    $nama_tarif = $_POST['nama_tarif'];
    $tarif = $_POST['tarif'];

    // SQL untuk memasukkan data
    $sql = "INSERT INTO tb_tarif (kode_tarif, nama_tarif, tarif) VALUES (:kode_tarif, :nama_tarif, :tarif)";

    $stmt = $pdo->prepare($sql);

    // Eksekusi dan bind data
    $stmt->bindParam(':kode_tarif', $kode_tarif);
    $stmt->bindParam(':nama_tarif', $nama_tarif);
    $stmt->bindParam(':tarif', $tarif);

    if ($stmt->execute()) {
        header("Location: ../tarif.php"); // Mengarahkan ke jadwal.php setelah berhasil
        exit(); // Menghentikan eksekusi script setelah pengalihan
    } else {
        echo "Gagal menyimpan data.";
    }
}
?>
