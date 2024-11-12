<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php'; // Sertakan koneksi database

// Periksa apakah pengguna sudah masuk
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}

// Ambil data dari form
$user_id = $_POST['id_code'] ?? null; // ID pengguna yang ingin diubah passwordnya
$new_password = $_POST['new_password'] ?? null;

// Validasi input
if (empty($new_password)) {
    echo "Password baru tidak boleh kosong.";
    exit;
}

// Hash password baru
$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

// Update password di database
$sql = "UPDATE users SET password = ? WHERE id_code = ?";
$stmt = $pdo->prepare($sql);
if (!$stmt->execute([$new_password_hash, $user_id])) {
    echo "Terjadi kesalahan saat mengubah password.";
    exit;
}

// Redirect atau tampilkan pesan sukses
echo "<script>alert('Password berhasil diubah!'); window.location.href='../jadwal.php';</script>";

exit();
?>