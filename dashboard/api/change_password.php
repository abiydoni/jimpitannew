<?php
session_start();
include 'db.php'; // Sertakan koneksi database

// Periksa apakah pengguna sudah masuk
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}

// Ambil data dari form
$user_id = $_POST['id_code']; // ID pengguna yang ingin diubah passwordnya
$new_password = $_POST['new_password'];

// Validasi input
if (empty($new_password)) {
    echo "Password baru tidak boleh kosong.";
    exit;
}

// Hash password baru
$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

// Update password di database
$sql = "UPDATE users SET password = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$new_password_hash, $user_id]);

// Redirect atau tampilkan pesan sukses
header('Location: ../jadwal.php?message=Password berhasil diubah');
exit();
?>