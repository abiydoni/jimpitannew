<?php
session_start(); // Memulai sesi

// Hapus semua variabel sesi
$_SESSION = [];

// Hancurkan sesi
session_destroy();

// Redirect ke halaman login atau homepage
header("Location: ../login.php");
exit();
?>
