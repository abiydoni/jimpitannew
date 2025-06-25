<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['code_id'];
    $kk_name = $_POST['kk_name'];

    // Update data
    $stmt = $pdo->prepare("UPDATE master_kk SET kk_name = ? WHERE code_id = ?");
    $stmt->execute([$kk_name, $id]);

    echo "<script>alert('Data berhasil diubah!'); window.location.href='../kk.php';</script>";
}
?>