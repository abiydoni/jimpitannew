<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['code_id'];
    $kk_name = $_POST['kk_name'];
    $kk_alamat = $_POST['kk_alamat'];
    $kk_hp = $_POST['kk_hp'];
    $kk_foto = $_FILES['kk_foto']['name'];

    // Update data
    if ($kk_foto) {
        move_uploaded_file($_FILES['kk_foto']['tmp_name'], "images/warga/" . $kk_foto);
        $stmt = $pdo->prepare("UPDATE master_kk SET kk_name = ?, kk_alamat = ?, kk_hp = ?, kk_foto = ? WHERE code_id = ?");
        $stmt->execute([$kk_name, $kk_alamat, $kk_hp, $kk_foto, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE master_kk SET kk_name = ?, kk_alamat = ?, kk_hp = ? WHERE code_id = ?");
        $stmt->execute([$kk_name, $kk_alamat, $kk_hp, $id]);
    }

    echo "<script>alert('Data berhasil diubah!'); window.location.href='../kk.php';</script>";
}
?>