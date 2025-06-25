<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['code_id'];
    $kk_name = $_POST['kk_name'];
    $nokk = $_POST['nokk'];

    // Update data
    $stmt = $pdo->prepare("UPDATE master_kk SET kk_name = ?, nokk = ? WHERE code_id = ?");
    $stmt->execute([$kk_name, $nokk, $id]);

    echo "<script>alert('Data berhasil diubah!'); window.location.href='../kk.php';</script>";
}
?>