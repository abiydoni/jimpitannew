<?php
session_start();
include 'db.php'; // Sertakan koneksi database

// Periksa apakah pengguna sudah masuk
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}

// Ambil data dari form
$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];
$user_id = $_SESSION['user']['id']; // Misalkan ID pengguna disimpan dalam session

// Validasi input
if ($new_password !== $confirm_password) {
    echo "Password baru dan konfirmasi password tidak cocok.";
    exit;
}

// Ambil password saat ini dari database
$sql = "SELECT password FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$current_password_hash = $stmt->fetchColumn();

// Periksa apakah password lama benar
if (!password_verify($old_password, $current_password_hash)) {
    echo "Password lama salah.";
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