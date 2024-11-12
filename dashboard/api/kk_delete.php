<?php
include 'db.php';

$code_id = isset($_GET['code_id']) ? intval($_GET['code_id']) : 0; // Validasi input
if ($code_id > 0) { // Pastikan code_id valid
    $stmt = $pdo->prepare("DELETE FROM master_kk WHERE code_id = ?");
    if ($stmt->execute([$code_id])) {
        echo "<script>alert('Data berhasil dihapus'); window.location.href = '../kk.php';</script>"; // Menampilkan messagebox dan redirect
        exit; // Pastikan tidak ada kode lain yang dieksekusi setelah redirect
    } else {
        // Tangani kesalahan eksekusi query
        echo "Error deleting record.";
    }
} else {
    echo "Invalid code_id.";
}
?>