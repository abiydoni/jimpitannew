<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kk_name = $_POST['kk_name'];
    $kk_alamat = $_POST['kk_alamat'];
    $kk_hp = $_POST['kk_hp'];
    $kk_foto = $_FILES['kk_foto']['name'];

    // Upload file foto
    move_uploaded_file($_FILES['kk_foto']['tmp_name'], "uploads/" . $kk_foto);

    $stmt = $pdo->prepare("INSERT INTO master_kk (kk_name, kk_alamat, kk_hp, kk_foto) VALUES (?, ?, ?, ?)");
    $stmt->execute([$kk_name, $kk_alamat, $kk_hp, $kk_foto]);

    header("Location: ../index.php"); // Redirect setelah berhasil
}
?>